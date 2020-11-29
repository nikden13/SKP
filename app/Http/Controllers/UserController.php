<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /*public function index(Request $request)
    {
        $page_size = 20;
        $size_from_request = (int)$request->input('page_size');
        if ($size_from_request > 0 && $size_from_request < 101) {
            $page_size = $size_from_request;
        }
        $users = User::all()->sortby($request->input('sort_by'))->take($page_size);
        return response()->json($users,200);
    }*/

    public function show()
    {
        return response()->json(auth()->user(),200);
    }

    public function update(Request $request)
    {
        auth()->user()->update($request->all());
        return response()->json(auth()->user(),200);
    }

    public function destroy()
    {
        auth()->user()->delete();
        return response()->json(['message' => 'Account was deleted'],200);
    }
}
