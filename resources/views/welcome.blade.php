<x-guest-layout>
    <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg mx-auto max-w-md">
        <div class="p-6">
            <div class="information text-center">
                <p>こちらはEasyTimeCardの管理者ページです。</p>
                <p class="auth mt-3">管理者登録はこちら</p>
                <button type="button" class="btn btn-primary mt-3" onclick="window.location='{{ route('register') }}'">管理者登録　</button>
                <p class="auth mt-3">ログインはこちら</p>
                <button type="button" class="btn btn-neutral mt-3" onclick="window.location='{{ route('login') }}'">管理者ログイン　</button>
                <p class="auth mt-3">勤怠スキャンはこちら</p>
                <button type="button" class="btn btn-warning mt-3" onclick="window.location='{{ route('worktimes.scan.form') }}'">勤怠スキャン　</button>
            </div>
        </div>
    </div>
</x-guest-layout>
