<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee_Register') }}
        </h2>
    </x-slot>

    <script>
        function togglePasswordVisibility(passwordFieldId) {
            const passwordField = document.getElementById(passwordFieldId);
            const eyeIcon = document.getElementById(`eye-icon-${passwordFieldId}`);

            const isPasswordType = passwordField.type === "password";

            // パスワードマスクを切り替え
            passwordField.type = isPasswordType ? "text" : "password";

            // 目アイコンを切り替え
            eyeIcon.classList.toggle("fa-eye");
            eyeIcon.classList.toggle("fa-eye-slash");
        }
    </script>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- アラート設定 --}}
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- 従業員登録フォーム --}}
                    <form method="POST" action="{{ route('employees.store', $user->id) }}">
                        @csrf

                        <div class="form-control my-4">
                            <label for="name" class="label">
                                <span class="label-text">従業員氏名 <a class="must">*</a></span>
                            </label>
                            <input type="text" name="name" class="input input-bordered w-full">
                        </div>

                        <div class="form-control my-4">
                            <label for="email" class="label">
                                <span class="label-text">メールアドレス <a class="must">*</a></span>
                            </label>
                            <input type="email" name="email" class="input input-bordered w-full">
                        </div>

                        <div class="form-control my-4 relative">
                            <label for="password" class="label">
                                <span class="label-text">パスワード <a class="must">*</a></span>
                            </label>
                            <input type="password" name="password" class="input input-bordered w-full pr-10" id="password">
                            <span class="absolute right-3 top-3 cursor-pointer" onclick="togglePasswordVisibility('password')">
                                <i class="fas fa-eye" id="eye-icon-password"></i>
                            </span>
                        </div>

                        <div class="form-control my-4 relative">
                            <label for="password_confirmation" class="label">
                                <span class="label-text">パスワード再入力 <a class="must">*</a></span>
                            </label>
                            <input type="password" name="password_confirmation" class="input input-bordered w-full pr-10" id="password_confirmation">
                            <span class="absolute right-3 top-3 cursor-pointer" onclick="togglePasswordVisibility('password_confirmation')">
                                <i class="fas fa-eye" id="eye-icon-password_confirmation"></i>
                            </span>
                        </div>

                        <button type="submit" class="btn btn-warning mt-3">登録を続ける　</button>
                        <button type="button" class="btn btn-neutral mt-3" onclick="window.location='{{ route('dashboard') }}'">戻る　</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
