<?php

namespace App\Http\Controllers\User;

use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TodoController
{
    public function index()
    {
        $user = auth()->user();

        // =========================
        // 1. Show Tasks (Today + Old Pending)
        // =========================
        $todos = $user->todos()
            ->where(function ($q) {
                $q->whereDate('task_date', today())
                ->orWhere(function ($q2) {
                    $q2->where('is_completed', false)
                        ->whereDate('task_date', '<', today());
                });
            })
            ->orderByRaw("is_completed ASC")
            ->orderByDesc('task_date')
            ->limit(10)
            ->get();


        // =========================
        // 2. Today Summary
        // =========================
        $todayTodos = $user->todos()
            ->whereDate('task_date', today())
            ->get();

        $todayPending = $user->todos()
            ->where('is_completed', false)
            ->whereDate('task_date', '<=', today())
            ->count();

        $todayTotal = $todayTodos->count();
        $todayCompleted = $user->todos()->whereDate('completed_at', today())->count();
        $todayProgress = $todayPending
            ? round(($todayCompleted / $todayPending) * 100)
            : 0;


        // =========================
        // 3. Monthly Summary
        // =========================
        $monthlyTodos = $user->todos()
            ->whereMonth('task_date', now()->month)
            ->whereYear('task_date', now()->year)
            ->get();

        $monthlyTotal = $monthlyTodos->count();
        $monthlyCompleted = $monthlyTodos->where('is_completed', true)->count();


        // =========================
        // 4. Yearly Summary
        // =========================
        $yearlyTodos = $user->todos()
            ->whereYear('task_date', now()->year)
            ->get();

        $yearlyTotal = $yearlyTodos->count();
        $yearlyCompleted = $yearlyTodos->where('is_completed', true)->count();

        $month = request('month', now()->month);
        $year  = request('year', now()->year);

        $historyDate = Carbon::create($year, $month, 1);

        // MONTH FILTERED DATA
        $historyTodos = $user->todos()
            ->whereMonth('task_date', $month)
            ->whereYear('task_date', $year)
            ->get();

        $historyTotal = $historyTodos->count();

        $historyCompleted = $historyTodos->filter(function ($todo) {
            return $todo->is_completed &&
                $todo->completed_at &&
                Carbon::parse($todo->completed_at)->month == Carbon::parse($todo->task_date)->month &&
                Carbon::parse($todo->completed_at)->year == Carbon::parse($todo->task_date)->year;
        })->count();

        $historyPending = $historyTotal - $historyCompleted;

        $historyProgress = $historyTotal
            ? round(($historyCompleted / $historyTotal) * 100)
            : 0;

        // YEARS LIST
        $years = $user->todos()
            ->selectRaw('YEAR(task_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');


        // =========================
        // 5. Final Data Pack
        // =========================
        $taskData = [
            'todos' => $todos,

            'today' => [
                'total' => $todayTotal,
                'completed' => $todayCompleted,
                'pending' => $todayPending,
                'percentage' => $todayProgress,
            ],

            'monthly' => [
                'total' => $monthlyTotal,
                'completed' => $monthlyCompleted,
                'pending' => $monthlyTotal - $monthlyCompleted,
                'percentage' => $monthlyTotal ? round(($monthlyCompleted / $monthlyTotal) * 100) : 0,
            ],

            'yearly' => [
                'total' => $yearlyTotal,
                'completed' => $yearlyCompleted,
                'pending' => $yearlyTotal - $yearlyCompleted,
                'percentage' => $yearlyTotal ? round(($yearlyCompleted / $yearlyTotal) * 100) : 0,
            ],
            'history' => [
                'total' => $historyTotal,
                'completed' => $historyCompleted,
                'pending' => $historyPending,
                'percentage' => $historyProgress,
            ],

            'selectedMonth' => $month,
            'selectedYear' => $year,
            'years' => $years,
        ];

        return view('user.pages.dashboard', compact('taskData'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'task_date' => 'required|date',
            'priority' => 'required|in:low,medium,high'
        ]);

        auth()->user()->todos()->create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'task_date' => $request->task_date,
        ]);

        return back()->with('success', 'Task added');
    }
    public function toggle($id)
    {
        $todo = Todo::findOrFail($id);

        if ($todo->is_completed) {
            $todo->update([
                'is_completed' => false,
                'completed_at' => null
            ]);
        } else {
            $todo->update([
                'is_completed' => true,
                'completed_at' => now()
            ]);
        }

        return back();
    }
    public function summary()
    {
        $total = auth()->user()->todos()->whereDate('task_date', today())->count();
        $completed = auth()->user()->todos()
            ->whereDate('task_date', today())
            ->where('is_completed', true)
            ->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'percentage' => $total > 0 ? ($completed / $total) * 100 : 0
        ];
    }

    public function allTodos()
    {
        $user = auth()->user();

        $todos = $user->todos()
            ->orderBy('is_completed', 'asc')
            ->orderByDesc('task_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->through(function ($todo) {

                $isOld = Carbon::parse($todo->task_date)->lt(today());

                return [
                    'id' => $todo->id,
                    'sl' => null,

                    'created_at' => $todo->created_at->format('d M Y, h:i A'),

                    'completed_at' => $todo->completed_at
                        ? Carbon::parse($todo->completed_at)->format('d M Y, h:i A')
                        : null,

                    'title' => $todo->title,
                    'description' => $todo->description ?? '-',
                    'priority' => $todo->priority,

                    'status_text' => $todo->is_completed
                        ? 'Completed'
                        : ($isOld ? 'Overdue' : 'Pending'),

                    'badge' => $todo->is_completed
                        ? 'success'
                        : ($isOld ? 'warning' : 'info'),

                    'is_completed' => $todo->is_completed,
                ];
            });

        return view('user.pages.todos-history', compact('todos'));
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);

        $todo->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);

        return back()->with('success', 'Task updated successfully!');
    }
}
