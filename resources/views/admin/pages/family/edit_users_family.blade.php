@extends('admin.dashboard')

@section('admin')
    <div class="container-fluid">

        <div class="card shadow-sm">

            <div class="card-header bg-dark text-white">
                ویرایش اعضای فامیل
            </div>

            <div class="card-body">

                <form action="{{ route('update.users.family') }}" method="POST">

                    @csrf
                    <input type="hidden" name="id" value="{{ $familyMember->id }}">
                    <input type="hidden" name="user_id" value="{{ $familyMember->user_id }}">

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label>اسم</label>
                            <input type="text" name="name" class="form-control" value="{{ $familyMember->name }}" required>
                        </div>

                        <div class="col-md-2">
                            <label>جنسیت</label>
                            <select name="gender" class="form-select">
                                <option value="male" {{ $familyMember->gender == 'male' ? 'selected' : '' }}>مرد</option>
                                <option value="female" {{ $familyMember->gender == 'female' ? 'selected' : '' }}>زن</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>تاریخ تولد</label>
                            <input type="date" name="birth_date" id="birth_date" class="form-control"
                                value="{{ $familyMember->birth_date }}">
                        </div>

                        <div class="col-md-3">
                            <label>سن</label>
                            <input type="number" id="age" class="form-control" value="{{ \Carbon\Carbon::parse($familyMember->birth_date)->age }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label>تحصیلات</label>
                            <input type="text" name="qualification" class="form-control"
                                value="{{ $familyMember->qualification }}">
                        </div>

                        <div class="col-md-6">
                            <label>درجه</label>
                            <input type="text" name="degree" class="form-control" value="{{ $familyMember->degree }}">
                        </div>

                        <div class="col-12">
                            <label>یادداشت</label>
                            <textarea name="note" class="form-control" rows="2">{{ $familyMember->note }}</textarea>
                        </div>

                    </div>

                    <div class="text-end mt-3">
                        <button class="btn btn-primary">ذخیره</button>
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
        window.addEventListener('load', function () {
            document.getElementById('birth_date').dispatchEvent(new Event('change'));
        });
    </script>
@endsection
