@extends('client.client_dashboard')

@section('client')

<div class="edit-page">

<form action="{{ route('update.profile') }}"
method="POST"
enctype="multipart/form-data">

@csrf

<div class="edit-card">

    <div class="header">
        <h2>ویرایش پروفایل</h2>
        <p>اطلاعات شخصی خود را به‌روزرسانی کنید</p>
    </div>

    <div class="photo-box">
        <img src="{{ (!empty($uinfo->photo))
        ? url('upload/user_images/'.$uinfo->photo)
        : url('upload/no_image.jpg') }}"
        id="showImage">

        <input type="file" name="photo" id="image">
    </div>

    <div class="form-grid">

        <div class="field">
            <label>نام</label>
            <input type="text" name="name" value="{{ $uinfo->name }}">
        </div>

        <div class="field">
            <label>نام پدر</label>
            <input type="text" name="father_name" value="{{ $uinfo->father_name }}">
        </div>

        <div class="field">
            <label>نام پدر کلان</label>
            <input type="text" name="grand_father_name" value="{{ $uinfo->grand_father_name }}">
        </div>

        <div class="field">
            <label>شاخه قومی</label>
            <input type="text" value="{{ $uinfo->ethnicBranch->name ?? '' }}" disabled>
        </div>

        <div class="field">
            <label>نام نماینده</label>
            <input type="text" value="{{ $uinfo->representativeName->name ?? '' }}" disabled>
        </div>

        <div class="field">
            <label>ناحیه مربوطه</label>
            <input type="text" value="{{ $uinfo->relevantField->name ?? '' }}" disabled>
        </div>

        <div class="field">
            <label>شماره تماس</label>
            <input type="text" name="phone" value="{{ $uinfo->phone }}">
        </div>

        <div class="field">
            <label>وضعیت مالی</label>
            <input type="text" name="financial_status" value="{{ $uinfo->financial_status }}">
        </div>

        <div class="field">
            <label>جنسیت</label>
            <select name="gender">
                <option value="male" {{ $uinfo->gender=='male'?'selected':'' }}>مرد</option>
                <option value="female" {{ $uinfo->gender=='female'?'selected':'' }}>زن</option>
            </select>
        </div>

        <div class="field full">
            <label>آدرس</label>
            <textarea name="address">{{ $uinfo->address }}</textarea>
        </div>

    </div>

    <button type="submit">ذخیره تغییرات</button>

</div>

</form>

</div>

<style>

.edit-page{
    padding:30px;
    background:transparent;
    font-family:system-ui;
}

/* ✔️ FIX اصلی اینجاست */
.edit-card{
    width:100%;
    max-width:1100px;   /* کمتر شد تا sidebar اذیت نشه */
    margin:0 auto;      /* مهم‌ترین خط برای جلوگیری از conflict */
    background:rgba(255,255,255,0.85);
    backdrop-filter: blur(12px);
    border-radius:26px;
    padding:40px;
    box-shadow:0 25px 70px rgba(0,0,0,0.08);
    border:1px solid rgba(255,255,255,0.5);
}

/* header */
.header{
    text-align:center;
    margin-bottom:25px;
}

.header h2{
    font-size:26px;
    font-weight:700;
    color:#111827;
}

.header p{
    color:#6b7280;
}

/* photo */
.photo-box{
    display:flex;
    flex-direction:column;
    align-items:center;
    margin-bottom:30px;
    gap:10px;
}

.photo-box img{
    width:160px;
    height:160px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid #6366f1;
}

.photo-box input{
    width:200px;
}

/* grid */
.form-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:20px;
}

/* inputs */
.field input,
.field select,
.field textarea{
    width:100%;
    padding:13px 14px;
    border-radius:12px;
    border:1px solid #e5e7eb;
    background:white;
    font-size:14px;
}

/* label */
.field label{
    font-size:13px;
    font-weight:600;
    color:#374151;
    margin-bottom:6px;
    display:block;
}

/* focus */
.field input:focus,
.field select:focus,
.field textarea:focus{
    border-color:#6366f1;
    box-shadow:0 0 0 3px rgba(99,102,241,0.15);
    outline:none;
}

.field textarea{
    min-height:120px;
}

.full{
    grid-column:span 2;
}

/* button */
button{
    width:100%;
    margin-top:25px;
    padding:14px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#6366f1,#8b5cf6);
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
}

button:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(99,102,241,0.3);
}

/* responsive */
@media(max-width:900px){
    .form-grid{
        grid-template-columns:1fr;
    }
    .full{
        grid-column:span 1;
    }
}

</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$('#image').change(function(e){
    var reader = new FileReader();
    reader.onload = function(e){
        $('#showImage').attr('src',e.target.result);
    }
    reader.readAsDataURL(e.target.files[0]);
});
</script>

@endsection