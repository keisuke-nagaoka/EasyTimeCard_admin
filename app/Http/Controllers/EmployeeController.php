<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Worktime;

use Illuminate\Http\Request;
use illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Carbon\Carbon;


class EmployeeController extends Controller
{
    public function index()
    {
        //idで管理者を取得
        $user = Auth::user();

        //ログイン中管理者以外はトップページに遷移させる
        if (Auth::user()) {

            //管理者が登録した従業員一覧を取得
            $employees = $user->employees;

            //管理者が登録したユーザーを一覧表示
            return view('dashboard', [
                'user' => $user,
                'employees' => $employees,
            ]);
        }

        return view('dashboard');
    }


    public function create()
    {
        //従業員の新規登録
        $employee = new Employee;

        //ログイン中管理者を取得
        $user = Auth::user();

        //従業員登録ページの表示
        return view('employees.create', [
            'employee' => $employee,
            'user' => $user,
        ]);
    }


    public function store(Request $request)
    {
        //バリデーション
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.Employee::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        //従業員の新規登録
        $employee = new Employee;

        // ログイン中管理者の取得
        $user = Auth::user();

        //入力内容をDBに保存
        $employee->admin_id = $user->id;
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->password = Hash::make($request->password);
        $employee->save();

        //DB保存後、従業員登録ページを表示
        return redirect()->route('employees.create')->with('success', '登録しました');
    }


    public function show(Request $request, $id)
    {
        // 年月をリアルタイムで取得
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        // 月の開始日と終了日を取得
        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // カレンダーの日付情報を生成
        $dates = [];
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
            $worktime = Worktime::where('employee_id', $id)
                ->whereDate('date', $date)
                ->first();

            $dates[] = [
                'date' => $date->copy(),
                'worktime' => $worktime,
            ];
        }

        //idで従業員を取得
        $employee = Employee::findOrFail($id);

        //idで指定の従業員ページを表示
        //ログイン中管理者以外はトップページに遷移させる
        if (Auth::id() === $employee->admin_id) {
            return view('employees.show', [
                'employee' => $employee,
                'dates' => $dates,
                'year' => $year,
                'month' => $month,
            ]);
        }

        return view('dashboard');
    }


    public function edit($id)
    {
        //idで従業員を取得
        $employee = Employee::findOrFail($id);

        //idで指定の従業員編集ページを表示
        //ログイン中管理者以外はトップページに遷移させる
        if (Auth::id() === $employee->admin_id) {
            return view('employees.edit', [
                'employee' => $employee,
            ]);
        }

        return view('dashboard');
    }


    public function update(Request $request, $id)
    {
        //バリデーション
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        //idで従業員を取得
        $employee = Employee::findOrFail($id);

        //ログイン中管理者以外はトップページに遷移させる
        if (Auth::id() === $employee->admin_id) {
            //入力内容をDBに保存
            $employee->name = $request->name;
            $employee->email = $request->email;
            //パスワード入力された場合のみ更新する
            if ($request->filled('password')) {
                $employee->password = Hash::make($request->password);
            }
            $employee->save();

            return redirect()->route('dashboard')->with('success', '従業員情報が更新されました。');
        }

        return view('dashboard');
    }


    public function destroy($id)
    {
        //idで従業員を取得
        $employee = Employee::findOrFail($id);

        //ログイン中管理者以外はトップページに遷移させる
        if (Auth::id() === $employee->admin_id) {
            //従業員に紐付く勤怠データを削除
            $employee->worktimes()->delete();
            //従業員を削除
            $employee->delete();

            return redirect('dashboard');
        }

        return view('dashboard');
    }


    public function handleQr(Request $request)
    {
        //読み取ったQRから従業員のIDを取得
        $employeeId = $request->query('id');

        //従業員が存在するか確認
        $employee = Employee::find($employeeId);

        //従業員が存在しない場合
        if (!$employee) {
            return redirect()->back()->with('error', '従業員が見つかりません。');
        }

        //勤務時間の記録にリダイレクトさせる
        return redirect()->route('worktimes.scan', ['id' => $employeeId]);
    }


    public function showScanForm()
    {
        //QRコードスキャンページの表示
        return view('worktimes.scan');
    }
}
