@extends('user.layouts.app')

@section('userContent')

<div class="page-header">
  <h3 class="page-title">Todo History</h3>
</div>

<div class="col-lg-12 grid-margin stretch-card">
  <div class="card">
    <div class="card-body">

      <div class="d-flex justify-content-between mb-3">
        <h4 class="card-title">All Tasks</h4>

        <!-- Add Task Button -->
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addTodoModal">
          + Add Task
        </button>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-hover">

            <thead class="thead-dark">
            <tr>
                <th>SL</th>
                <th>Task Date</th>
                <th>Completed</th>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>

            @forelse ($todos as $todo)

            <tr>

                <!-- SL -->
                <td>
                    {{ $loop->iteration + ($todos->currentPage() - 1) * $todos->perPage() }}
                </td>
                <!-- Task Date -->
                <td>{{ $todo['created_at'] }}</td>

                <!-- Completed -->
                <td>
                    {{ $todo['completed_at'] ?? '-' }}
                </td>

                <!-- Title -->
                <td>
                    <span>
                        {{ $todo['title'] }}
                    </span>
                </td>

                <!-- Description -->
                <td>{{ $todo['description'] }}</td>

                <!-- Status -->
                <td>
                    <span class="badge badge-{{ $todo['badge'] }}">
                        {{ $todo['status_text'] }}
                    </span>
                </td>

                <!-- Action -->
                <td class="d-flex gap-2">

                    <form action="{{ route('user.todos.toggle', $todo['id']) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm {{ $todo['is_completed'] ? 'btn-success' : 'btn-outline-secondary' }}">
                            {{ $todo['is_completed'] ? 'Done' : 'Mark' }}
                        </button>
                    </form>

                    <!-- EDIT BUTTON -->
                    <button class="btn btn-sm btn-info"
                        data-toggle="modal"
                        data-target="#editTodoModal{{ $todo['id'] }}">
                        <i class="fas fa-edit"></i>
                    </button>

                </td>

            </tr>
            <!-- ================= EDIT MODAL ================= -->
            <div class="modal fade" id="editTodoModal{{ $todo['id'] }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('user.todos.update', $todo['id']) }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-content">

                            <div class="modal-header">
                                <h5>Edit Task</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <div class="modal-body">

                                <div class="form-group">
                                    <label>Task Title</label>
                                    <input type="text"
                                        name="title"
                                        value="{{ $todo['title'] }}"
                                        class="form-control"
                                        required>
                                </div>

                                <div class="form-group mt-2">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control">{{ $todo['description'] }}</textarea>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-success w-100">
                                    Update Task
                                </button>
                            </div>

                        </div>

                    </form>
                </div>
            </div>

            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    No tasks found
                </td>
            </tr>




            @endforelse


            </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="mt-3">
          {{ $todos->links('user.layouts.partials.__pagination') }}
      </div>

    </div>
  </div>
</div>


{{-- ================= MODAL ================= --}}
<div class="modal fade" id="addTodoModal">
    <div class="modal-dialog">
        <form action="{{ route('user.todos.store') }}" method="POST">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add New Task</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Task Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="form-group mt-2">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <div class="form-group mt-2">
                        <label>Date</label>
                        <input type="date" name="task_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Save Task</button>
                </div>
            </div>

        </form>
    </div>
</div>



@endsection
