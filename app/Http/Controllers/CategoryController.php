<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{

    function CategoryPage(Request $request)
    {
        $user_id=$request->header('id');
        $list= Category::where('user_id',$user_id)->get();
        return Inertia::render('CategoryPage',['list'=>$list]);
    }


    function CategorySavePage(Request $request)
    {
        $category_id=$request->query('id');
        $user_id=$request->header('id');
        $list=Category::where('id',$category_id)->where('user_id',$user_id)->first();
        return Inertia::render('CategorySavePage',['list'=>$list]);

    }




    function CategoryList(Request $request){
        $user_id=$request->header('id');
        return Category::where('user_id',$user_id)->get();
    }

    function CategoryCreate(Request $request){

        $user_id=$request->header('id');
        Category::create([
            'name'=>$request->input('name'),
            'user_id'=>$user_id
        ]);
        $data =['message'=>'Create Successful','status'=>true,'error'=>''];
        return  redirect()->route('CategorySavePage')->with($data);

    }

    function CategoryDelete(Request $request){

        try {
            $category_id=$request->id;
            $user_id=$request->header('id');
            Category::where('id',$category_id)->where('user_id',$user_id)->delete();
            $data =['message'=>'Delete Successful','status'=>true,'error'=>''];
            return  redirect()->route('CategoryPage')->with($data);
        }catch (Exception $e){
            $data =['message'=>'Delete Fail','status'=>false,'error'=>''];
            return  redirect()->route('CategoryPage')->with($data);
        }

    }

    function CategoryByID(Request $request){
        $category_id=$request->input('id');
        $user_id=$request->header('id');
        return Category::where('id',$category_id)->where('user_id',$user_id)->first();
    }

    function CategoryUpdate(Request $request){
        $category_id=$request->input('id');
        $user_id=$request->header('id');
        Category::where('id',$category_id)->where('user_id',$user_id)->update([
            'name'=>$request->input('name'),
        ]);
        $data =['message'=>'Update Successful','status'=>true,'error'=>''];
        return  redirect()->route('CategorySavePage')->with($data);
    }
}
