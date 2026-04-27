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

                                    <small class="text-muted">
                                        {{ $todo->description ?? 'No description' }}
                                    </small>

                                    @if($isOld && !$todo->is_completed)
                                        <br>
                                        <small class="text-warning">Overdue task</small>
                                    @endif
                                </div>

                                <form action="{{ route('user.todos.toggle', $todo->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm {{ $todo->is_completed ? 'btn-success' : 'btn-outline-secondary' }}">
                                        {{ $todo->is_completed ? 'Complete' : 'Mark as Complete' }}
                                    </button>
                                </form>

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
                    <input type="text" name="title" class="form-control mb-2" placeholder="Task title" required>
                    <textarea name="description" class="form-control" placeholder="Description"></textarea>
                    <input type="hidden" name="task_date" value="{{ date('Y-m-d') }}">
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection
