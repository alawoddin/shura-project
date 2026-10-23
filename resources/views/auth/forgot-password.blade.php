<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>فراموشی رمز عبور | سیستم مدیریت شورا</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            /* font-family: Tahoma, Arial, sans-serif; */
            background: #f5f7f6;
        }

        .forgot-card {
            border: 1px solid #e2e8e5;
            box-shadow: 0 12px 35px rgba(20, 50, 35, 0.08);
        }

        .forgot-button {
            background: #176b4d;
            transition: 0.2s;
        }

        .forgot-button:hover {
            background: #12563e;
        }

        .forgot-input:focus {
            border-color: #176b4d;
            box-shadow: 0 0 0 3px rgba(23, 107, 77, 0.12);
            outline: none;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen px-4 py-8">

    <div class="w-full max-w-md">

        <div class="forgot-card bg-white rounded-2xl p-6 sm:p-8">

            {{-- عنوان --}}
            <div class="text-center mb-8">

                <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center"
                     style="background:#e5f3eb;color:#176b4d;">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="30" height="30" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 11a9 9 0 0118 0v3a3 3 0 01-3 3h-1v-6h4M3 11v3a3 3 0 003 3h1v-6H3m7 7h4"/>
                    </svg>

                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    فراموشی رمز عبور
                </h1>

                <p class="text-gray-500 mt-3 text-sm leading-7">
                    ایمیل خود را وارد کنید تا لینک بازنشانی رمز عبور برایتان ارسال شود.
                </p>
            </div>

            {{-- پیام وضعیت --}}
            @if (session('status'))
                <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            {{-- فرم فراموشی رمز عبور --}}
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-6">

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
                        class="forgot-input w-full px-4 py-3 border border-gray-300 rounded-lg text-left transition">

                    @error('email')
                        <p class="text-red-600 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- دکمه ارسال لینک --}}
                <button
                    type="submit"
                    class="forgot-button w-full text-white py-3 rounded-lg font-semibold">
                    ارسال لینک بازنشانی رمز عبور
                </button>

                {{-- بازگشت به ورود --}}
                <div class="text-center mt-6">
                    <a href="{{ route('login') }}"
                       class="text-sm font-semibold hover:underline"
                       style="color:#176b4d;">
                        بازگشت به صفحه ورود
                    </a>
                </div>

            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-5">
            سیستم مدیریت شورا
        </p>

    </div>

</body>
</html>