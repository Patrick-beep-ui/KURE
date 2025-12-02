<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;

class UsersController extends Controller
{
    public function getDoctors() {
        try {
            $doctors = User::where('role', 'doctor')->get();
            return response()->json($doctors);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching doctors',
                'message' => $e->getMessage()
            ], 500);
        }
    }   
}
