<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    public function index()
    {
        return view('admin.staff.index', [
            'staff' => User::whereIn('role', ['admin', 'staff'])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160|unique:users,email',
            'password' => ['required', Password::min(8)],
            'role' => 'required|in:admin,staff',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => $data['password'],
            'role' => $data['role'],
            'is_active' => true,
        ]);

        AuditLog::record('staff.created', $user);

        return back()->with('success', 'Staff account created.');
    }

    public function update(Request $request, User $staff)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160|unique:users,email,'.$staff->id,
            'password' => ['nullable', Password::min(8)],
            'role' => 'required|in:admin,staff',
            'is_active' => 'nullable|boolean',
        ]);

        $staff->fill([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active'),
        ]);
        if (! empty($data['password'])) {
            $staff->password = $data['password'];
        }
        $staff->save();

        AuditLog::record('staff.updated', $staff);

        return back()->with('success', 'Staff account updated.');
    }

    public function destroy(Request $request, User $staff)
    {
        if ($staff->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        AuditLog::record('staff.deleted', $staff, ['email' => $staff->email]);
        $staff->delete();

        return back()->with('success', 'Staff account removed.');
    }
}
