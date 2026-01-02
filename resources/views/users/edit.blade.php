@extends('layouts.master')
@section('title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4">
            <h4 class="mb-3">Edit User: {{ $user->name }}</h4>
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>

                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                </div>
                
                <div class="alert alert-info py-2 small">
                    <i class="fas fa-key"></i> Kosongkan kolom password jika tidak ingin mengubah password user.
                </div>

                <div class="mb-3">
                    <label>Password Baru (Opsional)</label>
                    <input type="password" name="password" class="form-control" minlength="6" placeholder="Biarkan kosong jika tidak diganti">
                </div>

                <div class="mb-3">
                    <label>Role (Jabatan)</label>
                    <select name="role" class="form-select" required>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ $userRole == $role ? 'selected' : '' }}>
                                {{ $role }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary w-100 mt-2">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection