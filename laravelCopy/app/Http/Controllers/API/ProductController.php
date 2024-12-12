<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Auth;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //return ProductResource::collection(Product::first());
        //return ProductResource::collection(Product::paginate(8));
        return ProductResource::collection(Product::all());
    }

    /**
     * Show the form for creating a new resource.
     */
 /*    public function create()
    {
        return 'Product create';
    } */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request):JsonResponse
    {
        /* dd($request); */
        if ($request->file('product_image')) {
            $image = $request->file('product_image');
            $type = $image->getClientOriginalExtension();
            $img = date('Y-m-d-H-i-s') .  '.' . $type;
            $image->move('image/product/', $img);

            $product_image = 'image/product/' . $img;
        } else {
            $product_image = '/dist/img/user2-160x160.jpg';
        }
        $data = [
            'description'       =>$request->description,
            'product_image'     =>$product_image,
            'cost_price'        =>$request->cost_price,
            'increase'          =>$request->increase,
            'stock'             =>$request->stock,
            'enabled'           =>true,
            'user_created'      =>Auth::user()->id,
            'user_updated'      =>Auth::user()->id,
        ];

        $product = Product::create($data);
        if($product){
            return response()->json([
                'message'=>'Product Registration Successfull',
            ],201);
        }else{
            return response()->json([
                'message'=>'Something went wrong!'
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
