<?php

namespace App\Support;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

/**
 * Turns raw activity-log rows into text an admin can read:
 * a Persian sentence, the section, and the list of changed fields (old → new),
 * with ids replaced by member / account names.
 */
class ActivityLogPresenter
{
    /** Model class basename => [section name, singular noun]. */
    public const SECTIONS = [
        'User' => ['اعضا و کاربران', 'عضو'],
        'FamilyMembers' => ['اعضای خانواده', 'عضو خانواده'],
        'KeyPerson' => ['اشخاص کلیدی', 'شخص کلیدی'],
        'EthnicBranch' => ['شاخه‌های قومی', 'شاخه قومی'],
        'Representative' => ['نماینده‌ها', 'نماینده'],
        'Category' => ['حساب‌ها و بخش‌ها', 'حساب'],
        'Income' => ['درآمدها', 'درآمد'],
        'Undeposited' => ['درآمد واریزنشده', 'واریز درآمد'],
        'Expense' => ['مصارف', 'مصرف'],
        'ReceivePayment' => ['دریافت فیس و قرض', 'دریافت فیس/قسط'],
        'Aids' => ['کمک‌ها', 'کمک مالی'],
        'Credit' => ['قرض‌ها (کریدیت)', 'قرض'],
        'CreditPayment' => ['قسط‌های قرض', 'قسط قرض'],
        'AccountTransaction' => ['انتقال و دریافت پول', 'تراکنش حساب'],
        'MemberFinancialReport' => ['گزارش مالی اعضا', 'گزارش مالی عضو'],
    ];

    public const EVENTS = [
        'created' => ['ثبت کرد', 'ثبت جدید', 'bg-success'],
        'updated' => ['ویرایش کرد', 'ویرایش', 'bg-warning text-dark'],
        'deleted' => ['حذف کرد', 'حذف', 'bg-danger'],
    ];

    /** Field labels (Persian). */
    protected const FIELDS = [
        'name' => 'نام',
        'father_name' => 'نام پدر',
        'grandfather_name' => 'نام پدرکلان',
        'lastname' => 'تخلص',
        'gender' => 'جنسیت',
        'national_id' => 'نمبر تذکره',
        'birth_date' => 'تاریخ تولد',
        'marital_status' => 'حالت مدنی',
        'phone' => 'شماره تلفن',
        'email' => 'ایمیل',
        'job' => 'وظیفه',
        'work_place' => 'محل کار',
        'education_level' => 'سطح تحصیل',
        'economic_status' => 'وضعیت اقتصادی',
        'permanent_address' => 'آدرس دایمی',
        'current_address' => 'آدرس فعلی',
        'family_members' => 'تعداد اعضای خانواده',
        'register_date' => 'تاریخ ثبت‌نام',
        'monthly_fee' => 'فیس ماهانه',
        'member_type' => 'نوع عضویت',
        'role' => 'نقش',
        'status' => 'وضعیت',
        'user_id' => 'عضو',
        'member_id' => 'عضو',
        'category_id' => 'بخش / حساب',
        'source_account_id' => 'از حساب',
        'target_account_id' => 'به حساب',
        'from_account_id' => 'از حساب',
        'to_account_id' => 'به حساب',
        'credit_id' => 'قرض',
        'credit_payment_id' => 'قسط قرض',
        'income_id' => 'درآمد',
        'amount' => 'مقدار',
        'remaining_amount' => 'باقی‌مانده',
        'paid_amount' => 'مقدار پرداخت‌شده',
        'balance' => 'موجودی',
        'debit' => 'بدهکار',
        'credit' => 'بستانکار',
        'date' => 'تاریخ',
        'paid_at' => 'تاریخ پرداخت',
        'last_payment_at' => 'آخرین پرداخت',
        'month_of' => 'برای ماه',
        'transaction_type' => 'نوع تراکنش',
        'type' => 'نوع',
        'account_type' => 'نوع حساب',
        'is_monthly_fee' => 'فیس ماهانه',
        'creditor_name' => 'نام پرداخت‌کننده',
        'party' => 'از طرف',
        'description' => 'توضیحات',
        'note' => 'یادداشت',
    ];

