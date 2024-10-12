<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee_Index') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (isset($employees) && !$employees->isEmpty())
                    <p class="info">※従業員の氏名をクリックすると勤怠表が表示されます。</p>
                    <table class="w-full">
                        <thead class="title">
                            <tr>
                                <th class="table_lines">氏名</th>
                                <th class="table_lines">編集</th>
                                <th class="table_lines">削除</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                            <tr>
                                <td class="table_lines">
                                    <a class="btn btn-link" href="{{ route('employees.show', $employee->id) }}">{{ $employee->name }}</a>
                                </td>
                                <td class="table_lines">
                                    <button type="button" class="btn btn-info mt-3"
                                        onclick="window.location='{{ route('employees.edit', $employee->id) }}'">
                                        編集する
                                    </button>
                                </td>
                                <td class="table_lines">
                                    {{-- 従業員の削除 --}}
                                    <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" class="my-2">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-error mt-3"
                                            onclick="return confirm('{{ $employee->name }}　を削除します。{{ $employee->name }}に関連する勤怠データも同時に削除されます。本当によろしいですか？')">
                                            削除する
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <p class="empty mt-3">従業員が未登録です。</p>
                        <div class="create">
                            <a href="{{ route('employees.create') }}" class="btn btn-warning mt-3">従業員を登録する</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
