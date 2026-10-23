<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use ZipArchive;

class BackupService
{
    public function directory(): string
    {
        $directory = storage_path('app/backups');

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('پوشه بکاپ ساخته نشد.');
        }

        return $directory;
    }

    public function create(): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('افزونه Zip در PHP فعال نیست.');
        }

        set_time_limit(0);

        $stamp = now()->format('Y-m-d_His');
        $directory = $this->directory();
        $sqlPath = $directory.DIRECTORY_SEPARATOR.'database_'.$stamp.'.sql';
        $zipName = 'shura-backup-'.$stamp.'.zip';
        $zipPath = $directory.DIRECTORY_SEPARATOR.$zipName;

        $handle = fopen($sqlPath, 'wb');
        if ($handle === false) {
            throw new RuntimeException('فایل بکاپ ساخته نشد.');
        }

        try {
            $this->writeDatabase($handle);
        } catch (\Throwable $exception) {
            fclose($handle);
            @unlink($sqlPath);
            throw $exception;
        }

        fclose($handle);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($sqlPath);
            throw new RuntimeException('فایل فشرده بکاپ ساخته نشد.');
        }

        $zip->addFile($sqlPath, 'database.sql');
        $this->addDirectory($zip, public_path('upload'), 'upload');

        if ($zip->close() !== true) {
            @unlink($sqlPath);
            @unlink($zipPath);
            throw new RuntimeException('بستن فایل بکاپ ناموفق بود.');
        }

        @unlink($sqlPath);

        return $zipName;
    }

    /**
     * @return list<array{name: string, size: int, created_at: int}>
     */
    public function list(): array
    {
        $files = glob($this->directory().DIRECTORY_SEPARATOR.'shura-backup-*.zip') ?: [];
        $backups = [];

        foreach ($files as $path) {
            if (! is_file($path)) {
                continue;
            }

            $backups[] = [
                'name' => basename($path),
                'size' => (int) filesize($path),
                'created_at' => (int) filemtime($path),
            ];
        }

        usort($backups, fn (array $a, array $b) => $b['created_at'] <=> $a['created_at']);

        return $backups;
    }

    public function path(string $filename): string
    {
        if (! preg_match('/^shura-backup-\d{4}-\d{2}-\d{2}_\d{6}\.zip$/', $filename)) {
            abort(404);
        }

        $path = $this->directory().DIRECTORY_SEPARATOR.$filename;

        if (! is_file($path)) {
            abort(404);
        }

        return $path;
    }

    public function delete(string $filename): void
    {
        $path = $this->path($filename);

        if (! unlink($path)) {
            throw new RuntimeException('حذف بکاپ ناموفق بود.');
        }
    }

    /**
     * @param  resource  $handle
     */
    private function writeDatabase($handle): void
    {
        $driver = DB::connection()->getDriverName();

        fwrite($handle, "-- Shura system backup\n");
        fwrite($handle, '-- Created: '.now()->toDateTimeString()."\n");
        fwrite($handle, '-- Driver: '.$driver."\n\n");

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $this->dumpMysql($handle);

            return;
        }

        if ($driver === 'sqlite') {
            $this->dumpSqlite($handle);

            return;
        }

        throw new RuntimeException('این نوع پایگاه داده برای بکاپ پشتیبانی نمی‌شود.');
    }

    /**
     * @param  resource  $handle
     */
    private function dumpMysql($handle): void
    {
        $pdo = DB::connection()->getPdo();

        fwrite($handle, "SET NAMES utf8mb4;\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");

        $tables = DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");

        foreach ($tables as $tableRow) {
            $table = $this->firstColumn($tableRow);
            $quoted = $this->ident($table);
            $create = DB::select('SHOW CREATE TABLE '.$quoted);
            $createSql = (string) ($create[0]->{'Create Table'} ?? '');

            fwrite($handle, 'DROP TABLE IF EXISTS '.$quoted.";\n");
            fwrite($handle, $createSql.";\n\n");
            $this->dumpRows($handle, $pdo, $table);
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
    }

    /**
     * @param  resource  $handle
     */
    private function dumpSqlite($handle): void
    {
        $pdo = DB::connection()->getPdo();

        fwrite($handle, "PRAGMA foreign_keys=OFF;\nBEGIN TRANSACTION;\n\n");

        $tables = DB::select("SELECT name, sql FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name");

        foreach ($tables as $table) {
            $quoted = $this->ident((string) $table->name);
            fwrite($handle, 'DROP TABLE IF EXISTS '.$quoted.";\n");
            fwrite($handle, $table->sql.";\n\n");
            $this->dumpRows($handle, $pdo, (string) $table->name);
        }

        fwrite($handle, "COMMIT;\n");
    }

    /**
     * @param  resource  $handle
     */
    private function dumpRows($handle, \PDO $pdo, string $table): void
    {
        $quoted = $this->ident($table);
        $statement = $pdo->query('SELECT * FROM '.$quoted);

        if ($statement === false) {
            throw new RuntimeException('خواندن جدول '.$table.' ناموفق بود.');
        }

        $columns = null;
        $batch = [];

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            if ($columns === null) {
                $columns = array_map(fn (string $column) => $this->ident($column), array_keys($row));
            }

            $values = array_map(fn ($value) => $this->sqlValue($pdo, $value), array_values($row));
            $batch[] = '('.implode(',', $values).')';

            if (count($batch) >= 100) {
                fwrite($handle, 'INSERT INTO '.$quoted.' ('.implode(',', $columns).') VALUES '.implode(",\n", $batch).";\n");
                $batch = [];
            }
        }

        $statement->closeCursor();

        if ($batch !== [] && $columns !== null) {
            fwrite($handle, 'INSERT INTO '.$quoted.' ('.implode(',', $columns).') VALUES '.implode(",\n", $batch).";\n");
        }

        fwrite($handle, "\n");
    }

    private function addDirectory(ZipArchive $zip, string $directory, string $prefix): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $relative = $prefix.'/'.str_replace('\\', '/', substr($file->getPathname(), strlen($directory) + 1));

            if ($file->isDir()) {
                $zip->addEmptyDir($relative);
                continue;
            }

            $zip->addFile($file->getPathname(), $relative);
        }
    }

    private function firstColumn(object $row): string
    {
        $values = array_values((array) $row);

        return (string) ($values[0] ?? '');
    }

    private function ident(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }

    private function sqlValue(\PDO $pdo, mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value)) {
            return (string) $value;
        }

        if (is_float($value)) {
            return is_finite($value) ? (string) $value : 'NULL';
        }

        $string = (string) $value;

        if (str_contains($string, "\0")) {
            return '0x'.bin2hex($string);
        }

        return $pdo->quote($string);
    }
}