    /** Fields that are noise or secret and never shown. */
    protected const HIDDEN = [
        'id', 'created_at', 'updated_at', 'password', 'remember_token', 'email_verified_at',
        'slug', 'review_status', 'created_by', 'photo', 'documents', 'has_account',
    ];

    protected const USER_FIELDS = ['user_id', 'member_id'];

    protected const ACCOUNT_FIELDS = [
        'category_id', 'source_account_id', 'target_account_id', 'from_account_id', 'to_account_id',
    ];

    protected const VALUES = [
        'status' => [
            'active' => 'فعال', 'inactive' => 'غیرفعال', 'suspended' => 'تعلیق (قرض)',
            'pending' => 'در انتظار', 'dead' => 'فوت‌شده', 'paid' => 'تسویه شده',
            'transferred' => 'واریز شده',
        ],
        'transaction_type' => ['credit' => 'بستانکار (دریافت)', 'debit' => 'بدهکار'],
        'type' => ['deposit' => 'دریافت پول', 'transfer' => 'انتقال بین حساب‌ها', 'withdraw' => 'ارسال پول'],
        'account_type' => [
            'receivable' => 'حساب‌های دریافتنی (قرض)', 'payable' => 'حساب‌های پرداختنی',
            'cash' => 'حساب نقدی / صندوق', 'aid' => 'حساب کمک‌ها', 'income' => 'منبع درآمد',
            'expense' => 'حساب مصارف', 'payment_type' => 'نوع پرداخت',
        ],
        'gender' => ['male' => 'مرد', 'female' => 'زن'],
        'role' => ['admin' => 'مدیر', 'user' => 'عضو'],
    ];

    protected const MONEY_FIELDS = ['amount', 'remaining_amount', 'paid_amount', 'balance', 'debit', 'credit', 'monthly_fee'];

    /** @var Collection<int, string> */
    protected Collection $userNames;

    /** @var Collection<int, string> */
    protected Collection $accountNames;

    /**
     * @param  iterable<Activity>  $activities
     * @return Collection<int, array>
     */
    public function present(iterable $activities): Collection
    {
        $activities = collect($activities);
        $this->loadNames($activities);

        return $activities->map(fn (Activity $activity) => $this->presentOne($activity));
    }

    protected function presentOne(Activity $activity): array
    {
        $model = $activity->subject_type ? class_basename($activity->subject_type) : null;
        [$section, $noun] = self::SECTIONS[$model] ?? [$model ?: 'عمومی', $model ?: 'رکورد'];

        $event = $activity->event ?: $activity->description;
        [$verb, $eventLabel, $badge] = self::EVENTS[$event] ?? [$activity->description, $activity->description, 'bg-secondary'];

        $new = (array) ($activity->properties['attributes'] ?? []);
        $old = (array) ($activity->properties['old'] ?? []);
        $record = $event === 'deleted' ? ($old ?: $new) : ($new ?: $old);

        $title = $this->recordTitle($model, $record);
        $who = $activity->causer?->name ?? 'سیستم';

        return [
            'id' => $activity->id,
            'who' => $who,
            'event' => $event,
            'event_label' => $eventLabel,
            'badge' => $badge,
            'section' => $section,
            'sentence' => trim("{$who} {$noun}" . ($title ? " «{$title}»" : '') . " را {$verb}."),
            'changes' => $this->changes($event, $new, $old),
            'when' => $activity->created_at,
        ];
    }

    /**
     * For an update: only the fields whose value changed (old → new).
     * For a create / delete: the main fields of the record.
     */
    protected function changes(string $event, array $new, array $old): array
    {
        $rows = [];

        if ($event === 'updated') {
            foreach ($new as $field => $value) {
                if ($this->isHidden($field) || ! array_key_exists($field, $old)) {
                    continue;
                }
                if ($this->same($old[$field], $value)) {
                    continue;
                }
                $rows[] = [
                    'field' => $this->label($field),
                    'old' => $this->value($field, $old[$field]),
                    'new' => $this->value($field, $value),
                ];
            }

            return $rows;
        }

        $values = $event === 'deleted' ? ($old ?: $new) : $new;

        foreach ($values as $field => $value) {
            if ($this->isHidden($field) || $value === null || $value === '') {
                continue;
            }
            $rows[] = [
                'field' => $this->label($field),
                'old' => null,
                'new' => $this->value($field, $value),
            ];
        }

        return $rows;
    }

