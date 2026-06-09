@extends('admin.dashboard')
@section('admin')
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">گذارش مصارفات </h4>
                </div>

                {{-- <div class="text-end">
                                <ol class="breadcrumb m-0 py-0">
                                    <a href="{{ route('add.employee') }}" class="btn btn-secondary">اضافه کردن کارمند</a>
                                </ol>
                            </div> --}}
            </div>

            <!-- Datatables  -->
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-header">

                        </div><!-- end card header -->

                        <div class="card-body">

                            <div class="row">

                                <!-- Search By Month -->
                                <div class="col-sm-4">
                                    <div class="card">
                                        <div class="card-body">

                                            <form action="{{ route('search.reports.by.date') }}" method="post">
                                                @csrf

                                                <h5>جستجو بر اساس تاریخ</h5>

                                                <div class="mb-3">
                                                    <label class="form-label">انتخاب تاریخ</label>
                                                    <input class="form-control" type="date" name="date"
                                                        id="example-text-input">
                                                </div>

                                                <button type="submit" class="btn btn-primary w-100">
                                                    جستجو
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </div>

                                <!-- Search By Month -->
                                <div class="col-sm-4">
                                    <div class="card">
                                        <div class="card-body">

                                            <form action="{{ route('search.reports.by.month') }}" method="post">
                                                @csrf

                                                <h5>جستجو بر اساس ماه</h5>

                                                <div class="mb-3">
                                                    <label class="form-label">انتخاب ماه</label>

                                                    <select name="month" class="form-select">
                                                        <option value="">Select Month</option>
                                                        <option value="1">January</option>
                                                        <option value="2">February</option>
                                                        <option value="3">March</option>
                                                        <option value="4">April</option>
                                                        <option value="5">May</option>
                                                        <option value="6">June</option>
                                                        <option value="7">July</option>
                                                        <option value="8">August</option>
                                                        <option value="9">September</option>
                                                        <option value="10">October</option>
                                                        <option value="11">November</option>
                                                        <option value="12">December</option>
                                                    </select>
                                                </div>

                                                <button type="submit" class="btn btn-primary w-100">
                                                    جستجو
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                                {{-- year report --}}
                                <div class="col-sm-4">
                                    <div class="card">
                                        <div class="card-body">

                                            <form action="{{ route('search.reports.by.year') }}" method="post">
                                                @csrf

                                                <h5>جستجو بر اساس سال</h5>

                                                <div class="mb-3">
                                                    <label class="form-label">انتخاب سال</label>

                                                    <select name="year" class="form-select">
                                                        <option value="">Select Year</option>
                                                        <option value="2025">2025</option>
                                                        <option value="2026">2026</option>
                                                        <option value="2027">2027</option>
                                                        <option value="2028">2028</option>
                                                        <option value="2029">2029</option>
                                                        <option value="2030">2030</option>
                                                    </select>
                                                </div>

                                                <button type="submit" class="btn btn-primary w-100">
                                                    جستجو
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </div>



                            </div>

                        </div>

                    </div>
                </div>
            </div>


        </div> <!-- container-fluid -->

    </div> <!-- content -->
@endsection
