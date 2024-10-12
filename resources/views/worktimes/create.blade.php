<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Worktime_Register') }}
        </h2>
    </x-slot>

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

                    {{-- 勤務時間登録フォーム --}}
                    <form method="POST" action="{{ route('worktimes.store', $employee->id) }}">
                        @csrf

                        <div class="form-control my-4">
                            <label for="date" class="label">
                                <span class="label-text">日付</span>
                            </label>
                            <input type="hidden" name="date" value="{{ $date }}" class="input input-bordered w-full">
                            <input type="text" value="{{ $date }}" class="input input-bordered w-full" disabled>
                        </div>

                        <div class="form-control my-4">
                            <label for="start_date" class="label">
                                <span class="label-text">出勤時刻</a></span>
                            </label>
                            <input type="time" name="start_date" class="input input-bordered w-full">
                        </div>

                        <div class="form-control my-4">
                            <label for="end_date" class="label">
                                <span class="label-text">退勤時刻</a></span>
                            </label>
                            <input type="time" name="end_date" class="input input-bordered w-full">
                        </div>

                        <div class="form-control my-4">
                            <label for="rest" class="label">
                                <span class="label-text">休憩時間</a></span>
                            </label>
                            <input type="number" name="rest" class="input input-bordered w-full" step="0.01" min="0">
                        </div>

                        <div class="form-control my-4">
                            <label for="note" class="label">
                                <span class="label-text">備考</a></span>
                            </label>
                            <input type="text" name="note" class="input input-bordered w-full">
                        </div>

                        <button type="submit" class="btn btn-warning mt-3">登録する　</button>
                        <button type="button" class="btn btn-neutral mt-3" onclick="window.location='{{ route('employees.show', $employee->id) }}'">戻る　</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
