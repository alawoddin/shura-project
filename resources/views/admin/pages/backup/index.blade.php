@extends('admin.dashboard')

@section('admin')

<div class="content">
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">پشتیبان‌گیری</h4>
            </div>

            <div class="text-end">
                <form method="POST" action="{{ route('admin.backup.create') }}" onsubmit="this.querySelector('button').disabled = true;">
                    @csrf
                    <button type="submit" class="btn btn-primary">گرفتن بکاپ</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <p class="mb-0 text-muted">هر بکاپ شامل پایگاه داده و فایل‌های آپلود شده (تصاویر و اسناد اعضا) است. فایل را دانلود کنید و در جای امن نگه دارید.</p>
                    </div>

                    <div class="card-body">
                        <table id="datatable" class="table table-bordered dt-responsive table-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>شماره</th>
                                    <th>نام فایل</th>
                                    <th>حجم</th>
                                    <th>تاریخ</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($files as $key => $file)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $file['name'] }}</td>
                                        <td>{{ $file['size'] >= 1048576 ? number_format($file['size'] / 1048576, 2).' MB' : number_format($file['size'] / 1024, 1).' KB' }}</td>
                                        <td>{{ date('Y-m-d H:i', $file['created_at']) }}</td>
                                        <td>
                                            <a href="{{ route('admin.backup.download', $file['name']) }}" class="btn btn-success btn-sm">دانلود</a>
                                            <a href="{{ route('admin.backup.delete', $file['name']) }}" class="btn btn-danger btn-sm" id="delete">حذف</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
