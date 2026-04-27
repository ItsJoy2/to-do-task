@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4 class="card-title mb-0">All Tasks</h4>

        {{-- ADD BUTTON --}}
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            + Add Task
        </button>
    </div>

    <div class="card-body">

        {{-- SEARCH --}}
        <div class="d-flex justify-content-end mb-3">

            <input type="text"
                id="searchInput"
                name="search"
                placeholder="Search email / task..."
                value="{{ request('search') }}"
                class="form-control form-control-sm w-25">

        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-striped table-hover mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Task</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($tasks as $task)
                    <tr>

                        <td>{{ $loop->iteration + $tasks->firstItem() - 1 }}</td>

                        <td>{{ $task->user->name ?? '-' }}</td>
                        <td>{{ $task->user->email ?? '-' }}</td>

                        <td>{{ $task->title }}</td>

                        <td>
                            <span class="badge {{ $task->priority == 'high' ? 'bg-danger' :
                                ($task->priority == 'medium' ? 'bg-primary' : 'bg-secondary') }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $task->is_completed ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $task->is_completed ? 'Done' : 'Pending' }}
                            </span>
                        </td>

                        <td class="d-flex gap-2">

                            {{-- VIEW --}}
                            <button class="btn btn-sm btn-info"
                                data-bs-toggle="modal"
                                data-bs-target="#viewModal{{ $task->id }}">
                                <i class="fas fa-eye"></i>
                            </button>

                            {{-- EDIT --}}
                            <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $task->id }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- DELETE --}}
                            <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalDeleteModal"
                                data-url="{{ route('admin.tasks.delete', $task->id) }}">
                                <i class="fas fa-trash"></i>
                            </button>

                            @include('admin.modal.confirmationmodal')

                        </td>

                    </tr>

                    {{-- VIEW MODAL --}}
                    <div class="modal fade" id="viewModal{{ $task->id }}">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5>Task Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <p><strong>User:</strong> {{ $task->user->name }}</p>
                                    <p><strong>Email:</strong> {{ $task->user->email }}</p>
                                    <p><strong>Task:</strong> {{ $task->title }}</p>
                                    <p><strong>Description:</strong> {{ $task->description ?? '-' }}</p>
                                    <p><strong>Priority:</strong> {{ ucfirst($task->priority) }}</p>
                                    <p><strong>Date:</strong> {{ $task->created_at }}</p>

                                    <p>
                                        <strong>Status:</strong>
                                        <span class="badge {{ $task->is_completed ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $task->is_completed ? 'Completed' : 'Pending' }}
                                        </span>
                                    </p>

                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- EDIT MODAL --}}
                    <div class="modal fade  " id="editModal{{ $task->id }}">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <form method="POST" action="{{ route('admin.tasks.update', $task->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5>Edit Task</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="mb-2">
                                            <label>User</label>
                                            <select name="user_id" class="form-control">
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ $task->user_id == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-2">
                                            <label>Task Title</label>
                                            <input type="text" name="title" class="form-control"
                                                value="{{ $task->title }}" required>
                                        </div>

                                        <div class="mb-2">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control">{{ $task->description }}</textarea>
                                        </div>

                                        <div class="mb-2">
                                            <label>Date</label>
                                            <input type="date" name="task_date" class="form-control"
                                                value="{{ $task->task_date }}" required>
                                        </div>

                                        <div class="mb-2">
                                            <label>Priority</label>
                                            <select name="priority" class="form-control">
                                                <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                                            </select>
                                        </div>

                                        <div class="mb-2">
                                            <label>Status</label>
                                            <select name="is_completed" class="form-control">
                                                <option value="0" {{ !$task->is_completed ? 'selected' : '' }}>Pending</option>
                                                <option value="1" {{ $task->is_completed ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-success w-100">Update Task</button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="7" class="text-center py-3">No tasks found</td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-3">
            {{ $tasks->appends(request()->query())->links('admin.layouts.partials.__pagination') }}
        </div>

    </div>
</div>

{{-- =========================
    ADD TASK MODAL
========================= --}}
<div class="modal fade" id="addTaskModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="{{ route('admin.tasks.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-2">
                        <label>User</label>

                        <select name="user_id" class="form-control" required>
                            <option value="">Select User</option>

                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Task Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <div class="mb-2">
                        <label>Priority</label>
                        <select name="priority" class="form-control">
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Date</label>
                        <input type="date" name="task_date" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success w-100">Save Task</button>
                </div>

            </form>

        </div>
    </div>
</div>


    <script>
        document.addEventListener("DOMContentLoaded", function () {

            let searchInput = document.getElementById("searchInput");
            let timer = null;

            searchInput.addEventListener("keyup", function () {

                clearTimeout(timer);

                timer = setTimeout(() => {

                    let query = this.value;

                    let url = new URL(window.location.href);

                    if (query) {
                        url.searchParams.set('search', query);
                    } else {
                        url.searchParams.delete('search');
                    }

                    window.location.href = url.toString();

                }, 500);

            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            var deleteModal = document.getElementById('globalDeleteModal');

            deleteModal.addEventListener('show.bs.modal', function (event) {

                var button = event.relatedTarget;
                var url = button.getAttribute('data-url');

                document.getElementById('globalDeleteForm').setAttribute('action', url);
            });

        });
    </script>

@endsection


