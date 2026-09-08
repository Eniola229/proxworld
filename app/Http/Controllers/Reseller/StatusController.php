<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function pending(Request $request)
    {
        $reseller = $request->user()->reseller;

        if ($reseller && $reseller->isActive()) {
            return redirect()->route('reseller.dashboard');
        }

        return view('reseller.welcome-pending', ['reseller' => $reseller]);
    }
}
