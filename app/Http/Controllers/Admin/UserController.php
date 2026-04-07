<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // ── INDEX ──────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = User::query();

        if ($role = $request->role) {
            $query->where('role', $role);
        }

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'    => User::count(),
            'admin'    => User::where('role', 'admin')->count(),
            'owner'    => User::where('role', 'owner')->count(),
            'employee' => User::where('role', 'employee')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    // ── CREATE ─────────────────────────────────────────────────
    public function create()
    {
        return view('admin.users.create');
    }

    // ── STORE ──────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,owner,employee',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
            'gender'   => 'nullable|in:male,female',
            'position' => 'nullable|string|max:100',
            'photo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'role', 'phone', 'address', 'gender', 'position']);
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employees', 'public');
        }

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User <strong>' . $request->name . '</strong> berhasil ditambahkan!');
    }

    // ── SHOW ───────────────────────────────────────────────────
    public function show(User $user)
    {
        return redirect()->route('admin.users.edit', $user);
    }

    // ── EDIT ───────────────────────────────────────────────────
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // ── UPDATE ─────────────────────────────────────────────────
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|in:admin,owner,employee',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
            'gender'   => 'nullable|in:male,female',
            'position' => 'nullable|string|max:100',
            'photo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'role', 'phone', 'address', 'gender', 'position']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('employees', 'public');
        }

        if ($request->boolean('remove_photo') && $user->photo) {
            Storage::disk('public')->delete($user->photo);
            $data['photo'] = null;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User <strong>' . $user->name . '</strong> berhasil diperbarui!');
    }

    // ── DESTROY ────────────────────────────────────────────────
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User <strong>' . $user->name . '</strong> berhasil dihapus!');
    }
}
