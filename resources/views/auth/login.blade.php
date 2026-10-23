<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ورود به سیستم مدیریت شورا</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f7f6;
        }

        .login-card {
            border: 1px solid #e2e8e5;
            box-shadow: 0 12px 35px rgba(20, 50, 35, 0.08);
        }

        .login-button {
            background: #176b4d;
            transition: 0.2s;
        }

        .login-button:hover {
            background: #12563e;
        }

        .login-input:focus {
            border-color: #176b4d;
            box-shadow: 0 0 0 3px rgba(23, 107, 77, 0.12);
            outline: none;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen px-4 py-8">

    <div class="w-full max-w-md">

        <div class="login-card bg-white rounded-2xl p-6 sm:p-8">

            {{-- عنوان --}}
            <div class="text-center mb-8">
                <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center"
                     style="background:#e5f3eb;color:#176b4d;">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="30" height="30" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-5h6v5M9 9h.01M15 9h.01M9 12h.01M15 12h.01"/>
                    </svg>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    سیستم مدیریت شورا
                </h1>

                <p class="text-gray-500 mt-3 text-sm">
                    برای ورود به حساب کاربری خود، اطلاعات را وارد کنید.
                </p>
            </div>

            {{-- پیام خطا --}}
            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    اطلاعات ورود صحیح نیست. لطفاً دوباره بررسی کنید.
                </div>
            @endif

            {{-- فرم ورود --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- ایمیل --}}
                <div class="mb-5">
                    <label for="email"
                           class="block text-sm font-semibold text-gray-700 mb-2">
                        ایمیل
                    </label>

                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        dir="ltr"
                        placeholder="name@example.com"
                        class="login-input w-full px-4 py-3 border border-gray-300 rounded-lg text-left transition">

                    @error('email')
                        <p class="text-red-600 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- رمز عبور --}}
                <div class="mb-4">
                    <label for="password"
                           class="block text-sm font-semibold text-gray-700 mb-2">
                        رمز عبور
                    </label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        dir="ltr"
                        placeholder="رمز عبور خود را وارد کنید"
                        class="login-input w-full px-4 py-3 border border-gray-300 rounded-lg transition">

                    @error('password')
                        <p class="text-red-600 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- مرا به خاطر بسپار و فراموشی رمز --}}
                <div class="flex flex-wrap justify-between items-center gap-3 mt-4 mb-6">

                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            class="rounded border-gray-300"
                            style="accent-color:#176b4d;"
                            {{ old('remember') ? 'checked' : '' }}>

                        مرا به خاطر بسپار
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm font-medium hover:underline"
                           style="color:#176b4d;">
                            رمز عبور را فراموش کرده‌اید؟
                        </a>
                    @endif

                </div>

                {{-- دکمه ورود --}}
                <button
                    type="submit"
                    class="login-button w-full text-white py-3 rounded-lg font-semibold">
                    ورود به سیستم
                </button>

                {{-- ثبت‌نام --}}
                @if (Route::has('register'))
                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600">
                            حساب کاربری ندارید؟
                            <a href="{{ route('register') }}"
                               class="font-semibold hover:underline"
                               style="color:#176b4d;">
                                ثبت‌نام کنید
                            </a>
                        </p>
                    </div>
                @endif

            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-5">
            سیستم مدیریت شورا
        </p>

    </div>

</body>
</html>