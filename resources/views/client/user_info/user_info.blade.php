@extends('client.client_dashboard')

@section('client')

<div class="profile-page">

    <div class="profile-card">

        {{-- Left Side --}}
        <div class="profile-left">

            <div class="profile-image">

                <img src="{{ (!empty($uinfo->photo))
                ? url('upload/user_images/'.$uinfo->photo)
                : url('upload/no_image.jpg') }}">

            </div>

            <h2>{{ $uinfo->name }}</h2>

            <p>{{ $uinfo->email }}</p>

            <span class="role-badge">
                {{ $uinfo->role }}
            </span>

        </div>

        {{-- Right Side --}}
        <div class="profile-right">

            <div class="info-grid">

                <div class="info-box">
                    <span>نام پدر</span>
                    <h5>{{ $uinfo->father_name }}</h5>
                </div>

                <div class="info-box">
                    <span>نام پدر کلان</span>
                    <h5>{{ $uinfo->grand_father_name }}</h5>
                </div>

                <div class="info-box">
                    <span>شاخه قومی</span>
                    <h5>{{ $uinfo->ethnicBranch->name ?? '' }}</h5>
                </div>

                <div class="info-box">
                    <span> اسم نماینده </span>
                    <h5>{{ $uinfo->representativeName->name ?? '' }}</h5>
                </div>

                <div class="info-box">
                    <span>ساحه مربوطه  </span>
                    <h5>{{ $uinfo->relevantField->name ?? '' }}</h5>
                </div>

                <div class="info-box">
                    <span>شماره </span>
                    <h5>{{ $uinfo->phone }}</h5>
                </div>

                <div class="info-box">
                    <span>جنسیت</span>
                    <h5>{{ $uinfo->gender }}</h5>
                </div>

                <div class="info-box">
                    <span>وضعیت مالی</span>
                    <h5>{{ $uinfo->financial_status }}</h5>
                </div>

                <div class="info-box">
                    <span>آدرس</span>
                    <h5>{{ $uinfo->address }}</h5>
                </div>

                <div class="info-box">
                    <span>Registration Date</span>
                    <h5>{{ $uinfo->registration }}</h5>
                </div>

                <div class="info-box">
                    <span>Status</span>
                    <h5>
                        @if($uinfo->is_active == 1)
                            فعال
                        @else
                            غیر فعال
                        @endif
                    </h5>
                </div>

            </div>

            <div class="profile-btns">

                <a href="{{ route('edit.profile') }}" class="edit-btn">
                    Edit Profile
                </a>

                <a href="{{ route('family.tree') }}" class="tree-btn">
                    Family Tree
                </a>

            </div>

        </div>

    </div>

</div>

<style>

.profile-page{
    padding:50px;
    background:#f4f7fe;
    min-height:100vh;
}

.profile-card{
    max-width:1200px;
    margin:auto;
    background:white;
    border-radius:30px;
    overflow:hidden;
    display:grid;
    grid-template-columns:350px 1fr;
    box-shadow:0 10px 40px rgba(0,0,0,0.08);
}

.profile-left{
    background:linear-gradient(180deg,#2563eb,#4f46e5);
    padding:50px 30px;
    text-align:center;
    color:white;
}

.profile-image img{
    width:170px;
    height:170px;
    border-radius:50%;
    object-fit:cover;
    border:6px solid rgba(255,255,255,0.3);
    margin-bottom:20px;
}

.profile-left h2{
    font-size:30px;
    margin-bottom:10px;
}

.profile-left p{
    opacity:.9;
    margin-bottom:20px;
}

.role-badge{
    background:white;
    color:#2563eb;
    padding:10px 25px;
    border-radius:30px;
    font-weight:bold;
}

.profile-right{
    padding:50px;
}

.info-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:25px;
}

.info-box{
    background:#f8fafc;
    padding:25px;
    border-radius:20px;
    transition:.3s;
    border:1px solid #e2e8f0;
}

.info-box:hover{
    transform:translateY(-5px);
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

.info-box span{
    color:#64748b;
    font-size:14px;
}

.info-box h5{
    margin-top:10px;
    color:#0f172a;
    font-size:18px;
}

.profile-btns{
    margin-top:40px;
    display:flex;
    gap:20px;
}

.edit-btn,
.tree-btn{
    text-decoration:none;
    padding:15px 30px;
    border-radius:15px;
    font-weight:bold;
    transition:.3s;
}

.edit-btn{
    background:#2563eb;
    color:white;
}

.tree-btn{
    background:#eef2ff;
    color:#4f46e5;
}

.edit-btn:hover,
.tree-btn:hover{
    transform:scale(1.05);
}

@media(max-width:900px){

    .profile-card{
        grid-template-columns:1fr;
    }

    .info-grid{
        grid-template-columns:1fr;
    }

}

</style>

@endsection