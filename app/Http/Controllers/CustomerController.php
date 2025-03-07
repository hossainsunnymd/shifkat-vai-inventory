<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{


    function CustomerPage(Request $request)
    {
        $user_id = $request->header('id');
        $list = Customer::where('user_id', $user_id)->get();
        return Inertia::render('CustomerPage', ['list' => $list]);
    }

    function CustomerSavePage(Request $request)
    {
        $category_id=$request->query('id');
        $user_id=$request->header('id');
        $list=Customer::where('id',$category_id)->where('user_id',$user_id)->first();
        return Inertia::render('CustomerSavePage',['list'=>$list]);

    }

    function CustomerCreate(Request $request){
        $user_id = $request->header('id');
    
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email', // Ensure email is provided and valid
            'mobile' => 'required|string|max:15'
        ]);
        Customer::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
            'user_id' => $user_id
        ]);
        return redirect()->route('CustomerSavePage')->with(['status'=>true, 'message'=>"Success"]);
    }

    function CustomerList(Request $request){
        $user_id=$request->header('id');
        return Customer::where('user_id',$user_id)->get();
    }

    function CustomerDelete(Request $request){
        // $customer_id=$request->input('id');
        // $user_id=$request->header('id');
        // return Customer::where('id',$customer_id)->where('user_id',$user_id)->delete();

        try {
            $customer_id=$request->id;
            $user_id=$request->header('id');
            Customer::where('id',$customer_id)->where('user_id',$user_id)->delete();
            $data =['message'=>'Delete Successful','status'=>true,'error'=>''];
            return  redirect()->route('CustomerPage')->with($data);
        }catch (Exception $e){
            $data =['message'=>'Delete Fail','status'=>false,'error'=>''];
            return  redirect()->route('CustomerPage')->with($data);
        }

    }

    function CustomerByID(Request $request){
        $customer_id=$request->input('id');
        $user_id=$request->header('id');
        return Customer::where('id',$customer_id)->where('user_id',$user_id)->first();
    }

    function CustomerUpdate(Request $request){
        $customer_id=$request->input('id');
        $user_id=$request->header('id');
        Customer::where('id',$customer_id)->where('user_id',$user_id)->update([
            'name'=>$request->input('name'),
            'email'=>$request->input('email'),
            'mobile'=>$request->input('mobile'),
        ]);
        $data =['message'=>'Update Successful','status'=>true,'error'=>''];
        return  redirect()->route('CustomerSavePage')->with($data);
    }
}
