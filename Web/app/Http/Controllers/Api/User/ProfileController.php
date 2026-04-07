<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user()->load(['student', 'employee']);
        $data = $user->toArray();
        $data['role'] = $user->getRoleNames()->first() ?? 'user';
        return response()->json($data);
    }
}
