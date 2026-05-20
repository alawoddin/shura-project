@extends('admin.dashboard')

@section('admin')

<div class="container-fluid">

    <div class="card mt-3 shadow-sm">

        <div class="card-header bg-dark text-white">
            ویرایش اعضای فامیل
        </div>

        <div class="card-body">

            <form action="{{ route('update.users.family') }}"
                  method="POST">

                @csrf

                <input type="hidden"
                       name="id"
                       value="{{ $familyMember->id }}">

                <input type="hidden"
                       name="user_id"
                       value="{{ $familyMember->user_id }}">

                <div id="family-wrapper">

                    @foreach(explode(',', $familyMember->name) as $member)

                    <div class="d-flex mb-3 family-item">

                        <input type="text"
                               name="family_members[]"
                               class="form-control"
                               value="{{ trim($member) }}"
                               required>

                        <button type="button"
                                class="btn btn-danger ms-2 remove-btn">

                            X

                        </button>

                    </div>

                    @endforeach

                </div>

                <button type="button"
                        class="btn btn-success mb-3"
                        id="add-family">

                    + اضافه کردن عضو

                </button>

                <br>

                <button class="btn btn-primary">

                    Update

                </button>

            </form>

        </div>

    </div>

</div>

<script>

document.getElementById('add-family')
.addEventListener('click', function(){

    let html = `
    
    <div class="d-flex mb-3 family-item">

        <input type="text"
               name="family_members[]"
               class="form-control"
               placeholder="اسم عضو فامیل"
               required>

        <button type="button"
                class="btn btn-danger ms-2 remove-btn">

            X

        </button>

    </div>
    `;

    document.getElementById('family-wrapper')
            .insertAdjacentHTML('beforeend', html);

});

document.addEventListener('click', function(e){

    if(e.target.classList.contains('remove-btn')){

        e.target.closest('.family-item').remove();

    }

});

</script>

@endsection