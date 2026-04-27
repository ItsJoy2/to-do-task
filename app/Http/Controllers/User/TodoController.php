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

        $todayTotal = $todayTodos->count();
        $todayCompleted = $user->todos()->whereDate('completed_at', today())->count();


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


        // =========================
        // 5. Final Data Pack
        // =========================
        $taskData = [
            'todos' => $todos,

            'today' => [
                'total' => $todayTotal,
                'completed' => $todayCompleted,
                'pending' => max(0, $todayTotal - $todayCompleted),
                'percentage' => $todayTotal
                    ? round(($todayCompleted / $todayTotal) * 100)
                    : 0,
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
        ];

        return view('user.pages.dashboard', compact('taskData'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'task_date' => 'required|date',
        ]);

        auth()->user()->todos()->create([
            'title' => $request->title,
            'description' => $request->description,
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

                    'status_text' => $todo->is_completed
                        ? 'Completed'
                        : ($isOld ? 'Pending' : 'Pending'),

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
        ]);

        return back()->with('success', 'Task updated successfully!');
    }
}
