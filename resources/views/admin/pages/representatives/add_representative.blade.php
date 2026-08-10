@extends('admin.dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <div class="container-fluid my-0">
        <div class="py-3">
            <h4 class="fs-18 fw-semibold m-0">اضافه کردن نماینده</h4>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <form id="myForm" action="{{ route('store.representative') }}" method="POST" class="row g-3">
                            @csrf

                            <div class="col-md-6">
                                <label class="form-label">شاخه قومی</label>
                                <select name="ethnic_branch_id" class="form-control">
                                    <option value="">انتخاب شاخه قومی</option>
                                    @foreach ($ethnicBranches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('ethnic_branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم نماینده</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                            </div>

                            <div class="col-12 mt-3">
                                <button class="btn btn-primary" type="submit">ذخیره</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#myForm').validate({
                rules: {
                    ethnic_branch_id: { required: true },
                    name: { required: true },
                },
                messages: {
                    ethnic_branch_id: { required: 'لطفاً شاخه قومی را انتخاب کنید' },
                    name: { required: 'لطفاً اسم نماینده را وارد کنید' },
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
