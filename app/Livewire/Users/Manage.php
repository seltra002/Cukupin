<?php

namespace App\Livewire\Users;

use App\Models\ActivityLog;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Manage extends Component
{
    public $email, $permission = 'view_only';

    public function invite()
    {
        abort_unless(Auth::user()->isOwner(), 403);
        $this->validate([
            'email' => 'required|email',
            'permission' => 'required|in:view_only,can_input',
        ]);

        $invitation = Invitation::create([
            'household_id' => Auth::user()->household_id,
            'email' => $this->email,
            'token' => Str::random(40),
            'permission' => $this->permission,
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
        ]);

        // TODO: kirim email undangan pakai Mail::to($invitation->email)->send(new InvitationMail($invitation));

        ActivityLog::record(Auth::user()->household_id, Auth::id(), 'user_invite', "Undang {$this->email} ({$this->permission})");

        $this->reset(['email']);
        $this->permission = 'view_only';
    }

    public function updatePermission($userId, $permission)
    {
        abort_unless(Auth::user()->isOwner(), 403);
        $user = User::where('household_id', Auth::user()->household_id)->where('role', '!=', 'owner')->findOrFail($userId);
        $user->update(['permission' => $permission]);
        ActivityLog::record(Auth::user()->household_id, Auth::id(), 'user_permission_update', "Ubah akses {$user->name} jadi {$permission}");
    }

    public function revoke($userId)
    {
        abort_unless(Auth::user()->isOwner(), 403);
        $user = User::where('household_id', Auth::user()->household_id)->where('role', '!=', 'owner')->findOrFail($userId);
        $name = $user->name;
        $user->update(['household_id' => null, 'permission' => null]);
        ActivityLog::record(Auth::user()->household_id, Auth::id(), 'user_revoke', "Cabut akses {$name}");
    }

    public function cancelInvitation($id)
    {
        abort_unless(Auth::user()->isOwner(), 403);
        Invitation::where('household_id', Auth::user()->household_id)->findOrFail($id)->delete();
    }

    public function render()
    {
        $members = User::where('household_id', Auth::user()->household_id)->where('role', '!=', 'owner')->get();
        $invitations = Invitation::where('household_id', Auth::user()->household_id)->where('status', 'pending')->latest()->get();

        return view('livewire.users.manage', compact('members', 'invitations'));
    }
}
