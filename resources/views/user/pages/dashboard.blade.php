@extends('user.layouts.app')

@section('userContent')

<div class="container-fluid">

    <div class="row">

        <!-- LEFT SIDE (CARDS) -->
        <div class="col-xl-4 col-lg-5 col-md-6">

            <!-- TODAY -->
            <div class="card mb-3">
                <div class="card-body text-center">

                    <h5>Today's Tasks</h5>

                    <h2 class="text-primary">{{ $taskData['today']['total'] }}</h2>

                    <div class="d-flex justify-content-around mt-3">
                        <div>
                            <h6 class="text-success">{{ $taskData['today']['completed'] }}</h6>
                            <small>Completed</small>
                        </div>
                        <div>
                            <h6 class="text-danger">{{ $taskData['today']['pending'] }}</h6>
                            <small>Pending</small>
                        </div>
                    </div>

                    <div class="progress mt-3">
                        <div class="progress-bar bg-success"
                             style="width: {{ $taskData['today']['percentage'] }}%">
                             {{ $taskData['today']['percentage'] }}%
                        </div>
                    </div>

                </div>
            </div>

            <!-- MONTHLY -->
            <div class="card mb-3">
                <div class="card-body text-center">

                    <h5>Monthly Tasks</h5>

                    <h2 class="text-primary">{{ $taskData['monthly']['total'] }}</h2>

                    <div class="d-flex justify-content-around mt-3">
                        <div>
                            <h6 class="text-success">{{ $taskData['monthly']['completed'] }}</h6>
                            <small>Completed</small>
                        </div>
                        <div>
                            <h6 class="text-danger">
                                {{ $taskData['monthly']['total'] - $taskData['monthly']['completed'] }}
                            </h6>
                            <small>Pending</small>
                        </div>
                    </div>

                    <div class="progress mt-3">
                        <div class="progress-bar bg-info"
                             style="width: {{ $taskData['monthly']['percentage'] }}%">
                             <span>{{ $taskData['monthly']['percentage'] }}%</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- YEARLY -->
            <div class="card">
                <div class="card-body text-center">

                    <h5>Yearly Tasks</h5>

                    <h2 class="text-primary">{{ $taskData['yearly']['total'] }}</h2>

                    <div class="d-flex justify-content-around mt-3">
                        <div>
                            <h6 class="text-success">{{ $taskData['yearly']['completed'] }}</h6>
                            <small>Completed</small>
                        </div>
                        <div>
                            <h6 class="text-danger">
                                {{ $taskData['yearly']['total'] - $taskData['yearly']['completed'] }}
                            </h6>
                            <small>Pending</small>
                        </div>
                    </div>

                    <div class="progress mt-3">
                        <div class="progress-bar bg-warning"
                             style="width: {{ $taskData['yearly']['percentage'] }}%">
                             <span>{{ $taskData['yearly']['percentage'] }}%</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>


        <!-- RIGHT SIDE (TODO LIST) -->
        <div class="col-xl-8 col-lg-7 col-md-6 mt-4 mt-md-0">
            <div class="card h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Pending & Today's Tasks</h4>

                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addTodoModal">
                            + Add Task
                        </button>
                    </div>

                    <div class="preview-list">

                        @forelse($taskData['todos'] as $todo)

                            @php
                                $isOld = \Carbon\Carbon::parse($todo->task_date)->lt(today());
                            @endphp

                            <div class="preview-item border-bottom d-flex justify-content-between align-items-center py-2">

                                <div>

                                    <h6 class="
                                        mb-1
                                        {{ $todo->is_completed ? 'text-success text-decoration-line-through' : '' }}
                                        {{ $isOld && !$todo->is_completed ? 'text-warning' : '' }}
                                    ">
                                        {{ $todo->title }}
                                    </h6>

                                    @php
                                        $desc = $todo->description ?? 'No description';
                                    @endphp

                                    <small class="text-muted d-flex align-items-center gap-1">

                                        {{ \Illuminate\Support\Str::limit($desc, 50) }}

                                        @if(strlen($desc) > 50)
                                            <button type="button"
                                                class="btn btn-sm p-0 text-primary border-0 bg-transparent"
                                                data-toggle="modal"
                                                data-target="#viewTodoModal{{ $todo->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif

                                    </small>

                                    {{-- PRIORITY --}}
                                    <div class="mt-1">
                                        <span class="badge
                                            {{ $todo->priority == 'high' ? 'bg-danger' :
                                            ($todo->priority == 'medium' ? 'bg-primary' : 'bg-info') }}">
                                            {{ ucfirst($todo->priority) }}
                                        </span>
                                    </div>

                                    @if($isOld && !$todo->is_completed)
                                        <small class="text-warning d-block mt-1">
                                            Overdue task
                                        </small>
                                    @endif

                                </div>

                                <div class="d-flex align-items-center gap-2">

                                    {{-- TOGGLE --}}
                                    <form action="{{ route('user.todos.toggle', $todo->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm {{ $todo->is_completed ? 'btn-success' : 'btn-outline-secondary' }}">
                                            {{ $todo->is_completed ? 'Done' : 'Mark' }}
                                        </button>
                                    </form>

                                </div>

                            </div>

                            {{-- VIEW MODAL --}}
                            <div class="modal fade" id="viewTodoModal{{ $todo->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5>Task Details</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>

                                        <div class="modal-body">

                                            <p><strong>Title:</strong> {{ $todo->title }}</p>
                                            <p><strong>Description:</strong> {{ $todo->description ?? '-' }}</p>
                                            <p><strong>Date:</strong> {{ $todo->task_date }}</p>

                                            <p>
                                                <strong>Priority:</strong>
                                                <span class="badge
                                                    {{ $todo->priority == 'high' ? 'bg-danger' :
                                                    ($todo->priority == 'medium' ? 'bg-primary' : 'bg-secondary') }}">
                                                    {{ ucfirst($todo->priority) }}
                                                </span>
                                            </p>

                                            <p>
                                                <strong>Status:</strong>
                                                <span class="badge {{ $todo->is_completed ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ $todo->is_completed ? 'Completed' : 'Pending' }}
                                                </span>
                                            </p>

                                        </div>

                                    </div>
                                </div>
                            </div>

                        @empty
                            <div class="text-center py-5">
                                <p class="text-muted">No tasks found</p>
                            </div>
                        @endforelse

                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- HISTORY PERFORMANCE -->
    <div class="card mt-4">
        <div class="card-body text-center">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Performance History</h5>

                <form method="GET" class="d-flex gap-2">

                    <!-- MONTH -->
                    <select name="month"
                            class="form-control form-control-sm text-light"
                            onchange="this.form.submit()">

                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}"
                                {{ $taskData['selectedMonth'] == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endfor

                    </select>

                    <!-- YEAR -->
                    <select name="year"
                            class="form-control form-control-sm text-light"
                            onchange="this.form.submit()">

                        @foreach($taskData['years'] as $y)
                            <option value="{{ $y }}"
                                {{ $taskData['selectedYear'] == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach

                    </select>

                </form>
            </div>

            <h2 class="text-primary">
                {{ $taskData['history']['total'] }}
            </h2>

            <div class="d-flex justify-content-around mt-3">
                <div>
                    <h6 class="text-success">
                        {{ $taskData['history']['completed'] }}
                    </h6>
                    <small>Completed</small>
                </div>

                <div>
                    <h6 class="text-danger">
                        {{ $taskData['history']['pending'] }}
                    </h6>
                    <small>Pending</small>
                </div>
            </div>

            <div class="progress mt-3">
                <div class="progress-bar bg-info"
                    style="width: {{ $taskData['history']['percentage'] }}%">
                    {{ $taskData['history']['percentage'] }}%
                </div>
            </div>

        </div>
    </div>
</div>


{{-- MODAL --}}
<div class="modal fade" id="addTodoModal">
    <div class="modal-dialog">
        <form action="{{ route('user.todos.store') }}" method="POST">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add Task</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <input type="text" name="title" class="form-control text-light mb-2" placeholder="Task title" required>
                    <textarea name="description" class="form-control text-light" placeholder="Description"></textarea>
                    <input type="hidden" name="task_date" value="{{ date('Y-m-d') }}">

                    <div class="mt-2 mb-2">
                        <label>Priority</label>
                        <select name="priority" class="form-control text-light">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>


                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection
