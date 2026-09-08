<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminInvitationMail;
use App\Models\Admin;
use App\Models\AdminInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/** Only reachable via permission:admins.manage — see routes/admin.php. */
class AdminManagementController extends Controller
{
    public function index(Request $request)
    {
        $admins = Admin::query()
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->role))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q2) => $q2
                ->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%")))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.admins.index', ['admins' => $admins, 'roles' => Role::where('guard_name', 'admin')->pluck('name')]);
    }

    public function create()
    {
        return view('admin.admins.create', ['roles' => Role::where('guard_name', 'admin')->pluck('name')]);
    }

    /** Creates a passwordless admin + sends a signed "set your password" link. Never sets a password here. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admins,email'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $admin = DB::transaction(function () use ($data, $request) {
            $newAdmin = Admin::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'is_active' => true,
            ]);

            $newAdmin->assignRole($data['role']);

            $invitation = AdminInvitation::create([
                'admin_id' => $newAdmin->id,
                'token' => Str::random(40),
                'invited_by' => $request->user('admin')->id,
                'expires_at' => now()->addDays(3),
            ]);

            Mail::to($newAdmin->email)->send(new AdminInvitationMail($invitation));

            return $newAdmin;
        });

        return redirect()->route('admin.admins.index')->with('success', "Admin created — an invitation to set their password was sent to {$admin->email}.");
    }

    public function update(Request $request, Admin $admin)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $admin->update([
            'name' => $data['name'],
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active', $admin->is_active),
        ]);

        $admin->syncRoles([$data['role']]);

        return back()->with('success', 'Admin updated.');
    }

    public function destroy(Request $request, Admin $admin)
    {
        abort_if($admin->id === $request->user('admin')->id, 403, "You can't delete your own account.");

        $admin->delete();

        return back()->with('success', 'Admin removed.');
    }

    public function show(Admin $admin)
    {
        return view('admin.admins.show', ['admin' => $admin]);
    }

    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', ['admin' => $admin, 'roles' => Role::where('guard_name', 'admin')->pluck('name')]);
    }

    /** Recent activity_log entries caused by this admin — filterable, same pattern as the main log viewer. */
    public function logs(Request $request, Admin $admin)
    {
        $logs = $admin->actions()
            ->when($request->filled('event'), fn ($q) => $q->where('event', 'like', "%{$request->event}%"))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.admins.logs', ['admin' => $admin, 'logs' => $logs]);
    }
}
