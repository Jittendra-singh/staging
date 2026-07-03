<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Volunteer;
use App\Events\AdminImpersonationStarted;
use App\Events\AdminImpersonationEnded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ImpersonationController extends Controller
{
    /**
     * Begin impersonating a volunteer.
     */
    public function take(Request $request, int $userId)
    {
        $admin = Auth::guard('web')->user();
        if (!$admin) {
            abort(403, 'You must be logged in as an administrator to impersonate.');
        }

        $target = Volunteer::findOrFail($userId);

        // Enforce canImpersonate() on the admin
        if (!$admin->canImpersonate()) {
            abort(403, 'You do not have permission to impersonate.');
        }

        // Enforce canBeImpersonated() on the target
        if (!$target->canBeImpersonated()) {
            abort(403, 'This user cannot be impersonated.');
        }

        // Fire audit event BEFORE switching auth
        event(new AdminImpersonationStarted($admin, $target, $request->ip()));

        // Start impersonation on the 'front' guard
        $admin->impersonate($target, 'front');

        return $this->redirectToConfiguredTarget('laravel-impersonate.take_redirect_to')
            ->with('impersonation_notice', "You are now impersonating {$target->name} ({$target->email_address})");
    }

    /**
     * Leave impersonation and return to admin session.
     */
    public function leave(Request $request)
    {
        if (!app('impersonate')->isImpersonating()) {
            return redirect('/')->with('info', 'No active impersonation session.');
        }

        $admin = app('impersonate')->getImpersonator();
        $target = Auth::guard('front')->user();

        if ($admin && $target) {
            // Fire audit event BEFORE restoring auth
            event(new AdminImpersonationEnded($admin, $target, $request->ip()));
        }

        // Restore original admin session
        if ($target) {
            $target->leaveImpersonation();
        } else {
            app('impersonate')->clear();
        }

        return $this->redirectToConfiguredTarget('laravel-impersonate.leave_redirect_to')
            ->with('success', "You have returned to your admin account.");
    }

    protected function redirectToConfiguredTarget(string $configKey)
    {
        $target = config($configKey, '/');

        return Route::has($target)
            ? redirect()->route($target)
            : redirect($target);
    }
}
