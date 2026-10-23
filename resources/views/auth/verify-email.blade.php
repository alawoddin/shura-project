<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>تأیید ایمیل | سیستم مدیریت شورا</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f7f6;
        }

        .verify-card {
            border: 1px solid #e2e8e5;
            box-shadow: 0 12px 35px rgba(20, 50, 35, 0.08);
        }

        .verify-button {
            background: #176b4d;
            transition: 0.2s;
        }

        .verify-button:hover {
            background: #12563e;
        }

        .verify-button:focus-visible,
        .logout-button:focus-visible {
            outline: 3px solid rgba(23, 107, 77, 0.2);
            outline-offset: 2px;
        }

        .logout-button {
            color: #176b4d;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen px-4 py-8">

    <div class="w-full max-w-md">

        <div class="verify-card bg-white rounded-2xl p-6 sm:p-8">

            {{-- عنوان --}}
            <div class="text-center mb-8">

                <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center"
                     style="background:#e5f3eb;color:#176b4d;">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="30" height="30" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor"
                         stroke-width="1.8">
                        <rect x="3" y="5" width="18" height="14" rx="2"
                              stroke-linecap="round" stroke-linejoin="round"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m3 7 9 6 9-6"/>
                    </svg>

                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    تأیید ایمیل
                </h1>

                <p class="text-gray-500 mt-3 text-sm leading-7">
                    از ثبت‌نام شما سپاسگزاریم! برای فعال‌سازی حساب کاربری،
                    روی لینک تأییدی که به ایمیل شما ارسال شده است کلیک کنید.
                    اگر ایمیلی دریافت نکرده‌اید، می‌توانید دوباره درخواست ارسال کنید.
                </p>
            </div>

            {{-- پیام ارسال مجدد --}}
            @if (session('status') == 'verification-link-sent')
                <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700 leading-6">
                    لینک تأیید جدید به آدرس ایمیلی که هنگام ثبت‌نام وارد کرده‌اید ارسال شد.
                </div>
            @endif

            {{-- خطاها --}}
            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- ارسال مجدد ایمیل تأیید --}}
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <button
                    type="submit"
                    class="verify-button w-full text-white py-3 rounded-lg font-semibold">
                    ارسال مجدد ایمیل تأیید
                </button>
            </form>

            {{-- خروج از حساب --}}
            <form method="POST" action="{{ route('logout') }}" class="mt-5 text-center">
                @csrf

                <button
                    type="submit"
                    class="logout-button text-sm font-semibold hover:underline">
                    خروج از حساب کاربری
                </button>
            </form>

        </div>

        <p class="text-center text-xs text-gray-400 mt-5">
            سیستم مدیریت شورا
        </p>

    </div>

</body>
</html>