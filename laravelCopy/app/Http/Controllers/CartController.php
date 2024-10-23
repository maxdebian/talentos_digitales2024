<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;
use App\Helpers\Notification;
use Exception;
use Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cart = Cart::with('carts_details')->where('user_created',Auth::user()->id)->first();
        if(is_null($cart)) return redirect('/');
        $total = 0;
        foreach ($cart->carts_details as $key => $detail) {
            $cart->carts_details[$key]->costPrice = (((($detail->increase * $detail->cost_price) / 100) + $detail->cost_price) * $detail->count);
            $total += (((($detail->increase * $detail->cost_price) / 100) + $detail->cost_price) * $detail->count);
        }
        $cart->total = $total;
        return view('cart.list',compact('cart'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $product = Product::find($request->cartId);
            $cart = Cart::where('user_created',Auth::user()->id)->first();
            if(is_null($cart)){
                $cart = Cart::create([
                    'date'          =>  Carbon::now(),
                    'user_created'  =>  Auth::user()->id,
                    'user_updated'  =>  Auth::user()->id,
                ]);
                $detail = CartDetail::create([
                    'cart_id'       =>  $cart->id,
                    'product_id'    =>  $product->id,
                    'cost_price'    =>  $product->cost_price,
                    'increase'      =>  $product->increase,
                    'count'         =>  $request->count,
                ]);

            }else{
                $detail = CartDetail::where('cart_id',$cart->id)->where('product_id',$product->id)->first();
                if(is_null($detail)){
                    $detail = CartDetail::create([
                        'cart_id'       =>  $cart->id,
                        'product_id'    =>  $product->id,
                        'cost_price'    =>  $product->cost_price,
                        'increase'      =>  $product->increase,
                        'count'         =>  $request->count,
                    ]);
                }else{
                    $detail->cost_price     =  $product->cost_price;
                    $detail->increase       =  $product->increase;
                    $detail->count          = $detail->count  + $request->count;
                    $detail->save();
                }
            }
            if(!is_null($cart) && !is_null($detail)){
                $product->stock = $product->stock - $request->count;
                $product->save();
                DB::commit();
                $notification = Notification::Notification('Product Successfully Add', 'success');
                return redirect('cart')->with('notification', $notification);
            }
        } catch (\Exception $e) {
/*             dd($e); */
            DB::rollBack();
            $notification = Notification::Notification('Error', 'error');
            return redirect('/')->with('notification', $notification);
        }

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
