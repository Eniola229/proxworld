<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $reseller = $request->user()->reseller;

        $customers = User::whereHas('orders', fn ($q) => $q->where('reseller_id', $reseller->id))
            ->withCount(['orders' => fn ($q) => $q->where('reseller_id', $reseller->id)])
            ->paginate(20);

        return view('reseller.manage.customers', ['customers' => $customers]);
    }
}
