<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use Livewire\Component;
use DB;
use App\Models\User;

class CompanyUsersTab extends Component
{
    public $company_id;

    public function mount($company_id)
    {
        $this->company_id = $company_id;
    }

    public function markVerified($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->email_verified_at = now()->setTimezone(config('app.timezone'))->format("Y-m-d H:i:s");
            $user->save();
        }
    }

    public function render()
    {
        $users = DB::table('company_user')
            ->join('users', 'users.id', 'company_user.user_id')
            ->whereCompanyId($this->company_id)
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.phone', 'users.email', 'users.email_verified_at', 'users.last_login', 'users.created_at')
            ->get();

        return view('prepagobags::components.superadmin.company-users-tab', compact('users'));
    }
}
