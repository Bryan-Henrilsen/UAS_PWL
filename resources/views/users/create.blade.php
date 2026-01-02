@extends('layouts.master')
@section('title', 'Tambah User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4">
            <h4 class="mb-3">Tambah User Baru</h4>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" required placeholder="Nama User">
                </div>
                
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Nama Panggilan">
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required minlength="5" placeholder="Minimal 5 karakter">
                </div>
                <div class="mb-3">
                    <label>Role (Jabatan)</label>
                    <select name="role" class="form-select" required>
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}">{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success w-100">Simpan User</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary w-100 mt-2">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection