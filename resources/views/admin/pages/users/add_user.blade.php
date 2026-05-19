@extends('admin.dashboard')

@section('admin')
<div class="container-fluid">

    <div class="card mt-3 shadow-sm">
        <div class="card-header bg-dark text-white">
            اضافه کردن کاربر
        </div>

        <div class="card-body">
            <form action="{{ route('store.user') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label>اسم</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>ولد</label>
                        <input type="text" name="father_name" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>ولدیت</label>
                        <input type="text" name="grandfather_name" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>تخلص</label>
                        <input type="text" name="lastname" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>جنسیت</label>
                        <select name="gender" class="form-control">
                            <option value="male">مرد</option>
                            <option value="female">زن</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>تاریخ تولد</label>
                        <input type="date" name="birth_date" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>حالت مدنی</label>
                        <select name="marital_status" class="form-control">
                            <option value="single">مجرد</option>
                            <option value="married">متاهل</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>سکونت اصلی</label>
                        <textarea name="permanent_address" class="form-control"></textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>سکونت فعلی</label>
                        <textarea name="current_address" class="form-control"></textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>درجه تحصیلی </label>
                        <select name="education_level" class="form-control">
                            <option value="illutrate">بی سواد</option>
                            <option value="graduate">فارغ التحصیل</option>
                            <option value="bachelor">لیسانس</option>
                            <option value="master">ماستر</option>
                            <option value="PhD">دوکتورا</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>وظیفه</label>
                        <input type="text" name="job" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>محل کار</label>
                        <input type="text" name="work_place" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>شماره تماس</label>
                        <input type="text" name="phone" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>وضعیت اقتصادی</label>
                        <input type="text" name="economic_status" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>تعداد اعضای فامیل</label>
                        <input type="number" name="family_members" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>تاریخ ثبت</label>
                        <input type="date" name="register_date" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>وضعیت</label>
                        <select name="status" class="form-control">
                            <option value="active">فعال</option>
                            <option value="inactive">غیرفعال</option>
                            <option value="pending">تعلیق</option>
                            <option value="dead">قوت شده</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>نوعیت عضو</label>
                        <select name="member_type" class="form-control">
                            <option value="normal">معمولی</option>
                            <option value="silver">تقره یی</option>
                            <option value="golden">طلا یی</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>فیس ماهانه</label>
                        <input type="number" step="0.01" name="monthly_fee" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>شاخه قومی</label>
                        <input type="text" name="ethnic_branch" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>اسم نماینده</label>
                        <input type="text" name="representative_name" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>عکس</label>
                        <input type="file" name="photo" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>اسناد</label>
                        <input type="file" name="documents" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>ایمیل</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>پسورد</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>نقش</label>
                        <select name="role" class="form-control">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                </div>

                <button class="btn btn-primary">ذخیره</button>
            </form>
        </div>
    </div>

</div>
@endsection