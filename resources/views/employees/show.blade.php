<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee_Show') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <button type="button" class="btn btn-neutral mt-3" onclick="window.location='{{ route('dashboard') }}'">戻る　</button>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="employee w-full">
                        <thead>
                            <tr>
                                <th>従業員ID</th>
                                <th>氏名</th>
                                <th>メールアドレス</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $employee->id }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-6 text-gray-900">
                    <div class="mt-6">
                        <h3 class="tomonth">{{ $year }} / {{ $month }} 月</h3>
                        {{-- カレンダー移動ボタン --}}
                        <div class="flex justify-between mb-4">
                            <a href="{{ route('employees.show', ['employee' => $employee->id, 'year' => $month == 1 ? $year - 1 : $year, 'month' => $month == 1 ? 12 : $month - 1]) }}" class="btn btn-success">前月を表示する</a>
                            <a href="{{ route('employees.show', ['employee' => $employee->id, 'year' => $month == 12 ? $year + 1 : $year, 'month' => $month == 12 ? 1 : $month + 1]) }}" class="btn btn-success">次月を表示する</a>
                        </div>
                            <table class="w-full">
                                <thead class="title">
                                    <tr>
                                        <th class="table_lines">日付</th>
                                        <th class="table_lines">出勤打刻</th>
                                        <th class="table_lines">退勤打刻</th>
                                        <th class="table_lines">休憩時間</th>
                                        <th class="table_lines">勤務時間</th>
                                        <th class="table_lines">残業時間</th>
                                        <th class="table_lines">備考</th>
                                        <th class="table_lines">登録</th>
                                        <th class="table_lines">編集</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dates as $dateData)
                                        @php
                                            // 土曜日
                                            $Saturday = $dateData['date']->format('N') == 6;
                                            // 日曜日
                                            $Sunday = $dateData['date']->format('N') == 7;
                                            $rowClass = '';

                                            if ($Saturday) {
                                                $rowClass = 'suturday';
                                            } elseif ($Sunday) {
                                                $rowClass = 'sunday';
                                            }
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            @php
                                                $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
                                                $weekdayIndex = $dateData['date']->format('w');
                                            @endphp
                                            <td class="table_lines">{{ $dateData['date']->format('j') }} ({{ $weekdays[$weekdayIndex] }})</td>
                                            <td class="table_lines">
                                                @if($dateData['worktime'] && $dateData['worktime']->start_date)
                                                    {{ $dateData['worktime']->start_date }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="table_lines">
                                                @if($dateData['worktime'] && $dateData['worktime']->end_date)
                                                    {{ $dateData['worktime']->end_date }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="table_lines">
                                                @if($dateData['worktime'] && $dateData['worktime']->rest)
                                                    {{ $dateData['worktime']->rest }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="table_lines">
                                                @if($dateData['worktime'] && $dateData['worktime']->start_date && $dateData['worktime']->end_date)
                                                    @php
                                                        //出勤打刻と退勤打刻の差を計算
                                                        $start = \Carbon\Carbon::parse($dateData['worktime']->start_date);
                                                        $end = \Carbon\Carbon::parse($dateData['worktime']->end_date);

                                                        //総労働時間（休憩時間を引く前）を10進法に換算
                                                        $totalWorkTime = $end->diffInMinutes($start) / 60;

                                                        //総労働時間から休憩時間を引く
                                                        $restTime = $dateData['worktime']->rest ? $dateData['worktime']->rest : 0;
                                                        $workTimeWithoutRest = $totalWorkTime - $restTime;

                                                        //残業時間を計算（8時間以上なら差を計算、8時間未満なら0と定義）
                                                        $overtime = max($workTimeWithoutRest - 8, 0);

                                                        //勤務時間を10進法で小数点以下2桁まで表示させる
                                                        $workTimeDecimal = number_format($workTimeWithoutRest, 2);

                                                        //残業時間を10進法で小数点以下2桁まで表示させる
                                                        $overtimeDecimal = number_format($overtime, 2);
                                                    @endphp
                                                    {{-- 勤務時間を表示する --}}
                                                    {{ $workTimeDecimal }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="table_lines">
                                                {{-- 残業時間を表示する --}}
                                                @if($dateData['worktime'] && $dateData['worktime']->start_date && $dateData['worktime']->end_date)
                                                    {{ $overtimeDecimal }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="table_lines">
                                                {{-- 備考を表示する --}}
                                                @if($dateData['worktime'] && $dateData['worktime']->note)
                                                    {{ $dateData['worktime']->note }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="table_lines">
                                                @if(!$dateData['worktime'])
                                                    <button type="button" class="btn btn-warning"
                                                        onclick="window.location='{{ route('worktimes.create', ['employeeId' => $employee->id]) }}?date={{ $dateData['date']->format('Y-m-d') }}'">
                                                        登録する
                                                    </button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="table_lines">
                                                @if($dateData['worktime'])
                                                    <button type="button" class="btn btn-info"
                                                        onclick="window.location='{{ route('worktimes.edit', ['employeeId' => $employee->id, 'worktimeId' => $dateData['worktime']->id]) }}?date={{ $dateData['date']->format('Y-m-d') }}'">
                                                        編集する
                                                    </button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
