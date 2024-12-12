<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;
use Auth;
use Illuminate\Http\JsonResponse;
use Exception;
use Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():JsonResponse
    {
        $cart = Cart::where('user_created',Auth::user()->id)->first();
        if(is_null($cart)) return response()->json(['productCount'=>0], 200);

        $cartsDetails = CartDetail::with('product')/* with(['product' => function($queryProduct){
            $queryProduct->select('id','description')->get();
        }]) */->where('cart_id',$cart->id)->get();
        $total = 0;
        $totalProduct=0;
        foreach ($cartsDetails as $key => $detail) {
            $cartsDetails[$key]->totalPrice = (((($detail->increase * $detail->cost_price) / 100) + $detail->cost_price) * $detail->count);
            $total += (((($detail->increase * $detail->cost_price) / 100) + $detail->cost_price) * $detail->count);
            $totalProduct += $detail->count;
        }
        /* $totalProduct = $cartsDetails->count(); */

        $cart->total = $total;
        $cart->totalProduct = $totalProduct;

        return response()->json([
            'cart'          =>  $cart->toArray(),
            'cartsDetails'    =>  $cartsDetails->toArray()
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request):JsonResponse
    {

        try {
            DB::beginTransaction();
            $product = Product::find($request->product_id);
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
                return response()->json([
                    'message'=>'add cart successfuly',
                ],200);
            }
        } catch (\Exception $e) {

            DB::rollBack();
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
        try {
            DB::beginTransaction();
            $product = Product::find($id);
            $cart = Cart::where('user_created',Auth::user()->id)->first();
            $detail = CartDetail::where('cart_id',$cart->id)->where('product_id',$product->id)->first();
            //dd($product->id,$cart->id);
            if(!is_null($detail)) $detail->delete();
            if($product){
                $product->stock = $product->stock + 1;
                $product->save();
                DB::commit();
                return response()->json([
                    'message'=>'remove cart successfuly',
                ],200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'=>'Something went wrong!'
            ],500);
        }
    }
}
