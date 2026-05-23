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

                    <input type="hidden" name="user_id" value="{{ $familyMember->user_id }}">

                    @php

                        $names = explode(',', $familyMember->name);

                        $genders = explode(',', $familyMember->gender);

                        $birthDates = explode(',', $familyMember->birth_date);

                        $ages = explode(',', $familyMember->age);

                        $qualifications = explode(',', $familyMember->qualification);

                        $degrees = explode(',', $familyMember->degree);

                        $notes = explode(',', $familyMember->note);

                    @endphp

                    @foreach ($names as $key => $member)
                        <div class="card mb-4">

                            <div class="card-header">

                                عضو {{ $key + 1 }}

                            </div>

                            <div class="card-body">

                                <div class="row g-3">

                                    <div class="col-md-4">

                                        <label>

                                            اسم

                                        </label>

                                        <input type="text" name="family_members[]" class="form-control"
                                            value="{{ $member }}">

                                    </div>

                                    <div class="col-md-2">

                                        <label>

                                            جنسیت

                                        </label>

                                        <select name="gender[]" class="form-select">

                                            <option value="male" {{ ($genders[$key] ?? '') == 'male' ? 'selected' : '' }}>

                                                Male

                                            </option>

                                            <option value="female" {{ ($genders[$key] ?? '') == 'female' ? 'selected' : '' }}>

                                                Female

                                            </option>

                                        </select>

                                    </div>

                                    <div class="col-md-3">

                                        <label>

                                            تاریخ تولد

                                        </label>

                                        <input type="date" name="birth_date[]" class="form-control"
                                            value="{{ $birthDates[$key] ?? '' }}">

                                    </div>

                                    <div class="col-md-3">

                                        <label>

                                            سن

                                        </label>

                                        <input type="number" name="age[]" class="form-control"
                                            value="{{ $ages[$key] ?? '' }}">

                                    </div>

                                    <div class="col-md-6">

                                        <label>

                                            تحصیلات

                                        </label>

                                        <input type="text" name="qualification[]" class="form-control"
                                            value="{{ $qualifications[$key] ?? '' }}">

                                    </div>

                                    <div class="col-md-6">

                                        <label>

                                            درجه

                                        </label>

                                        <input type="text" name="degree[]" class="form-control"
                                            value="{{ $degrees[$key] ?? '' }}">

                                    </div>

                                    <div class="col-12">

                                        <label>

                                            یادداشت

                                        </label>

                                        <textarea name="note[]" class="form-control" rows="2">{{ $notes[$key] ?? '' }}</textarea>

                                    </div>

                                </div>

                            </div>

                        </div>
                    @endforeach

                    <div class="text-end">

                        <button class="btn btn-primary">

                            ذخیره

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
