@extends('admin.dashboard')
@section('admin')

<div class="content">
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0"> Daily Report </h4>  
                {{-- ////{{ $date }}// --}}
            </div>
        </div>

        {{-- ---------------- Expenses Table ---------------- --}}
        <div class="row mt-3">
            <div class="col-12">
                <div class="card shadow-sm">

                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Daliy Expense</h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable-expenses" class="table table-bordered align-middle text-nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-center">Employee </th>
                                        <th class="text-center">Expense amount</th>
                                        <th class="text-center">Total Price</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <tr>
                                        <td colspan="6" class="text-center">Expense is not found</td>
                                    </tr>
                                </tbody>
                              
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ---------------- Sales Table ---------------- --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">

                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Sale Report</h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable-sales" class="table table-bordered align-middle text-nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Employee name</th>
                                        <th>Sale items</th>
                                        <th>total sale</th>
                                        <th>total expense</th>
                                        <th>profit</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <tr>
                                            <td colspan="8" class="text-center">sale is not founded</td>
                                        </tr>
                                </tbody>
                                
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

       

       

    </div>
</div>

@endsection