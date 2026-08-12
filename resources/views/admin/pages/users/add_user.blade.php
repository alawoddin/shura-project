@extends('admin.dashboard')

@section('admin')

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <div class="container-fluid">

        <div class="card mt-3 shadow-sm">
            <div class="card-header bg-dark text-white">
                اضافه کردن کاربر
            </div>

            <div class="card-body">
                <form action="{{ route('store.user') }}" method="POST" id="myForm" enctype="multipart/form-data">
                    @csrf

                    <div class="row">

                        <div class="col-md-4 mb-3 form-group">
                            <label> <span class="text text-danger"> * </span>اسم</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3 form-group">
                            <label>ولد</label>
                            <input type="text" name="father_name" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>ولدیت</label>
                            <input type="text" name="grandfather_name" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>تخلص</label>
                            <input type="text" name="lastname" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>جنسیت</label>
                            <select name="gender" class="form-control">
                                <option value="male">مرد</option>
                                <option value="female">زن</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>نمبر تذکره </label>
                            <input type="text" name="national_id" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>تاریخ تولد</label>
                            <input type="date" name="birth_date" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>حالت مدنی</label>
                            <select name="marital_status" class="form-control">
                                <option value="single">مجرد</option>
                                <option value="married">متاهل</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>گروپ خون</label>
                            <select name="blood_group" class="form-control">
                                <option value="">انتخاب کنید</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>سکونت اصلی</label>
                            <textarea name="permanent_address" class="form-control"></textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>سکونت فعلی</label>
                            <textarea name="current_address" class="form-control"></textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>درجه تحصیلی </label>
                            <select name="education_level" class="form-control">
                                <option value="illutrate">بی سواد</option>
                                <option value="graduate">فارغ التحصیل</option>
                                <option value="bachelor">لیسانس</option>
                                <option value="master">ماستر</option>
                                <option value="PhD">دوکتورا</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>وظیفه</label>
                            <input type="text" name="job" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>محل کار</label>
                            <input type="text" name="work_place" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>شماره تماس</label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>وضعیت اقتصادی</label>
                            <input type="text" name="economic_status" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>تعداد اعضای فامیل</label>
                            <input type="number" name="family_members" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>تاریخ ثبت</label>
                            <input type="date" name="register_date" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>وضعیت</label>
                            <select name="status" class="form-control">
                                <option value="active">فعال</option>
                                <option value="inactive">غیرفعال</option>
                                <option value="pending">تعلیق</option>
                                <option value="dead">فوت شده</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>نوعیت عضو</label>
                            <select name="member_type" class="form-control">
                                <option value="normal">معمولی</option>
                                <option value="silver">تقره یی</option>
                                <option value="golden">طلا یی</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>فیس ماهانه</label>
                            <input type="number" step="0.01" name="monthly_fee" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>شاخه قومی</label>
                            <select name="ethnic_branch_id" id="ethnic_branch_id" class="form-control">
                                <option value="">انتخاب شاخه قومی</option>
                                @foreach ($ethnicBranches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('ethnic_branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>اسم نماینده</label>
                            <select name="representative_id" id="representative_id" class="form-control">
                                <option value="">ابتدا شاخه قومی را انتخاب کنید</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>عکس</label>
                            <input type="file" name="photo" class="form-control" id="image">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label for="validationDefault02" class="form-label"> </label>
                            <img id="showImage" src="{{ url('upload/no_image.jpg') }}"
                                class="rounded-circle avatar-xl img-thumbnail float-start" alt="image profile">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>اسناد</label>
                            <input type="file" name="documents" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>
                                <input type="checkbox" name="has_account" value="1" id="has_account">
                                ایجاد حساب کاربری
                            </label>
                        </div>

                        <div id="account_fields" style="display:none;">
                            <div class="col-md-4 mb-3">
                                <label>ایمیل</label>
                                <input type="email" name="email" class="form-control">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>پسورد</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                        </div>

                        <input type="hidden" name="role" value="user">

                    </div>

                    <button class="btn btn-primary">ذخیره</button>
                </form>
            </div>
        </div>

    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#image').change(function(e) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#showImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            })
        })
    </script>

    
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

            function loadRepresentatives(ethnicBranchId, selectedId = null) {
                const $representativeSelect = $('#representative_id');

                if (!ethnicBranchId) {
                    $representativeSelect.html('<option value="">ابتدا شاخه قومی را انتخاب کنید</option>');
                    return;
                }

                $representativeSelect.html('<option value="">در حال بارگذاری...</option>');

                $.get("{{ url('admin/representatives/by-ethnic') }}/" + ethnicBranchId, function(data) {
                    let options = '<option value="">انتخاب نماینده</option>';

                    data.forEach(function(item) {
                        const selected = selectedId == item.id ? 'selected' : '';
                        options += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                    });

                    $representativeSelect.html(options);
                });
            }

            $('#ethnic_branch_id').on('change', function() {
                loadRepresentatives($(this).val());
            });

            if ($('#ethnic_branch_id').val()) {
                loadRepresentatives($('#ethnic_branch_id').val(), "{{ old('representative_id') }}");
            }
        });
        $('#has_account').on('change', function () {
            if ($(this).is(':checked')) {
                $('#account_fields').show();
            } else {
                $('#account_fields').hide();
            }
        });
    </script>


@endsection
