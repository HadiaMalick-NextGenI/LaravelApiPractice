<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        try{
            $users = User::with('roles')->get();
            return response()->json(['users' => $users]);
        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try{
            $user = User::with('roles')->findOrFail($id);
            
            return response()->json([
                'user' => $user,
                'roles' => $user->roles->pluck('name') 
            ]);
        }catch(Exception $e){
            return ApiResponse::error('User not found',404);
        }
    }

    public function assignRole(Request $request, $id)
    {
        try{
            $user = User::findOrFail($id);
            $role = Role::findByName($request->role);

            if ($role) {
                $user->syncRoles([$role->name]);
                return response()->json(['message' => 'Role assigned successfully.']);
            } else {
                return response()->json(['message' => 'Role not found.'], 404);
            }
        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try{
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'User deleted successfully.']);
        }catch(Exception $e){
            return ApiResponse::error('User not found',404);
        }
    }
}
