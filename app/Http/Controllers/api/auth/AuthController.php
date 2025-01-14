<?php

namespace App\Http\Controllers\api\auth;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Destination;
use App\Models\Email;
use App\Models\ModelJob;
use App\Models\Ngo;
use App\Models\StatusTypeTran;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function user(Request $request)
    {
        $user = $request->user()->load([
            'contact:id,value',
            'email:id,value',
            'role:id,name',
              'userStatus:id,status_type_id'

        ]);
        $userPermissions = $this->userWithPermission($user);
        $userdetail = $this->userdetail($user);
        return response()->json(array_merge([
            "user" => $userdetail
            
        ], [
            "permissions" => $userPermissions["permissions"],
        ]), 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $email = Email::where('value', '=', $credentials['email'])->first();
        if (!$email) {
            return response()->json([
                'message' => __('app_translation.email_not_found'),
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
        $loggedIn = Auth::guard('user:api')->attempt([
            "email_id" => $email->id,
            "password" => $request->password,
        ]);
        if ($loggedIn) {
            // Get the auth user
            $user = $loggedIn['user'];
            if ($user->status == 0) {
                return response()->json([
                    'message' => __('app_translation.account_is_lock'),
                ], 403, [], JSON_UNESCAPED_UNICODE);
            }
            $userPermissions = $this->userWithPermission($user);
            $user = $user->load([
                'contact:id,value',
                'email:id,value',
                'role:id,name',
                'userStatus:id,status_type_id'
            ]);
           
            $userdetail = $this->userdetail($user);
            return response()->json(
                array_merge([
                    "user" => $userdetail
                ], [
                    "token" => $loggedIn['tokens']['access_token'],
                    "permissions" => $userPermissions["permissions"],
                ]),
                200,
                [],
                JSON_UNESCAPED_UNICODE
            );
        } else {
            return response()->json([
                'message' => __('app_translation.user_not_found')
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->invalidateToken(); // Calls the invalidateToken method defined in the trait
        return response()->json([
            'message' => __('app_translation.user_logged_out_success')
        ], 204, [], JSON_UNESCAPED_UNICODE);
    }
    // HELPER
    protected function userWithPermission($user)
    {
        $userId = $user->id;
        $userPermissions = DB::table('user_permissions')
            ->join('permissions', function ($join) use ($userId) {
                $join->on('user_permissions.permission', '=', 'permissions.name')
                    ->where('user_permissions.user_id', '=', $userId);
            })
            ->select(
                "permissions.name as permission",
                "permissions.icon as icon",
                "permissions.priority as priority",
                "user_permissions.view",
                "user_permissions.add",
                "user_permissions.delete",
                "user_permissions.edit",
                "user_permissions.id",
            )
            ->orderBy("priority")
            ->get();
        return ["user" => $user->toArray(), "permissions" => $userPermissions];
    }

   protected function userdetail($user)
{
    $userId = $user->id;
    $userRole = $user->role->name;

    // Fetch status type for all roles
    $status_type = StatusTypeTran::where('language_name', App::getLocale())
        ->where('status_type_id', $user->userStatus->status_type_id)
        ->select('id', 'name')
        ->first();

    // Handle regular user roles
    if (in_array($userRole, [RoleEnum::user, RoleEnum::super, RoleEnum::admin, RoleEnum::debugger])) {
        $userDetails = UserDetail::where('user_id', $userId)->first();

        return $userDetails ? [
            "id" => $user->id,
            "full_name" => $userDetails->full_name,
            "username" => $user->username,
            'email' => $user->email ? $user->email->value : "",
            "profile" => $user->profile,
            "status" => $status_type,
            "grant" => $userDetails->grant_permission,
            "role" => ["role" => $user->role->id, "name" => $user->role->name],
            'contact' => $user->contact ? $user->contact->value : "",
            "destination" => $userDetails->destination ? $this->getTranslationWithNameColumn($userDetails->destination, Destination::class) : "",
            "job" => $userDetails->job ? $this->getTranslationWithNameColumn($userDetails->job, ModelJob::class) : "",
            "created_at" => $user->created_at,
        ] : null;
    }

    // Handle NGO role
    if ($userRole == RoleEnum::ngo) {
        $ngoDetails = Ngo::where('user_id', $userId)->first();

        return $ngoDetails ? [
            "id" => $user->id,
            "ngo_name" => $ngoDetails->ngoTrans()->name ?? "",
            "username" => $user->username,
            'email' => $user->email ? $user->email->value : "",
            "profile" => $user->profile,
            "status" => $status_type,
            "role" => ["role" => $user->role->id, "name" => $user->role->name],
            'contact' => $user->contact ? $user->contact->value : "",
            "created_at" => $user->created_at,
        ] : null;
    }

    // Handle Donor role
    if ($userRole == RoleEnum::donor) {
        // Add donor-specific logic here when available
        return [
            "id" => $user->id,
            "username" => $user->username,
            "role" => ["role" => $user->role->id, "name" => $user->role->name],
            "created_at" => $user->created_at,
        ];
    }

    // Default response if role is not recognized
    return null;
}

}
