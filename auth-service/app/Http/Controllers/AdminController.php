<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;

class AdminController extends Controller
{
    use ApiResponse;

    public function usersCount()
    {
        $total = User::count();

        return $this->success(['total' => $total]);
    }
}
