@extends('admin.dashboard')

@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Start Content-->
    <div class="container-fluid my-0">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Add Income</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">

                    <li class="breadcrumb-item active">Add Income</li>
                </ol>
            </div>
        </div>

        <!-- Form Validation -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Income</h5>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <form action="{{ route('store.expense') }}" method="POST" class="row g-3">
                            @csrf

                            <div class="col-md-6">
                                <label>Category</label>
                                <select name="category_id" class="form-control">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="validationDefault01" class="form-label">Expense Name</label>
                                <input type="text" class="form-control" name="expense_name">
                            </div>

                            <div class="col-md-6">
                                <label for="validationDefault01" class="form-label">Amount</label>
                                <input type="text" class="form-control" name="amount">
                            </div>

                            <div class="col-md-6">
                                <label for="validationDefault01" class="form-label">Date</label>
                                <input type="date" class="form-control" name="date">
                            </div>


                            <div class="col-md-6">
                                <label for="validationDefault02" class="form-label">Description</label>
                                <textarea class="form-control" name="description"></textarea>



                                <div class="col-12 mt-3">
                                    <button class="btn btn-primary" type="submit">Save Change</button>
                                </div>
                        </form>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->


        </div>



    </div> <!-- container-fluid -->

    </div>




    <script type="text/javascript">
        $(document).ready(function() {
            $('#myForm').validate({
                rules: {
                    name: {
                        required: true,
                    },
                    father_name: {
                        required: true,
                    },


                },
                messages: {
                    name: {
                        required: 'Please Enter customer name',
                    },
                    father_name: {
                        required: 'Please Enter User father_name',
                    },


                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
            });
        });
    </script>
@endsection
