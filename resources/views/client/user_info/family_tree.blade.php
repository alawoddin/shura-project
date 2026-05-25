@extends('client.client_dashboard')

@section('client')

<div class="tree-page">

    <div class="family-tree">

        {{-- Grand Father --}}
        <div class="member">

            <img src="{{ url('upload/no_image.jpg') }}">

            <h3>{{ $uinfo->grand_father_name }}</h3>

            <p>Grand Father</p>

        </div>

        <div class="line"></div>

        {{-- Father --}}
        <div class="member">

            <img src="{{ url('upload/no_image.jpg') }}">

            <h3>{{ $uinfo->father_name }}</h3>

            <p>Father</p>

        </div>

        <div class="line"></div>

        {{-- User --}}
        <div class="member active-member">

            <img src="{{ (!empty($uinfo->photo))
            ? url('upload/user_images/'.$uinfo->photo)
            : url('upload/no_image.jpg') }}">

            <h3>{{ $uinfo->name }}</h3>

            <p>User</p>

        </div>

    </div>

</div>

<style>

.tree-page{
    min-height:100vh;
    background:linear-gradient(180deg,#f8fafc,#e2e8f0);
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px;
}

.family-tree{
    display:flex;
    flex-direction:column;
    align-items:center;
}

.member{
    text-align:center;
    position:relative;
}

.member img{
    width:130px;
    height:130px;
    border-radius:50%;
    object-fit:cover;
    border:6px solid white;
    box-shadow:0 10px 30px rgba(0,0,0,0.15);
    transition:.3s;
}

.member img:hover{
    transform:scale(1.08);
}

.member h3{
    margin-top:15px;
    font-size:24px;
    color:#0f172a;
}

.member p{
    color:#64748b;
    font-size:15px;
}

.line{
    width:4px;
    height:100px;
    background:linear-gradient(to bottom,#2563eb,#7c3aed);
    margin:10px 0;
    border-radius:20px;
    position:relative;
}

.line::after{
    content:'';
    position:absolute;
    bottom:-8px;
    left:50%;
    transform:translateX(-50%);
    width:18px;
    height:18px;
    background:#7c3aed;
    border-radius:50%;
}

.active-member img{
    border-color:#7c3aed;
    box-shadow:0 0 40px rgba(124,58,237,0.4);
}

</style>

@endsection