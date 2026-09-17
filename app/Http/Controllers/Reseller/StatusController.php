<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Types\ResellerStatus;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function pending(Request $request)
    {
        $user = $request->user();
        $reseller = $user->reseller;

        if ($reseller && $reseller->status === ResellerStatus::APPROVED) {
            return redirect()->route('reseller.dashboard');
        }

        return view('reseller.welcome-pending', ['reseller' => $reseller]);
    }
}