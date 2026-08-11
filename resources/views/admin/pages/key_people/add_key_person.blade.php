@extends('admin.dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <div class="container-fluid my-0">
        <div class="py-3">
            <h4 class="fs-18 fw-semibold m-0">اضافه کردن فرد کلیدی</h4>
        </div>

        <div class="alert alert-info">
            کاربر از لیست اعضای موجود انتخاب می‌شود. برای افراد کلیدی اکانت جدید ساخته نمی‌شود و ایمیل/پسورد لازم نیست.
        </div>

        <div class="card">
            <div class="card-body">
                <form id="myForm" action="{{ route('store.key.person') }}" method="POST" class="row g-3">
                    @csrf

                    <div class="col-md-6">
                        <label class="form-label">انتخاب کاربر</label>
                        <select name="user_id" class="form-control">
                            <option value="">انتخاب کاربر</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} @if($user->phone) — {{ $user->phone }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">سمت</label>
                        <select name="position" class="form-control">
                            <option value="">انتخاب سمت</option>
                            <option value="مدیر مالی" {{ old('position') == 'مدیر مالی' ? 'selected' : '' }}>مدیر مالی</option>
                            <option value="مدیر عامل" {{ old('position') == 'مدیر عامل' ? 'selected' : '' }}>مدیر عامل</option>
                            <option value="منشی" {{ old('position') == 'منشی' ? 'selected' : '' }}>منشی</option>
                            <option value="مسئول اداری" {{ old('position') == 'مسئول اداری' ? 'selected' : '' }}>مسئول اداری</option>
                            <option value="مسئول فرهنگی" {{ old('position') == 'مسئول فرهنگی' ? 'selected' : '' }}>مسئول فرهنگی</option>
                            <option value="سایر" {{ old('position') == 'سایر' ? 'selected' : '' }}>سایر</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">یادداشت</label>
                        <textarea name="note" rows="3" class="form-control">{{ old('note') }}</textarea>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary">ذخیره</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#myForm').validate({
                rules: {
                    user_id: { required: true },
                    position: { required: true },
                },
                messages: {
                    user_id: { required: 'لطفاً کاربر را انتخاب کنید' },
                    position: { required: 'لطفاً سمت را انتخاب کنید' },
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.parent().append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
            });
        });
    </script>
@endsection
