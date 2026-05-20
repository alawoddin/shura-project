@extends('admin.dashboard')

@section('admin')



 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="container-fluid">

    <div class="card mt-3 shadow-sm">

        <div class="card-header bg-dark text-white">
            اضافه کردن اعضای فامیل
        </div>

        <div class="card-body">

            <form action="{{ route('store.users.family') }}"
                  method="POST"
                  id="myForm">

                @csrf

                <input type="hidden"
                       name="user_id"
                       value="{{ $user->id }}">

                <div id="family-wrapper">

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

                </div>

                <button type="button"
                        class="btn btn-success mb-3"
                        id="add-family">

                    + اضافه کردن عضو

                </button>

                <br>

                <button class="btn btn-primary">

                    ذخیره

                </button>

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
        });
    </script>

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
