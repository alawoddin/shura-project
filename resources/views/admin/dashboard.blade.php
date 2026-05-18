<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('backend/images/favicon.png') }}">
    <title>Home - CopyGen - AI Writer &amp; Copywriting Landing Page HTML Template.</title>
    <link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >




</head>

<body class="nk-body ">
    <div class="nk-app-root " data-sidebar-collapse="lg">
        <div class="nk-main">


            @include('admin.body.sidebar')

            <!-- .nk-sidebar -->
            <div class="nk-wrap">

                @include('admin.body.mobile')

                <div class="nk-content">
                    <div class="container-xl">
                        @yield('admin')
                    </div>
                </div>

                @include('admin.body.footer')

            </div>
        </div>
    </div>

      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

      
    <script src="{{ asset('backend/assets/js/bundle.js') }}"></script>
    <script src="{{ asset('backend/assets/js/scripts.js') }}"></script>

     <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
 @if(Session::has('message'))
 var type = "{{ Session::get('alert-type','info') }}"
 switch(type){
    case 'info':
    toastr.info(" {{ Session::get('message') }} ");
    break;

    case 'success':
    toastr.success(" {{ Session::get('message') }} ");
    break;

    case 'warning':
    toastr.warning(" {{ Session::get('message') }} ");
    break;

    case 'error':
    toastr.error(" {{ Session::get('message') }} ");
    break; 
 }
 @endif 
</script>



</body>

</html>
