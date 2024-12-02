<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json(['users' => $users]);
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        
        return response()->json([
            'user' => $user,
            'roles' => $user->roles->pluck('name') 
        ]);
    }

    public function assignRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $role = Role::findByName($request->role);

        if ($role) {
            $user->syncRoles([$role->name]);
            return response()->json(['message' => 'Role assigned successfully.']);
        } else {
            return response()->json(['message' => 'Role not found.'], 404);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully.']);
    }
}
