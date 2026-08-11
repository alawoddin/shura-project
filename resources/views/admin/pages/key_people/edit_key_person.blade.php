@extends('admin.dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <div class="container-fluid my-0">
        <div class="py-3">
            <h4 class="fs-18 fw-semibold m-0">ویرایش فرد کلیدی</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="myForm" action="{{ route('update.key.person') }}" method="POST" class="row g-3">
                    @csrf
                    <input type="hidden" name="id" value="{{ $keyPerson->id }}">

                    <div class="col-md-6">
                        <label class="form-label">انتخاب کاربر</label>
                        <select name="user_id" class="form-control">
                            <option value="">انتخاب کاربر</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('user_id', $keyPerson->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} @if($user->phone) — {{ $user->phone }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">سمت</label>
                        <select name="position" class="form-control">
                            <option value="">انتخاب سمت</option>
                            @foreach (['مدیر مالی', 'مدیر عامل', 'منشی', 'مسئول اداری', 'مسئول فرهنگی', 'سایر'] as $position)
                                <option value="{{ $position }}"
                                    {{ old('position', $keyPerson->position) == $position ? 'selected' : '' }}>
                                    {{ $position }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">یادداشت</label>
                        <textarea name="note" rows="3" class="form-control">{{ old('note', $keyPerson->note) }}</textarea>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary">به‌روزرسانی</button>
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
