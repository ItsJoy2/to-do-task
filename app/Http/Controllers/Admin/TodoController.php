<?php

namespace App\Http\Controllers\Admin;

use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TodoController
{

    public function index(Request $request)
    {
        // SELECTED MONTH/YEAR
        $selectedMonth = $request->month ?? now()->month;
        $selectedYear  = $request->year ?? now()->year;

        $selectedDate = Carbon::now();

        $users = User::where('role', '!=', 'admin')
            ->where('is_active', 1)
            ->with('todos')
            ->get();

        $userList = $users->map(function ($user) use ($selectedMonth, $selectedYear, $selectedDate) {

            $todos = $user->todos;

            // TODAY
            $todayTasks = $todos->filter(function ($todo) use ($selectedDate) {
                return $todo->task_date &&
                    Carbon::parse($todo->task_date)->isSameDay($selectedDate);
            });

            // OLD PENDING

            $oldPending = $todos->filter(function ($todo) use ($selectedDate) {
                return !$todo->is_completed &&
                    $todo->task_date &&
                    Carbon::parse($todo->task_date)->lt($selectedDate);
            });


            // ALL PENDING

            $pendingTasks = $todos->filter(function ($todo) {
                return !$todo->is_completed;
            });


            // COMPLETED TODAY

            $completedToday = $todos->filter(function ($todo) use ($selectedDate) {
                return $todo->completed_at &&
                    Carbon::parse($todo->completed_at)->isSameDay($selectedDate);
            });


            // MONTH FILTER (SELECTED)

            $monthTasks = $todos->filter(function ($todo) use ($selectedMonth, $selectedYear) {
                if (!$todo->task_date) return false;

                $d = Carbon::parse($todo->task_date);

                return $d->month == $selectedMonth && $d->year == $selectedYear;
            });

            $monthCompleted = $todos->filter(function ($todo) use ($selectedMonth, $selectedYear) {
                if (!$todo->completed_at) return false;

                $d = Carbon::parse($todo->completed_at);

                return $d->month == $selectedMonth && $d->year == $selectedYear;
            });


            // UI USE ONLY MONTH DATA

            $selectedTotal = $monthTasks->count();
            $selectedCompleted = $monthCompleted->count();

            $progress = $selectedTotal
                ? round(($selectedCompleted / $selectedTotal) * 100)
                : 0;

            return [
                'id' => $user->id,
                'name' => $user->name,

                'today_total' => $todayTasks->count(),
                'old_pending' => $oldPending->count(),
                'completed_today' => $completedToday->count(),

                'selected_total' => $selectedTotal,
                'selected_completed' => $selectedCompleted,
                'progress' => $progress,

                'pending_tasks' => $pendingTasks->map(function ($todo) {
                    return [
                        'title' => $todo->title,
                        'created_at' => $todo->created_at?->format('d M, h:i A'),
                    ];
                })->values(),
            ];
        });

        $topUser = $userList->sortByDesc('progress')->first();

        $years = Todo::selectRaw('YEAR(task_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $userData = [
            'date' => now()->format('d M Y'),
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'years' => $years,
            'topUser' => $topUser,
            'users' => $userList,
        ];

        return view('admin.dashboard', compact('userData'));
    }

    public function list(Request $request)
    {
        $search = $request->search;

        $tasks = Todo::with('user')
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('email', 'like', "%{$search}%");
                    });

                });

            })
            ->orderBy('is_completed', 'asc')
            ->latest()
            ->paginate(15);

        return view('admin.pages.tasks.index', compact('tasks'));
    }
}
