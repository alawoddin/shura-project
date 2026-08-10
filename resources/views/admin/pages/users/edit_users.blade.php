@extends('admin.dashboard')

@section('admin')
<div class="container-fluid">

    <div class="card mt-3 shadow-sm">
        <div class="card-header bg-dark text-white">
            ویرایش کاربر
        </div>

        <div class="card-body">

            <form action="{{ route('update.users') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="id" value="{{ $users->id }}">

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label>اسم</label>
                        <input type="text" name="name" value="{{ $users->name }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>ولد</label>
                        <input type="text" name="father_name" value="{{ $users->father_name }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>ولدیت</label>
                        <input type="text" name="grandfather_name" value="{{ $users->grandfather_name }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>تخلص</label>
                        <input type="text" name="lastname" value="{{ $users->lastname }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>جنسیت</label>
                        <select name="gender" class="form-control">
                            <option value="male" {{ $users->gender == 'male' ? 'selected' : '' }}>مرد</option>
                            <option value="female" {{ $users->gender == 'female' ? 'selected' : '' }}>زن</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>نمبر تذکره </label>
                        <input type="text" name="national_id" value="{{ $users->national_id }}"  class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>تاریخ تولد</label>
                        <input type="date" name="birth_date" value="{{ $users->birth_date }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>حالت مدنی</label>
                        <select name="marital_status" class="form-control">
                            <option value="single" {{ $users->marital_status == 'single' ? 'selected' : '' }}>مجرد</option>
                            <option value="married" {{ $users->marital_status == 'married' ? 'selected' : '' }}>متاهل</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>گروپ خون</label>
                        <select name="blood_group" class="form-control">
                            <option value="">انتخاب کنید</option>
                            <option value="A+" {{ $users->blood_group == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ $users->blood_group == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ $users->blood_group == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ $users->blood_group == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ $users->blood_group == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ $users->blood_group == 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ $users->blood_group == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ $users->blood_group == 'O-' ? 'selected' : '' }}>O-</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>سکونت اصلی</label>
                        <textarea name="permanent_address" class="form-control">{{ $users->permanent_address }}</textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>سکونت فعلی</label>
                        <textarea name="current_address" class="form-control">{{ $users->current_address }}</textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>درجه تحصیلی</label>
                        <select name="education_level" class="form-control">
                            <option value="illutrate" {{ $users->education_level == 'illutrate' ? 'selected' : '' }}>بی سواد</option>
                            <option value="graduate" {{ $users->education_level == 'graduate' ? 'selected' : '' }}>فارغ التحصیل</option>
                            <option value="bachelor" {{ $users->education_level == 'bachelor' ? 'selected' : '' }}>لیسانس</option>
                            <option value="master" {{ $users->education_level == 'master' ? 'selected' : '' }}>ماستر</option>
                            <option value="PhD" {{ $users->education_level == 'PhD' ? 'selected' : '' }}>دوکتورا</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>وظیفه</label>
                        <input type="text" name="job" value="{{ $users->job }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>محل کار</label>
                        <input type="text" name="work_place" value="{{ $users->work_place }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>شماره تماس</label>
                        <input type="text" name="phone" value="{{ $users->phone }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>وضعیت اقتصادی</label>
                        <input type="text" name="economic_status" value="{{ $users->economic_status }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>تعداد اعضای فامیل</label>
                        <input type="number" name="family_members" value="{{ $users->family_members }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>تاریخ ثبت</label>
                        <input type="date" name="register_date" value="{{ $users->register_date }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>وضعیت</label>
                        <select name="status" class="form-control">
                            <option value="active" {{ $users->status == 'active' ? 'selected' : '' }}>فعال</option>
                            <option value="inactive" {{ $users->status == 'inactive' ? 'selected' : '' }}>غیرفعال</option>
                            <option value="pending" {{ $users->status == 'pending' ? 'selected' : '' }}>تعلیق</option>
                            <option value="dead" {{ $users->status == 'dead' ? 'selected' : '' }}>فوت شده</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>نوعیت عضو</label>
                        <select name="member_type" class="form-control">
                            <option value="normal" {{ $users->member_type == 'normal' ? 'selected' : '' }}>معمولی</option>
                            <option value="silver" {{ $users->member_type == 'silver' ? 'selected' : '' }}>نقره‌ای</option>
                            <option value="golden" {{ $users->member_type == 'golden' ? 'selected' : '' }}>طلایی</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>فیس ماهانه</label>
                        <input type="number" step="0.01" name="monthly_fee" value="{{ $users->monthly_fee }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>شاخه قومی</label>
                        <input type="text" name="ethnic_branch" value="{{ $users->ethnic_branch }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>اسم نماینده</label>
                        <input type="text" name="representative_name" value="{{ $users->representative_name }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>عکس جدید</label>
                        <input type="file" name="photo" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <img src="{{ url('upload/user_images/'.$users->photo) }}" width="80" class="rounded">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>اسناد جدید</label>
                        <input type="file" name="documents" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>ایمیل</label>
                        <input type="email" name="email" value="{{ $users->email }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>پسورد جدید</label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>نقش</label>
                        <select name="role" class="form-control">
                            <option value="user" {{ $users->role == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ $users->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                </div>

                <button class="btn btn-primary">آپدیت</button>

            </form>

        </div>
    </div>

</div>
@endsection