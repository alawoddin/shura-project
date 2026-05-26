@extends('admin.dashboard')

@section('admin')
    <div class="content">

        <div class="container-fluid my-0">

            <div class="py-3 d-flex align-items-center">

                <div class="flex-grow-1">

                    <h4 class="fs-18 fw-semibold">

                        قرض ها

                    </h4>

                </div>


                <div>

                    <a href="{{ route('add.credit') }}" class="btn btn-secondary">

                        اضافه کردن

                    </a>

                </div>

            </div>




            <div class="card">

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="datatable" class="table table-bordered">

                            <thead>

                                <tr>

                                    <th>آیدی</th>

                                    <th>کاربر</th>

                                    <th>بخش</th>

                                    <th>تاریخ</th>

                                    <th>قرض</th>

                                    <th>باقیمانده</th>

                                    <th>وضعیت</th>

                                    <th>نوت</th>

                                    <th>عملیات</th>

                                </tr>

                            </thead>



                            <tbody>

                                @foreach ($alldata as $key => $item)
                                    <tr>

                                        <td>

                                            {{ $key + 1 }}

                                        </td>

                                        <td>

                                            {{ $item->user->name }}

                                        </td>

                                        <td>

                                            {{ $item->category->name }}

                                        </td>

                                        <td>

                                            {{ $item->date }}

                                        </td>

                                        <td>

                                            {{ $item->amount }}

                                        </td>

                                        <td>

                                            {{ $item->remaining_amount }}

                                        </td>


                                        <td>

                                            @if ($item->status == 'active')
                                                <span class="badge bg-warning">

                                                    فعال

                                                </span>
                                            @else
                                                <span class="badge bg-success">

                                                    پرداخت شد

                                                </span>
                                            @endif

                                        </td>


                                        <td>

                                            {{ $item->description }}

                                        </td>



                                        <td>

                                            <a href="{{ route('edit.credit', $item->id) }}" class="btn btn-success btn-sm">

                                                ویرایش

                                            </a>



                                            @if ($item->status == 'active')
                                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#payModal{{ $item->id }}">

                                                    پرداخت

                                                </button>
                                            @endif



                                            <a href="{{ route('delete.credit', $item->id) }}" class="btn btn-danger btn-sm">

                                                حذف

                                            </a>

                                        </td>

                                    </tr>



                                    {{-- Payment Modal --}}

                                    <div class="modal fade" id="payModal{{ $item->id }}">

                                        <div class="modal-dialog">

                                            <div class="modal-content">

                                                <form action="{{ route('paid.credit') }}" method="POST">

                                                    @csrf


                                                    <input type="hidden" name="id" value="{{ $item->id }}">


                                                    <div class="modal-header">

                                                        <h5>

                                                            ثبت پرداخت

                                                        </h5>

                                                    </div>



                                                    <div class="modal-body">

                                                        <label>

                                                            باقیمانده:

                                                            {{ $item->remaining_amount }}

                                                        </label>


                                                        <input type="number" name="paid_amount" class="form-control"
                                                            required>

                                                    </div>



                                                    <div class="modal-footer">

                                                        <button class="btn btn-primary">

                                                            ثبت

                                                        </button>

                                                    </div>


                                                </form>

                                            </div>

                                        </div>

                                    </div>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
