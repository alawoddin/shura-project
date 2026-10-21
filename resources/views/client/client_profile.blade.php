@extends('client.client_dashboard')

@section('title', 'پروفایل من')

@section('client')
<div class="content">
    <div class="container-fluid client-dash">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">پروفایل شخصی</h4>
                <p class="text-muted mb-0">اطلاعات حساب خود را به‌روزرسانی کنید</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">ویرایش پروفایل</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('user.profile.store') }}" method="post" enctype="multipart/form-data" class="row g-3">
                    @csrf

                    <div class="col-md-6">
                        <label class="form-label">نام</label>
                        <input type="text" name="name" class="form-control" value="{{ $profileData->name }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">ایمیل</label>
                        <input type="email" name="email" class="form-control" value="{{ $profileData->email }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">نمبر</label>
                        <input type="text" name="phone" class="form-control" value="{{ $profileData->phone }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">آدرس</label>
                        <input type="text" name="address" class="form-control" value="{{ $profileData->address }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">عکس پروفایل</label>
                        <input type="file" name="photo" class="form-control" id="image">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">پیش‌نمایش</label>
                        <div>
                            <img id="showImage"
                                src="{{ !empty($profileData->photo) ? url('upload/user_images/'.$profileData->photo) : url('upload/no_image.jpg') }}"
                                class="rounded-circle img-thumbnail"
                                style="width: 80px; height:80px; object-fit: cover;">
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('image')?.addEventListener('change', function (e) {
        const file = e.target.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (event) {
            document.getElementById('showImage').src = event.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>
@endsection