    /** A short name for the record, e.g. a member name or "احمد — 5,000". */
    protected function recordTitle(?string $model, array $record): ?string
    {
        $memberId = $record['user_id'] ?? $record['member_id'] ?? null;
        $member = $memberId ? ($this->userNames[$memberId] ?? null) : null;
        $amount = isset($record['amount']) ? $this->money($record['amount']) : null;

        return match ($model) {
            'User', 'Category', 'EthnicBranch', 'Representative', 'KeyPerson', 'FamilyMembers'
                => $record['name'] ?? null,
            'Income' => collect([$record['creditor_name'] ?? null, $amount])->filter()->implode(' — '),
            'Expense' => collect([$amount, $record['description'] ?? null])->filter()->implode(' — '),
            'ReceivePayment' => collect([$member, $amount, isset($record['month_of']) ? 'ماه ' . $record['month_of'] : null])
                ->filter()->implode(' — '),
            'AccountTransaction' => collect([
                self::VALUES['type'][$record['type'] ?? ''] ?? null,
                $amount,
            ])->filter()->implode(' — '),
            default => collect([$member, $amount])->filter()->implode(' — ') ?: null,
        };
    }

    protected function value(string $field, mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (in_array($field, self::USER_FIELDS, true)) {
            return $this->userNames[$value] ?? "عضو #{$value}";
        }

        if (in_array($field, self::ACCOUNT_FIELDS, true)) {
            return $this->accountNames[$value] ?? "حساب #{$value}";
        }

        if ($field === 'credit_id') {
            return "قرض #{$value}";
        }

        if (isset(self::VALUES[$field])) {
            return self::VALUES[$field][$value] ?? (string) $value;
        }

        if (is_bool($value) || $field === 'is_monthly_fee') {
            return $value ? 'بله' : 'خیر';
        }

        if (in_array($field, self::MONEY_FIELDS, true) && is_numeric($value)) {
            return $this->money($value);
        }

        if (is_string($value) && preg_match('/^(\d{4}-\d{2}-\d{2})[T ](\d{2}:\d{2})/', $value, $m)) {
            return $m[2] === '00:00' ? $m[1] : "{$m[1]} {$m[2]}";
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return (string) $value;
    }

    protected function money(mixed $value): string
    {
        $number = (float) $value;

        return number_format($number, fmod($number, 1.0) == 0.0 ? 0 : 2);
    }

    protected function label(string $field): string
    {
        return self::FIELDS[$field] ?? $field;
    }

    protected function isHidden(string $field): bool
    {
        return in_array($field, self::HIDDEN, true);
    }

    protected function same(mixed $a, mixed $b): bool
    {
        if (is_numeric($a) && is_numeric($b)) {
            return (float) $a === (float) $b;
        }

        return $a == $b;
    }

    /** Load all member and account names used on this page in two queries. */
    protected function loadNames(Collection $activities): void
    {
        $userIds = [];
        $accountIds = [];

        foreach ($activities as $activity) {
            foreach (['attributes', 'old'] as $key) {
                foreach ((array) ($activity->properties[$key] ?? []) as $field => $value) {
                    if ($value === null || $value === '') {
                        continue;
                    }
                    if (in_array($field, self::USER_FIELDS, true)) {
                        $userIds[] = $value;
                    } elseif (in_array($field, self::ACCOUNT_FIELDS, true)) {
                        $accountIds[] = $value;
                    }
                }
            }
        }

        $this->userNames = $userIds
            ? User::whereIn('id', array_unique($userIds))->pluck('name', 'id')
            : collect();

        $this->accountNames = $accountIds
            ? Category::whereIn('id', array_unique($accountIds))->pluck('name', 'id')
            : collect();
    }
}
