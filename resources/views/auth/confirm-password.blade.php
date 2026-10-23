<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>تأیید رمز عبور | سیستم مدیریت شورا</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            /* font-family: Tahoma, Arial, sans-serif; */
            background: #f5f7f6;
        }

        .confirm-card {
            border: 1px solid #e2e8e5;
            box-shadow: 0 12px 35px rgba(20, 50, 35, 0.08);
        }

        .confirm-button {
            background: #176b4d;
            transition: 0.2s;
        }

        .confirm-button:hover {
            background: #12563e;
        }

        .confirm-input:focus {
            border-color: #176b4d;
            box-shadow: 0 0 0 3px rgba(23, 107, 77, 0.12);
            outline: none;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen px-4 py-8">

    <div class="w-full max-w-md">

        <div class="confirm-card bg-white rounded-2xl p-6 sm:p-8">

            {{-- عنوان --}}
            <div class="text-center mb-8">

                <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center"
                     style="background:#e5f3eb;color:#176b4d;">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="30" height="30" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-7a2 2 0 00-2-2H6a2 2 0 00-2 2v7a2 2 0 002 2zm2-9V7a4 4 0 118 0v3"/>
                    </svg>

                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    تأیید رمز عبور
                </h1>

                <p class="text-gray-500 mt-3 text-sm leading-7">
                    این بخش امن است. لطفاً برای ادامه، رمز عبور خود را تأیید کنید.
                </p>
            </div>

            {{-- پیام وضعیت --}}
            @if (session('status'))
                <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            {{-- خطاها --}}
            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    لطفاً رمز عبور واردشده را بررسی کنید.
                </div>
            @endif

            {{-- فرم تأیید رمز عبور --}}
            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="mb-6">

                    <label for="password"
                           class="block text-sm font-semibold text-gray-700 mb-2">
                        رمز عبور
                    </label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autofocus
                        autocomplete="current-password"
                        dir="ltr"
                        placeholder="رمز عبور خود را وارد کنید"
                        class="confirm-input w-full px-4 py-3 border border-gray-300 rounded-lg transition">

                    @error('password')
                        <p class="text-red-600 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- دکمه تأیید --}}
                <button
                    type="submit"
                    class="confirm-button w-full text-white py-3 rounded-lg font-semibold">
                    تأیید و ادامه
                </button>

            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-5">
            سیستم مدیریت شورا
        </p>

    </div>

</body>
</html>