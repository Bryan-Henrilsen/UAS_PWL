<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'asc')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // Validasi Username (Unik)
            'username' => 'required|string|unique:users,username|max:50',
            'password' => 'required|min:5', 
            'role' => 'required|exists:roles,name'
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username, // Simpan Username
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($request->role);

            DB::commit();
            return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name')->first();

        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            // Validasi Unique Username kecuali punya sendiri
            'username' => 'required|string|max:50|unique:users,username,'.$id,
            'password' => 'nullable|min:5',
            'role' => 'required|exists:roles,name'
        ]);

        try {
            DB::beginTransaction();

            $user->name = $request->name;
            $user->username = $request->username; // Update Username
            
            if(!empty($request->password)) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            $user->syncRoles($request->role);

            DB::commit();
            return redirect()->route('users.index')->with('success', 'Data user diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if (auth()->id() == $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}