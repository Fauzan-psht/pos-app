@extends('layouts.app')

@section('content')
    <div class="mb-3">
        <h4>Log Aktivitas Sistem</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Nama Tabel</th>
                        <th>ID Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->user ? $log->user->name : 'Sistem' }}</td>
                            <td>
                                @if($log->action === 'create')
                                    <span class="badge bg-success">CREATE</span>
                                @elseif($log->action === 'update')
                                    <span class="badge bg-warning text-dark">UPDATE</span>
                                @else
                                    <span class="badge bg-danger">DELETE</span>
                                @endif
                            </td>
                            <td>{{ $log->table_name }}</td>
                            <td>#{{ $log->record_id }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Belum ada aktivitas tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $logs->links() }}
        </div>
    </div>
@endsection