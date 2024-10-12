<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Worktime;

use Illuminate\Http\Request;
use illuminate\Support\Facades\Auth;


class WorktimeController extends Controller
{
    public function index($id)
    {
        //ログイン中管理者の従業員を取得
        $employees = Auth::user()->employees;

        //idで従業員を取得
        $employee = Employee::findOrFail($id);

        //admin_idを代入
        $owner = $employee->admin_id;

        //ログイン中管理者をidで取得
        $user = Auth::user()->id;

        //ログイン中管理者以外はトップページに遷移させる
        if ($owner != $user) {
            return redirect('/');
        }

        //従業員の勤務時間を取得
        $worktimes = $employee->worktimes;

        //勤務時間一覧ページの表示
        return view('employees.show',[
            'employees' => $employees,
            'employee' => $employee,
            'worktimes' => $worktimes,
        ]);
    }


    public function create(Request $request, $id)
    {
        //idで従業員を取得
        $employee = Employee::findOrFail($id);

        //admin_idを代入
        $owner = $employee->admin_id;

        //ログイン中管理者をidで取得
        $user = Auth::user()->id;

        //ログイン中管理者以外はトップページに遷移させる
        if ($owner != $user) {
            return redirect('/');
        }

        //日付を取得
        $date = $request->query('date');

        //勤務時間の新規登録
        $worktime = new Worktime;

        //勤務時間登録ページの表示
        return view('worktimes.create', [
            'worktime' => $worktime,
            'employee' => $employee,
            'date' => $date,
        ]);
    }


    public function store(Request $request, $id)
    {
        //idで従業員を取得
        $employee = Employee::findOrFail($id);

        //バリデーション
        $request->validate([
            'date' => 'date|nullable',
            'start_date' => 'date_format:H:i|nullable',
            'end_date' => 'date_format:H:i|nullable',
            'rest' => 'numeric|nullable',
            'note' => 'string|nullable',
        ]);

        //勤務時間の新規作成
        $worktime = new Worktime;

        //入力内容をDBに保存
        $worktime->employee_id = $employee->id;
        $worktime->date = $request->date;
        $worktime->start_date = $request->start_date;
        $worktime->end_date = $request->end_date;
        $worktime->rest = $request->rest;
        $worktime->note = $request->note;
        $worktime->save();

        //DB保存後、勤務時間一覧ページを表示
        return redirect()->route('employees.show', $employee->id)->with('success', '登録しました');
    }


    public function edit(Request $request, $employeeId, $worktimeId)
    {
        //idで従業員を取得
        $employee = Employee::findOrFail($employeeId);

        //idで勤務時間を取得
        $worktime = Worktime::findOrFail($worktimeId);

        //日付を取得
        $date = $request->query('date');

        //idで指定の勤務時間編集ページを表示
        //ログイン中管理者以外はトップページに遷移させる
        if (Auth::id() === $employee->admin_id) {
            return view('worktimes.edit', [
            'employee' => $employee,
            'worktime' => $worktime,
            'date' => $date,
            ]);
        }

        return view('dashboard');
    }


    public function update(Request $request, $employeeId, $worktimeId)
    {
        //バリデーション
        $request->validate([
            'date' => 'date|nullable',
            'start_date' => 'date_format:H:i|nullable',
            'end_date' => 'date_format:H:i|nullable',
            'rest' => 'numeric|nullable',
            'note' => 'string|nullable',
        ]);

        //idで従業員を取得
        $employee = Employee::findOrFail($employeeId);

        //idで勤務時間を取得
        $worktime = Worktime::findOrFail($worktimeId);

        //編集内容をDBへ保存し勤務時間一覧ページを表示
        //ログイン中管理者以外はトップページに遷移させる
        if (Auth::id() === $employee->admin_id) {
            $worktime->start_date = $request->start_date;
            $worktime->end_date = $request->end_date;
            $worktime->rest = $request->rest;
            $worktime->note = $request->note;
            $worktime->save();

            return redirect()->route('employees.show', [$employee->id, $worktime->id]);
        }

        return view('dashboard');
    }
}
