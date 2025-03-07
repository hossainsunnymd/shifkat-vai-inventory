<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class ProductController extends Controller
{


    function ProductPage(Request $request)
    {
        $user_id=$request->header('id');
        $list=Product::where('user_id',$user_id)->get();
        return Inertia::render('ProductPage',['list'=>$list]);
    }

    function ProductSavePage(Request $request)
    {
    
        $product_id=$request->query('id');
        $user_id=$request->header('id');
        $list=Product::where('id',$product_id)->where('user_id',$user_id)->first();
        // return Inertia::render('ProductSavePage',['list'=>$list]);

        $categories = Category::all();

        return Inertia::render('ProductSavePage', [
            'list' => $list,
            'categories' => $categories // Pass categories to the view
        ]);
    }

    




    // function CreateProduct(Request $request)
    // {
    //     $user_id=$request->header('id');
    //     return Product::create([
    //         'name'=>$request->input('name'),
    //         'price'=>$request->input('price'),
    //         'unit'=>$request->input('unit'),
    //         'category_id'=>$request->input('category_id'),
    //         'user_id'=>$user_id
    //     ]);

    // }


    public function CreateProduct(Request $request)
    {
    
    // Validate form inputs including the image
    $data=$request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'unit' => 'required|string|max:255',
        'category_id' => 'required',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);
   
    // Handle the image upload
    if ($request->hasFile('image')) {
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);
        $data['image']=$imageName;
    } else {
        $data['image']=null;
    }
        $data['user_id']= $request->header('id');
        Product::Create ($data);

    return redirect()->route('ProductSavePage')->with(['status'=>true, 'message'=>"Product Created"]);
}






    function DeleteProduct(Request $request)
    {
        try{
            $product_id=$request->id;
            $user_id=$request->header('id');
            $old_image=Product::where('id',$product_id)->where('user_id',$user_id)->select('image')->first();
            File::delete(public_path('images/'.$old_image->image));
            Product::where('id',$product_id)->where('user_id',$user_id)->delete();
            $data =['message'=>'Product Delete Successful','status'=>true,'error'=>''];
            return  redirect()->route('ProductPage')->with($data);
        }
        catch (Exception $e){
            $data =['message'=>'Product Delete Fail','status'=>false,'error'=>''];
            return  redirect()->route('ProductPage')->with($data);
        }
        
    }

    function ProductByID(Request $request)
    {
        $user_id=$request->header('id');
        $product_id=$request->input('id');
        return Product::where('id',$product_id)->where('user_id',$user_id)->first();


    }

    function ProductList(Request $request)
    {
        $user_id=$request->header('id');
        return Product::where('user_id',$user_id)->get();
    }

    // function UpdateProduct(Request $request)
    // {
    //     $user_id=$request->header('id');
    //     $product_id=$request->input('id');
    //     Product::where('id',$product_id)->where('user_id',$user_id)->update([
    //         'name'=>$request->input('name'),
    //         'price'=>$request->input('price'),
    //         'unit'=>$request->input('unit'),
    //         'category_id'=>$request->input('category_id'),
    //         'image'=>$request->input('image'),
    //     ]);
    //     $data =['message'=>'Update Successful','status'=>true,'error'=>''];
    //     return  redirect()->route('ProductSavePage')->with($data);


    // }




    public function UpdateProduct(Request $request)
    {
        $user_id = $request->header('id');
        $product_id = $request->input('id');
        
        // Find the existing product
        $product = Product::where('id', $product_id)
                          ->where('user_id', $user_id)
                          ->first();
        
        if (!$product) {
            return redirect()->route('ProductSavePage')->with([
                'status' => false, 
                'message' => 'Product not found', 
                'error' => 'Product does not exist.'
            ]);
        }
    
        // Prepare the data array for updating
        $data = [
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'unit' => $request->input('unit'),
            'category_id' => $request->input('category_id'),
        ];
    
        // Handle the image update if a new image is uploaded
        if ($request->hasFile('image')) {
            // Check if there is an existing image and delete it
            if ($product->image && file_exists(public_path('images/' . $product->image))) {
                unlink(public_path('images/' . $product->image)); // Delete the existing image
            }
    
            // Upload the new image
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = $imageName;
        }
    
        // Update the product record
        $product->update($data);
    
        return redirect()->route('ProductSavePage')->with([
            'status' => true, 
            'message' => 'Update Successful'
        ]);
    }
    








//     public function UpdateProduct(Request $request)
// {
//     // Validate form inputs excluding image as required
//     $data = $request->validate([
//         'name' => 'required|string|max:255',
//         'price' => 'required|numeric',
//         'unit' => 'required|string|max:255',
//         'category_id' => $request->input('category_id'),
//         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image is optional
//     ]);

//     $product_id = $request->input('id');
//     $user_id = $request->header('id');

//     // Find the product to update
//     $product = Product::where('id', $product_id)
//         ->where('user_id', $user_id)
//         ->first();

//     if (!$product) {
//         return redirect()->back()->with(['status' => false, 'message' => 'Product not found']);
//     }

//     // Check if a new image is uploaded
//     if ($request->hasFile('image')) {
//         // Delete the old image if exists
//         if ($product->image && file_exists(public_path('images/' . $product->image))) {
//             unlink(public_path('images/' . $product->image));
//         }

//         // Upload the new image
//         $imageName = time() . '.' . $request->image->extension();
//         $request->image->move(public_path('images'), $imageName);
//         $data['image'] = $imageName;
//     } else {
//         // Keep the existing image if no new one is uploaded
//         unset($data['image']); // Do not overwrite the existing image field
//     }

//     // Update the product details without overriding the image if none is uploaded
//     $product->update($data);

//     return redirect()->route('ProductSavePage')->with(['status' => true, 'message' => 'Product Updated']);
// }


}
