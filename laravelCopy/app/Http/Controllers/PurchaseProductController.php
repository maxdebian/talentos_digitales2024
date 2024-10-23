<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Provider;
use App\Models\ProductProvider;
use App\Helpers\Notification;
use Auth;

class PurchaseProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $providers = Provider::all();
        $products = Product::all();
        return view('purchase.create',compact('providers','products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request);
        $provider = Provider::find($request->provider);
        $product = Product::find($request->product);
        if(!is_null($provider) && !is_null($product)){
            $product->stock = $product->stock + $request->count;
            $product->cost_price = $request->cost_price;
            $product->increase = $request->increase;
            $product->user_updated = Auth::user()->id;
            $product->save();

            /* Pivot */
            $productProvider = ProductProvider::where('product_id',$product->id)
                ->where('provider_id',$provider->id)->first();
            if(is_null($productProvider)){
                ProductProvider::create([
                    'product_id'    =>  $product->id,
                    'provider_id'   =>  $provider->id,
                ]);
            }

            $notification = Notification::Notification('Product Successfully Update', 'success');
            return redirect('product')->with('notification', $notification);
        }
        $notification = Notification::Notification('Error', 'error');
        return redirect('product/create')->with('notification', $notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
