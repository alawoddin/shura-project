@extends('admin.dashboard')

@section('admin')

    <div class="container-fluid">

        <div class="card shadow-sm">

            <div class="card-header bg-dark text-white">

                اضافه کردن اعضای فامیل

            </div>

            <div class="card-body">

                <form action="{{ route('store.users.family') }}" method="POST">

                    @csrf

                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                    @if ($user->family_members > 0)

                            <div class="card mb-4 border">

                                <div class="card-header bg-light">

                                    عضو فامیل

                                </div>

                                <div class="card-body">

                                    <div class="row g-3">

                                        <div class="col-md-4">

                                            <label>

                                                اسم

                                            </label>

                                            <input type="text" name="name" class="form-control" required>

                                        </div>

                                        <div class="col-md-2">

                                            <label>

                                                جنسیت

                                            </label>

                                            <select name="gender" class="form-select">

                                                <option value="male">

                                                    مرد

                                                </option>

                                                <option value="female">

                                                    زن

                                                </option>

                                            </select>

                                        </div>

                                        <div class="col-md-3">

                                            <label>

                                                تاریخ تولد

                                            </label>

                                            <input type="date" name="birth_date" id="birth_date" class="form-control">

                                        </div>

                                        <div class="col-md-3">

                                            <label>

                                                سن

                                            </label>

                                            <input type="number" id="age" class="form-control" readonly>

                                        </div>

                                        <div class="col-md-6">

                                            <label>

                                                تحصیلات

                                            </label>

                                            <input type="text" name="qualification" class="form-control">

                                        </div>

                                        <div class="col-md-6">

                                            <label>

                                                درجه

                                            </label>

                                            <input type="text" name="degree" class="form-control">

                                        </div>

                                        <div class="col-12">

                                            <label>

                                                یادداشت

                                            </label>

                                            <textarea name="note" rows="2" class="form-control"></textarea>

                                        </div>

                                    </div>

                                </div>

                            </div>

                    @else
                        <div class="alert alert-warning">

                            تعداد اعضای فامیل ثبت نشده

                        </div>
                    @endif

                    <div class="text-end">

                        <button type="submit" class="btn btn-primary px-5">

                            ذخیره اعضای فامیل

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <script>
        document.getElementById('birth_date').addEventListener('change', function() {
            const birthDate = new Date(this.value);
            if (!this.value || Number.isNaN(birthDate.getTime())) {
                document.getElementById('age').value = '';
                return;
            }

            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();

            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            document.getElementById('age').value = age;
        });
    </script>

@endsection