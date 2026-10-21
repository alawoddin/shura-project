@extends('client.client_dashboard')

@section('title', 'تغییر رمز')

@section('client')
<div class="content">
    <div class="container-fluid shura-dash">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">تغییر رمز</h4>
                <p class="text-muted mb-0">رمز عبور حساب خود را به‌روزرسانی کنید</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">رمز جدید</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('user.password.update') }}" method="post" class="row g-3">
                    @csrf

                    <div class="col-md-6">
                        <label class="form-label">رمز گذشته</label>
                        <input type="password" name="old_password" id="old_password"
                            class="form-control @error('old_password') is-invalid @enderror"
                            placeholder="رمز فعلی">
                        @error('old_password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">رمز جدید</label>
                        <input type="password" name="new_password" id="new_password"
                            class="form-control @error('new_password') is-invalid @enderror"
                            placeholder="رمز جدید">
                        @error('new_password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">تایید رمز جدید</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                            class="form-control" placeholder="تکرار رمز جدید">
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">ذخیره</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
