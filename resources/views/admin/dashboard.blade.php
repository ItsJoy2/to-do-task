@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">

    <h4 class="mb-4 fw-bold">
        Task Report ({{ $userData['date'] }})
    </h4>

    {{-- USER CARDS --}}
    <div class="row g-4">

        @foreach($userData['users'] as $user)

        <div class="col-md-4">

            <div class="card shadow-sm border-0 h-auto hover-shadow
                {{ $userData['topUser']['id'] == $user['id'] ? 'border border-success' : '' }}">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        {{-- USER NAME --}}
                        <h5 class="fw-bold mb-0">
                            {{ $user['name'] }}
                        </h5>

                        {{-- TOP BADGE --}}
                        @if($userData['topUser']['id'] == $user['id'])
                            <span class="badge bg-success">
                                🏆 Top Performer
                            </span>
                        @endif

                    </div>

                    <div class="row g-1 mb-3">

                        <div class="col-4">
                            <div class="pt-1 border rounded text-center">
                                <small class="text-muted d-block">Today</small>
                                <strong class="text-primary">{{ $user['today_total'] }}</strong>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="pt-1 border rounded text-center">
                                <small class="text-muted d-block">Pending</small>
                                <strong class="text-warning">{{ $user['old_pending'] }}</strong>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class=" pt-1 border rounded text-center">
                                <small class="text-muted d-block">Completed</small>
                                <strong class="text-success">{{ $user['completed_today'] }}</strong>
                            </div>
                        </div>

                    </div>

                    {{-- PROGRESS BAR --}}
                    <div class="progress mb-2" style="height:8px;">
                        <div class="progress-bar
                            {{ $user['progress'] >= 80 ? 'bg-success' : ($user['progress'] >= 40 ? 'bg-warning' : 'bg-danger') }}"
                            style="width: {{ $user['progress'] }}%">
                        </div>
                    </div>

                    <small>Progress: {{ $user['progress'] }}%</small>

                    {{-- MODAL BUTTON --}}
                    <button class="btn btn-md btn-outline-danger w-100 mt-2"
                        data-bs-toggle="modal"
                        data-bs-target="#pendingModal-{{ $user['id'] }}">
                        Pending Tasks
                    </button>

                </div>
            </div>

        </div>

        {{-- MODAL --}}
        <div class="modal fade" id="pendingModal-{{ $user['id'] }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $user['name'] }} - Pending Tasks
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        @if(count($user['pending_tasks']) > 0)

                            <ul class="list-group">

                                @foreach($user['pending_tasks'] as $task)
                                <li class="list-group-item d-flex justify-content-between">

                                    <div>
                                        <div class="fw-semibold">
                                            {{ $task['title'] }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $task['created_at'] }}
                                        </small>
                                    </div>

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                </li>
                                @endforeach

                            </ul>

                        @else
                            <div class="text-center text-muted">
                                No pending tasks
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </div>

        @endforeach

    </div>

    <div class="card shadow-sm border-0 mt-4">

    <div class="card-body">

        {{-- Header with select --}}
        <div class="d-flex justify-content-between align-items-center mb-3">

            <h5 class="fw-bold mb-0">
                Performance Overview
            </h5>

            <form method="GET" class="d-flex gap-2">

                {{-- MONTH --}}
                <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">

                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}"
                            {{ $userData['selectedMonth'] == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor

                </select>

                {{-- YEAR --}}
                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">

                    @foreach($userData['years'] as $y)
                        <option value="{{ $y }}"
                            {{ $userData['selectedYear'] == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach

                </select>

            </form>

        </div>

        {{-- TABLE --}}
        <div class="table-responsive">

            <table class="table table-bordered text-center">

                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Total Tasks</th>
                        <th>Completed</th>
                        <th>Pending</th>
                        <th>Progress</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($userData['users'] as $user)

                    <tr>

                        <td>{{ $user['name'] }}</td>

                        <td>{{ $user['selected_total'] }}</td>

                        <td class="text-success fw-bold">
                            {{ $user['selected_completed'] }}
                        </td>

                        <td class="text-warning fw-bold">
                            {{ $user['selected_total'] - $user['selected_completed'] }}
                        </td>

                        <td>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar bg-primary"
                                    style="width: {{ $user['progress'] }}%">
                                </div>
                            </div>

                            <small>{{ $user['progress'] }}%</small>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            No data available.
                        </td>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

</div>
@endsection
