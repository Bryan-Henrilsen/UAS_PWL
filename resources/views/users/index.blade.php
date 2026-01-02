@extends('layouts.master')
@section('title', 'Manajemen User')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Manajemen User & Role</h4>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Tambah User Baru
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th width="5%">No</th>
                    <th>Nama User</th>
                    <th>Username</th> <th>Role (Hak Akses)</th>
                    <th>Tanggal Bergabung</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $key => $user)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>
                        <strong>{{ $user->name }}</strong>
                        @if(auth()->id() == $user->id)
                            <span class="badge bg-info ms-1">Saya</span>
                        @endif
                    </td>
                    <td>{{ $user->username }}</td>
                    <td>
                        @if(!empty($user->getRoleNames()))
                            @foreach($user->getRoleNames() as $role)
                                <span class="badge bg-primary">{{ $role }}</span>
                            @endforeach
                        @else
                            <span class="badge bg-secondary">Tidak ada role</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>

                            @if(auth()->id() != $user->id)
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini? Data transaksi mereka akan tetap ada tapi user hilang.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection