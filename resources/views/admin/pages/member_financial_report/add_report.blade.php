@extends('admin.dashboard')

@section('admin')

<div class="container-fluid my-0">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">
                اضافه کردن راپور مالی عضو
            </h4>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">

            <div class="card">

                <div class="card-header">
                    <h5 class="card-title mb-0">
                        اضافه کردن راپور مالی
                    </h5>
                </div>

                <div class="card-body">

                    <form action="{{ route('store.financial.report') }}" method="POST" class="row g-3">
                        @csrf

                        <div class="col-md-6">
                            <label class="form-label">عضو</label>

                            <select name="member_id" class="form-control">
                                <option value="">انتخاب عضو</option>

                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">تاریخ</label>

                            <input type="date" class="form-control" name="date">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">بدهکار (Dr)</label>

                            <input type="number" class="form-control" name="debit" value="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">بستانکار (Cr)</label>

                            <input type="number" class="form-control" name="credit" value="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">بیلانس</label>

                            <input type="number" class="form-control" name="balance" value="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">توضیحات</label>

                            <textarea class="form-control" name="description"></textarea>
                        </div>

                        <div class="col-12 mt-3">
                            <button class="btn btn-primary" type="submit">
                                ذخیره
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>

@endsection