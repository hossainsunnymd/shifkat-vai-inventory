<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleController extends Controller
{
    function SalePage(Request $request)
    {
        $user_id=$request->header('id');
        $customer=Customer::where('user_id',$user_id)->get();
        $product=Product::where('user_id',$user_id)->get();
        return Inertia::render('SalePage', ['customer'=>$customer , 'product'=>$product]);
    }
}
