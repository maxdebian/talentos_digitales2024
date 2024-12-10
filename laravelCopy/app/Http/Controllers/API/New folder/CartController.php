<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\nucooks\entities\Cart;
use App\nucooks\entities\Language;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\nucooks\entities\DetailsCart;
use App\nucooks\entities\BranchOffice;
use App\nucooks\entities\LanguageRecipe;
use App\nucooks\entities\IngredientLanguage;
use App\nucooks\entities\IngredientsRecipe;
use App\nucooks\entities\BranchOfficeIngredient;
use App\nucooks\entities\StatusCarts;
use App\nucooks\entities\MercadopagoPayments;
use App\nucooks\entities\HistoryCart;
use App\nucooks\entities\UsersData;
use App\nucooks\entities\Address;
use App\nucooks\entities\Location;
use App\nucooks\entities\SubdetailsDetailsCarts;
use App\nucooks\entities\HistoryDetailsCart;
use App\nucooks\entities\HistorySubDetailsCart;
use App\nucooks\entities\StatusTakeCarts;
use App\nucooks\entities\OrderTime;
use App\nucooks\entities\CartNotifications;
use App\nucooks\entities\DeviveryPoints;

use App\User;
use App\Events\MyEvent;
use App\Events\StateCartEvent;
use App\Events\StateStockAlert;
use App\Events\StateReturnOrder;
use App\nucooks\entities\TimeRanges;
use App\nucooks\entities\UtensilLanguage;
use Symfony\Component\HttpFoundation\Session\Session;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use SantiGraviano\LaravelMercadoPago\Facades\MP;
use App\Traits\CalculateCart;

class CartController extends Controller
{
    use CalculateCart;
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['showCart', 'dataCart']]);
    }

    public function showCart()
    {
        if (Auth::user()) {
            $lang = Auth::user()->language_id;
        } else {
            $lang = session()->get('locale');
            if (is_null($lang)) {
                $lang = "pt-br";
            }
            $lang = Language::where('lang', $lang)->first();
            $lang = $lang->id;
        }
        return view('users.cart.cart');
    }



    public function dataCart($localStorage)
    {
        $recipesCart = array();
        $independientUtensilCart = array();
        $cart = json_decode($localStorage);

        $index = 0;
        $indexUtensil=0;
        foreach ($cart as $key => $valCart) {
            if(!is_null($valCart->idR)){
                /*             echo('<br>');
                echo('Id Receta: '.$valCart->idR);
                echo('  Id Lang: '.$valCart->lang);
                echo('  Select Person: '.$valCart->selectPerson); */
                $langId = $valCart->lang;

                /*             $recipesCart[$index] = LanguageRecipe::where('recipe_id',$valCart->idR)
                ->with(['recipe_only' => function($query){$query->with('images')->get();}])
                ->with('ingredient_language_recipes')
                ->with(['ingredients' => function($queryIngredient) use ($langId){
                    $queryIngredient->with(['ingredient_language' => function($queryLangIngredient) use ($langId){
                        $queryLangIngredient->where('language_id',$langId)->get();
                    }])->get();
                }])
                ->where('language_id',$valCart->lang)->get();
     */


                $recipesCart[$index] = LanguageRecipe::with(['recipe_only' => function ($query) {
                    $query->with('images')->get();
                }])->with(['ingredients' => function ($queryIngredient) use ($langId) {
                    $queryIngredient->with(['ingredient_language' => function ($queryLangIngredient) use ($langId) {
                        $queryLangIngredient->where('language_id', $langId)->get();
                    }])->get();
                }])
                    ->with(['utensils' => function ($queryUtensil) use ($langId) {
                        $queryUtensil->with(['utensil_language' => function ($queryLangUtensil) use ($langId) {
                            $queryLangUtensil->where('language_id', $langId)->get();
                        }])->get();
                    }])
                    ->with('ingredient_language_recipes', 'utensil_language_recipes')->where('recipe_id', $valCart->idR)->where('language_id', $langId)->get();


                $index++;

            }else{

/*                 {#719
  +"idR": null
  +"lang": 1
  +"selectPerson": 10
  +"independientUtensil": {#720
    +"id": 4
    +"utensil_id": 4
    +"utensilLanguageId": 10
    +"unit_min": "12"
  }
} */

                $independientUtensilCart[$indexUtensil] = UtensilLanguage::select('id','utensil_id','language_id','description','description_brand','cost_price','increase')
                ->where('id',$valCart->independientUtensil->utensilLanguageId)
                ->with(['utensil_only' => function($queryUtensil){
                    $queryUtensil->select('id','unit_min','avatar_image')->first();
                }])->first();
                $independientUtensilCart[$indexUtensil]->quantitySelected = $valCart->independientUtensil->quantity;


                $indexUtensil++;



            }



            /*             foreach ($valCart->ingredientLanguage as $keyIngredient => $valCartIngredient) {
                echo('<br>');
                echo(' Id Lang Ingredient: '.$valCartIngredient->id);
                echo('  Id Language Ingredient: '.$valCartIngredient->language_recipe_id);
                echo('  Id Ingredient: '.$valCartIngredient->ingredient_id);
                echo('  Status: '.$valCartIngredient->status);

            }  */
        }

        if(count($independientUtensilCart)>0 && count($recipesCart)>0){
            foreach ($recipesCart as $keyArray => $arrayRecipeCart) {
                foreach ($arrayRecipeCart as $keyRecipeCart => $recipeCart) {
                    if(!is_null($recipeCart->utensils)){
                        $flag = false;
                        foreach ($recipeCart->utensils as $keyUtensil => $utensilRecipi) {
                            foreach ($independientUtensilCart as $keyIndependientUtensil => $independientUtensil) {
                                if($utensilRecipi->id == $independientUtensil->utensil_id){
                                    $keyFound = $keyIndependientUtensil;
                                    $flag=true;
                                }
                                $independientUtensilCart[$keyIndependientUtensil]->added = false;
                            }
                        }
                        if($flag) $independientUtensilCart[$keyFound]->foundIt = $flag;

                    }
                }
            }
        }else if(count($independientUtensilCart)>0){
            dd('only utensil');
        }


    /*     echo('<br><br>');
        print_r($independientUtensilCart);
        echo('<br><br>');
        dd('stop'); */
/*         echo(json_encode($recipesCart)); */
        /*dd($localStorage); */
        /*  dd($recipesCart); */
        if ($recipesCart) {
            return response()->json([
                'status'                    => '200',
                'cart'                      => $recipesCart,
                'independientUtensilCart'   => $independientUtensilCart
            ]);
        }
        return response()->json([
            'status'                    => '400',
            'cart'                      => $recipesCart,
            'independientUtensilCart'   => $independientUtensilCart
        ]);
    }


    public function controlPayCart(Request $request)
    {
        $lang = Auth::user()->language_id;
        $arrayStockProductDescription = [];
        if (!is_null($request->ingredientIdArray)) {
            $count = count($request->ingredientIdArray);
            $flag = true;
            for ($i = 0; $i < $count; $i++) {
                $currentStock = BranchOfficeIngredient::where('ingredient_id', $request->ingredientIdArray[$i])->where('branch_office_id', 1)->first();
                $languageIngredient = IngredientLanguage::where('ingredient_id', $request->ingredientIdArray[$i])->where('language_id', $lang)->first();
                if (!empty($currentStock)) {
                    if ($currentStock->stock < $request->ingredientTotalUnitArray[$i]) {
                        $flag = false;
                        /* array_push($arrayStockProductDescription,['id' => $request->ingredientIdArray[$i], 'description' => $languageIngredient->description]); */
                        array_push($arrayStockProductDescription, ['id' => $languageIngredient->id, 'description' => $languageIngredient->description]);
                    }
                } else {
                    /* array_push($arrayStockProductDescription,['id' => $request->ingredientIdArray[$i], 'description' => $languageIngredient->description]); */
                    array_push($arrayStockProductDescription, ['id' => $languageIngredient->id, 'description' => $languageIngredient->description]);
                    $flag = false;
                }
            }
            if ($flag) {
                return response()->json([
                    'statusStock' => '200',
                    'missingDescription' =>  ''
                ]);
            } else {
                return response()->json([
                    'statusStock' => '400',
                    'missingDescription' =>  $arrayStockProductDescription
                ]);
            }
        }
        return response()->json([
            'statusStock' => '400',
            'missingDescription' =>  $arrayStockProductDescription
        ]);


        /*



        ESTO FUNCIONA BIEN PERO CUANDO ES SOLO LOS INGREDIENTES CONTRA LAS RECETAS

        $cartUser = Cart::where('user_id', Auth::user()->id)->where('status_cart',0)->first();
        $detailsCart = DetailsCart::where('cart_id',$cartUser->id)->with('sub_details')->get();

        $arrayProduct = [];
        $arrayStockProduct = [];
        $arrayStockProductDescription = [];
        foreach ($detailsCart as $valueDetailsCart) {
            foreach ($valueDetailsCart->sub_details as $valueSubDetailsCart) {
                if($valueSubDetailsCart->status == 0){
                    $arrayProduct[$valueSubDetailsCart->ingredient_language_id] =0;
                    $arrayStockProduct[$valueSubDetailsCart->ingredient_language_id] =0;
                }
            }

        }

        foreach ($detailsCart as $valueDetailsCart) {
            $selectPeron = $valueDetailsCart->selectPerson;
            $LanguageRecipe = LanguageRecipe::where('id',$valueDetailsCart->language_recipes_id)->with('recipe_only')->first();
            $minPerson = $LanguageRecipe->recipe_only->min_person;
            $statusFractional = 0;
            foreach ($valueDetailsCart->sub_details as $valueSubDetailsCart) {
                $languageIngredient = IngredientLanguage::where('id',$valueSubDetailsCart->ingredient_language_id)->with('ingredient_only')->first();
                if($languageIngredient->ingredient_only->fractional==0){
                    $statusFractional = 1;
                }
            }

            foreach ($valueDetailsCart->sub_details as $valueSubDetailsCart) {

                $languageIngredient = IngredientLanguage::where('id',$valueSubDetailsCart->ingredient_language_id)->with('ingredient_only')->first();
                $ingredientsRecipe = IngredientsRecipe::where('ingredient_id',$languageIngredient->ingredient_id)->where('language_recipe_id',$valueDetailsCart->language_recipes_id)->first();

                if($valueSubDetailsCart->status == 0){
                    if($statusFractional==0){
                        $arrayProduct[$valueSubDetailsCart->ingredient_language_id] += (($selectPeron*$ingredientsRecipe->quantity)/$minPerson);
                    }else{
                        $arrayProduct[$valueSubDetailsCart->ingredient_language_id] += ($selectPeron*$ingredientsRecipe->quantity);
                    }
                }
            }




        }


        $stateStock = true;
        foreach($arrayProduct as $keyId => $val){
            $languageIngredient = IngredientLanguage::where('id',$keyId)->with('ingredient_only')->first();
            if(!is_null($languageIngredient)){
                $valAbsolute =  intval(ceil($val / $languageIngredient->ingredient_only->unit_min));
                $currentStock = BranchOfficeIngredient::where('ingredient_id',$languageIngredient->ingredient_id)->where('branch_office_id',1)->first();
                if(!empty($currentStock)){
                    $arrayStockProduct[$keyId] = $currentStock->stock;
                }else{

                    $arrayStockProduct[$keyId] = 0;
                }

                if($val>0){

                    if($valAbsolute>$arrayStockProduct[$keyId]){
                        array_push($arrayStockProductDescription,['id' => $keyId, 'description' => $languageIngredient->description]);
                        $stateStock = false;
                    }
                }
            }
        }
        if($stateStock){
            return response()->json([
                'statusStock' => '200',
                'missingDescription' =>  ''
                ]);
        }else{
            return response()->json([
                'statusStock' => '400',
                'missingDescription' =>  $arrayStockProductDescription
                ]);
        } */
    }
    public function payCart()
    {

        /*         dd($date); */

        /*
            esta es la consulta que va
        SELECT * from details_cart INNER JOIN carts on carts.id=details_cart.cart_id INNER JOIN ingredient_language_recipe as ilr on ilr.language_recipe_id = details_cart.ingredient_language_recipe_id INNER JOIN ingredient_language as il on il.ingredient_id = details_cart.ingredient_id INNER JOIN ingredients as i on i.id = details_cart.ingredient_id where carts.language_id = 1 and carts.user_id = 8

        */


        $branchOffices = BranchOffice::all();

        $idLangUser = Auth::user()->language_id;


        $carts = DetailsCart::with(['carts' => function ($queryCart) use ($idLangUser) {
            $queryCart->where('language_id', $idLangUser)->get();
        }])->with(['ingredient' => function ($ingredient) use ($idLangUser) {
            $ingredient->with(['ingredient_language' => function ($ingreLang) use ($idLangUser) {
                $ingreLang->where('language_id', $idLangUser)->get();
            }])->get();
        }])->with('language_recipes')->get();

        return view('admin.payment.payment', compact('branchOffices', 'carts'));
    }


    public function confirmCart()
    {
        $branchOffices = BranchOffice::all();
        $idLangUser = Auth::user()->language_id;
        $carts = DetailsCart::with(['carts' => function ($queryCart) use ($idLangUser) {
            $queryCart->where('language_id', $idLangUser)->get();
        }])->with(['ingredient' => function ($ingredient) use ($idLangUser) {
            $ingredient->with(['ingredient_language' => function ($ingreLang) use ($idLangUser) {
                $ingreLang->where('language_id', $idLangUser)->get();
            }])->get();
        }])->with('language_recipes')->get();

        return view('admin.payment.confirm_payment', compact('branchOffices', 'carts'));
    }

    // para el boton pagar... seria necesario mandarle el cart_id para obtener el precio total
    public function pagarMercadopago($origin, $destination, $dateShipping, $hoursIndex)
    {
        $hourInitial = null;
        $hourFinaly = null;

        if ($hoursIndex != 'null') {
            $timeRange = TimeRanges::first();
            $horaInitial = strtotime($timeRange->hourInitial);
            $horaInitialFormat = date('H:i:s', $horaInitial);
            $selectTime = [];
            $partHI = explode(':', $timeRange->hourInitial);
            $partHF = explode(':', $timeRange->hourFinaly);
            $diff = $partHF[0] - $partHI[0];

            for ($x = 0; $x <= $diff; $x++) {
                array_push($selectTime, $horaInitialFormat);
                $horaInitialFormat = strtotime('+1 hour', strtotime($horaInitialFormat));
                $horaInitialFormat = date('H:i:s', $horaInitialFormat);
            }
            $interval = Carbon::parse($timeRange->interval)->format('h');
            foreach ($selectTime as $key => $selectTimeOrigin) {
                if ($key == $hoursIndex) {
                    $hourInitial = $selectTimeOrigin;
                    $horaInitialFormat = strtotime('+' . $interval . ' hour', strtotime($selectTimeOrigin));
                    $horaInitialFormat = date('H:i:s', $horaInitialFormat);
                    $hourFinaly = $horaInitialFormat;
                }
            }
        }


        /* tengo que modificar el carrito para poner la hora y entonces ahi sabre a que hora debo enviar el producto */

        if ($origin === 'address') {
            $idDestination = $destination;
            /* dd(Auth::user()->id); */
            $cartUser = Cart::where('user_id', Auth::user()->id)->where('status_cart', 0)->first();
            /*          dd($cartUser); */
            $address = Address::where('id', $idDestination)->first();
            $location = Location::where('id', $address->location_id)->first();

            $cartUser->date_shipping_retirement = $dateShipping;
            $cartUser->shipping_price = $location->shipping_cost;
            $cartUser->address_offices_id = $idDestination;
            $cartUser->type_origin = $origin;
            $cartUser->hourInitial = $hourInitial;
            $cartUser->hourFinaly = $hourFinaly;
            $cartUser->save();
        } else {
            $idDestination = $destination;
            $cartUser = Cart::where('user_id', Auth::user()->id)->where('status_cart', 0)->first();
            $cartUser->date_shipping_retirement = $dateShipping;
            $cartUser->shipping_price = 0;
            $cartUser->address_offices_id = $idDestination;
            $cartUser->type_origin = $origin;
            $cartUser->hourInitial = $hourInitial;
            $cartUser->hourFinaly = $hourFinaly;
            $cartUser->save();
        }


        $dateNow = new Carbon();
        if ($dateShipping < $dateNow->format('Y-m-d'))
            return back();
        if ($dateShipping === $dateNow->format('Y-m-d')) {
            $this->controlStockIngredient($cartUser);
        }


        /*         dd('origin: '.$origin.' --- destination: '.$destination.' ---- dateShipping: '.$dateShipping); */

        /*         echo($dateShipping.'---'.$dateNow->format('Y-m-d'));
        dd($cartUser); */






        $this->totalCartOnlyValRecipe(Auth::user()->id);


        /*         dd('stppppp'); */

        // fix mercado pago at the moment We redirect to success. Very important check that because the houerInitial and Hourfinaly is wrong in History carts
        $this->mercadopagoSuccessAuxiliary($hourInitial, $hourFinaly);
        return;



        /*

        $this->totalCart(Auth::user()->id);
 */












        /*         $cartUserAddress = Cart::where('user_id',Auth::user()->id)->where('status_cart',0)->first();


        $cartUserAddress->save(); */

        $cartUser = Cart::where('user_id', Auth::user()->id)->where('status_cart', 0)->first();
        dd($cartUser);
        $details = DetailsCart::where('cart_id', $cartUser->id)->get();

        /* if($destination=='address'){
            if(!is_null($cartUser)){
                $cartUser->shipping_price = 523.35;
                $cartUser->save();
            }
        }else{
            if(!is_null($cartUser)){
                $cartUser->shipping_price = 0;
                $cartUser->save();
            }
        } */
        /*         dd($cartUser); */

        $datos_carro = [
            'items' => [
                [
                    'id' => $cartUser->id,
                    'category_id' => 'Recetas',
                    'title' => 'Recetas de Nucooks',
                    'description' => 'Receta nombre',
                    'picture_url' => 'https://baconmockup.com/600/601',
                    'quantity' => 1,
                    'currency_id' => 'ARS',
                    'unit_price' => ($cartUser->total_price + $cartUser->shipping_price),
                ],
            ],
            'back_urls' => [
                'success' => 'http://nucooks.test:8000/success',
                'failure' => 'http://nucooks.test:8000/failure',
                'pending' => 'http://nucooks.test:8000/pending'
            ],
            'auto_return' => 'all', //'approved'

            // 'payment_methods' => [
            //     'excluded_payment_types' => [
            //         'ticket',
            //         'atm',
            //         //"id" => "debit_card"
            //     ],
            // ],
            "payment_methods" => array(
                "excluded_payment_methods" => array(),
                "excluded_payment_types" => array(
                    array("id" => "ticket"),
                    array("id" => "atm"),
                    array("id" => "debit_card")
                ),
                "installments" => 12
            ),
        ];

        // dd($datos_carro);
        // comentar este para pasar a sandbox estas dos lineas
        // $preference = MP::create_preference($datos_carro);
        // return dd($preference);

        // esta en modo sandbox ='sandbox_init_point', production='init_point'
        try {
            $preference = MP::create_preference($datos_carro);
            return redirect()->to($preference['response']['sandbox_init_point']);
        } catch (Exception $e) {
            /*          dd('stop'); */
            dd($e->getMessage());
        }
    }
    protected function mercadopagoSuccessAuxiliary($hourInitial, $hourFinaly)
    {

        $userId = Auth::user()->id;
        $cartUser = Cart::where('user_id', $userId)->where('status_cart', 0)->first();
        $statusCarts = StatusCarts::where('description', 'approved')->first();

        /* https://www.mercadopago.com.ar/developers/es/guides/online-payments/checkout-pro/test-integration
        https://www.mercadopago.com.ar/developers/es/guides/plugins
        https://www.mercadopago.com.ar/developers/es/guides/online-payments/checkout-pro/test-integration
        https://www.mercadopago.com.ar/

        revisar las tablas que estan faltando de estatus */

        /*  dd($statusCarts); */
        if ((!is_null($userId)) && (!is_null($cartUser)) && (!is_null($statusCarts))) {


            $paymentsMercadoPago = new MercadopagoPayments();
            $paymentsMercadoPago->cart_id = $cartUser->id;
            $paymentsMercadoPago->user_id = $userId;
            $paymentsMercadoPago->collection_id = '1';
            $paymentsMercadoPago->collection_status = 'approved';
            $paymentsMercadoPago->external_reference = '23SD32DS42';
            $paymentsMercadoPago->payment_type = 'CREDIT CARD';
            $paymentsMercadoPago->merchant_order_id = '23293823';
            $paymentsMercadoPago->preference_id = '3';
            $paymentsMercadoPago->site_id = '1';
            $paymentsMercadoPago->processing_mode = 'MODE CREDIT';
            $paymentsMercadoPago->merchant_account_id = '1';
            $paymentsMercadoPago->save();


            if ($paymentsMercadoPago) {
                if ('approved' === 'approved') {
                    $cartUser->status_cart = 1;
                    $cartUser->statuscart_id = $statusCarts->id;
                    $cartUser->save();
                    $this->saveHistoryCart($userId, $cartUser, $statusCarts->id, 'approved');
                }
                event(new MyEvent($paymentsMercadoPago->id));
            }
        }
        return redirect('/');
    }
    public function mercadopagoSuccess(Request $request)
    {
        dd('stop');
        $userId = Auth::user()->id;
        $cartUser = Cart::where('user_id', $userId)->where('status_cart', 0)->first();
        $statusCarts = StatusCarts::where('description', $request->collection_status)->first();
        if ((!is_null($userId)) && (!is_null($cartUser)) && (!is_null($statusCarts))) {


            $paymentsMercadoPago = new MercadopagoPayments();
            $paymentsMercadoPago->cart_id = $cartUser->id;
            $paymentsMercadoPago->user_id = $userId;
            $paymentsMercadoPago->collection_id = $request->collection_id;
            $paymentsMercadoPago->collection_status = $request->collection_status;
            $paymentsMercadoPago->external_reference = $request->external_reference;
            $paymentsMercadoPago->payment_type = $request->payment_type;
            $paymentsMercadoPago->merchant_order_id = $request->merchant_order_id;
            $paymentsMercadoPago->preference_id = $request->preference_id;
            $paymentsMercadoPago->site_id = $request->site_id;
            $paymentsMercadoPago->processing_mode = $request->processing_mode;
            $paymentsMercadoPago->merchant_account_id = $request->merchant_account_id;
            $paymentsMercadoPago->save();


            if ($paymentsMercadoPago) {
                if ($request->collection_status === 'approved') {
                    $cartUser->status_cart = 1;
                    $cartUser->statuscart_id = $statusCarts->id;
                    $cartUser->save();
                    $this->saveHistoryCart($userId, $cartUser, $statusCarts->id, $request->collection_status);



                    /*
                    FUNCIONA PERO SACAMOS POR CLIENTE VER QUE ESTO TIENE QUE VOLVER ACA
                    $this->updateStockIngredient($cartUser);


*/
                }
                event(new MyEvent($paymentsMercadoPago->id));
            }
        }
        return redirect('/');
    }
    public function mercadopagoFailure(Request $request)
    {

        $userId = Auth::user()->id;
        $cartUser = Cart::where('user_id', $userId)->where('status_cart', 0)->first();
        $statusCarts = StatusCarts::where('description', $request->collection_status)->first();
        if ((!is_null($userId)) && (!is_null($cartUser)) && (!is_null($statusCarts))) {
            $paymentsMercadoPago = new MercadopagoPayments();
            $paymentsMercadoPago->cart_id = $cartUser->id;
            $paymentsMercadoPago->user_id = $userId;
            $paymentsMercadoPago->collection_id = $request->collection_id;
            $paymentsMercadoPago->collection_status = $request->collection_status;
            $paymentsMercadoPago->external_reference = $request->external_reference;
            $paymentsMercadoPago->payment_type = $request->payment_type;
            $paymentsMercadoPago->merchant_order_id = $request->merchant_order_id;
            $paymentsMercadoPago->preference_id = $request->preference_id;
            $paymentsMercadoPago->site_id = $request->site_id;
            $paymentsMercadoPago->processing_mode = $request->processing_mode;
            $paymentsMercadoPago->merchant_account_id = $request->merchant_account_id;
            $paymentsMercadoPago->save();

            /*             if($paymentsMercadoPago){
                if($request->collection_status === 'rejected'){
                    $cartUser->status_cart = 1;
                    $cartUser->statuscart_id = $statusCarts->id;
                    $cartUser->save();
                }
            } */

            event(new MyEvent($paymentsMercadoPago->id));
        }
        return redirect('/');
    }
    public function mercadopagoPending(Request $request)
    {


        $userId = Auth::user()->id;
        $cartUser = Cart::where('user_id', $userId)->where('status_cart', 0)->first();
        $statusCarts = StatusCarts::where('description', $request->collection_status)->first();
        if ((!is_null($userId)) && (!is_null($cartUser)) && (!is_null($statusCarts))) {



            $paymentsMercadoPago = new MercadopagoPayments();
            $paymentsMercadoPago->cart_id = $cartUser->id;
            $paymentsMercadoPago->user_id = $userId;
            $paymentsMercadoPago->collection_id = $request->collection_id;
            $paymentsMercadoPago->collection_status = $request->collection_status;
            $paymentsMercadoPago->external_reference = $request->external_reference;
            $paymentsMercadoPago->payment_type = $request->payment_type;
            $paymentsMercadoPago->merchant_order_id = $request->merchant_order_id;
            $paymentsMercadoPago->preference_id = $request->preference_id;
            $paymentsMercadoPago->site_id = $request->site_id;
            $paymentsMercadoPago->processing_mode = $request->processing_mode;
            $paymentsMercadoPago->merchant_account_id = $request->merchant_account_id;
            $paymentsMercadoPago->save();


            if ($paymentsMercadoPago) {
                if ($request->collection_status === 'in_process') {
                    $cartUser->status_cart = 1;
                    $cartUser->statuscart_id = $statusCarts->id;
                    $cartUser->save();
                    $this->saveHistoryCart($userId, $cartUser, $statusCarts->id, $request->collection_status);
                }
                event(new MyEvent($paymentsMercadoPago->id));
            }
        }
        return redirect('/');
    }




    protected function totalCartOnlyValRecipe($userId)
    {
        $total = 0;
        $cartUser = Cart::where('user_id', $userId)->where('status_cart', 0)->first();
        $detailsCart = DetailsCart::where('cart_id', $cartUser->id)->with('sub_details')->get();
        $arrayProductTotalUnit = [];
        foreach ($detailsCart as $valueDetailsCart) {
            foreach ($valueDetailsCart->sub_details as $valueSubDetailsCart) {
                if (!$valueSubDetailsCart->status) array_push($arrayProductTotalUnit, ['id' => $valueSubDetailsCart->ingredient_language_id, 'value' => $valueSubDetailsCart->total_unit]);
            }
        }
        if (count($arrayProductTotalUnit) > 0) {
            $newArrayProductTotalUnit = array();
            foreach ($arrayProductTotalUnit as $key => $value) {
                if (!in_array($value, $newArrayProductTotalUnit))
                    array_push($newArrayProductTotalUnit, ['id' => $value['id'], 'value' => $value['value']]);
            }
        }

        if (count($newArrayProductTotalUnit) > 0) {
            foreach ($newArrayProductTotalUnit as $key => $value) {
                $languageIngredient = IngredientLanguage::where('id', $value['id'])->with('ingredient_only')->first();
                $total += (($languageIngredient->cost_price + (($languageIngredient->cost_price * $languageIngredient->increase) / 100)) *  $value['value']);
            }
        }
        $cartUser->total_price = $total;
        $result = $cartUser->save();
        return $result;
    }

    protected function totalCartAAAAAAAAAAAAAAAUUUUUUUUUUUUUUUUUUUUUUXXXXXXXXXXXXXXXXXXXXXXXXXX($userId)
    {


        $total = 0;
        $cartUser = Cart::where('user_id', $userId)->where('status_cart', 0)->first();
        $detailsCart = DetailsCart::where('cart_id', $cartUser->id)->with('sub_details')->get();
        $arrayProduct = [];
        foreach ($detailsCart as $valueDetailsCart) {
            foreach ($valueDetailsCart->sub_details as $valueSubDetailsCart) {
                if ($valueSubDetailsCart->status == 0) {
                    $arrayProduct[$valueSubDetailsCart->ingredient_language_id] = 0;
                }
            }
        }

        foreach ($detailsCart as $valueDetailsCart) {
            $selectPeron = $valueDetailsCart->selectPerson;
            $LanguageRecipe = LanguageRecipe::where('id', $valueDetailsCart->language_recipes_id)->with('recipe_only')->first();
            $minPerson = $LanguageRecipe->recipe_only->min_person;
            $statusFractional = 0;
            foreach ($valueDetailsCart->sub_details as $valueSubDetailsCart) {
                $languageIngredient = IngredientLanguage::where('id', $valueSubDetailsCart->ingredient_language_id)->with('ingredient_only')->first();
                if ($languageIngredient->ingredient_only->fractional == 0) {
                    $statusFractional = 1;
                }
            }
            foreach ($valueDetailsCart->sub_details as $valueSubDetailsCart) {

                $languageIngredient = IngredientLanguage::where('id', $valueSubDetailsCart->ingredient_language_id)->with('ingredient_only')->first();
                $ingredientsRecipe = IngredientsRecipe::where('ingredient_id', $languageIngredient->ingredient_id)->where('language_recipe_id', $valueDetailsCart->language_recipes_id)->first();

                if ($valueSubDetailsCart->status == 0) {
                    if ($statusFractional == 0) {
                        $arrayProduct[$valueSubDetailsCart->ingredient_language_id] += (($selectPeron * $ingredientsRecipe->quantity) / $minPerson);
                    } else {
                        $arrayProduct[$valueSubDetailsCart->ingredient_language_id] += ($selectPeron * $ingredientsRecipe->quantity);
                    }
                }
            }
        }
        $totalGeneral = 0;
        foreach ($arrayProduct as $keyId => $val) {
            $languageIngredient = IngredientLanguage::where('id', $keyId)->with('ingredient_only')->first();

            $valAbsolute =  intval(ceil($val / $languageIngredient->ingredient_only->unit_min));
            if ($val > 0) {
                $totalGeneral += (($languageIngredient->cost_price + (($languageIngredient->cost_price * $languageIngredient->increase) / 100)) * $valAbsolute);
            }
        }


        $cartUser->total_price = $totalGeneral;
        $result = $cartUser->save();
        return $result;
    }

    public function deleteUtensilRecipeCart(UtensilLanguage $idUtensil){
      /*   dd($idUtensil); */
        if (Auth::user()) {
            $lang = Auth::user()->language_id;
            $idUser = Auth::user()->id;
            $cart = Cart::where('user_id', $idUser)->where('status_cart', 0)->first();
            $detailsCart = DetailsCart::where('cart_id', $cart->id)->with('sub_details')->get();
            if(!is_null($detailsCart)){
                foreach ($detailsCart as $detailCart) {
                    if($detailCart->sub_details){

                        foreach ($detailCart->sub_details as $subDetail) {
                            if(!is_null($subDetail->utensil_language_id)){
                                if($subDetail->utensil_language_id === $idUtensil->id)
                                    $resultSubDetails = $subDetail->delete();

                            }
                        }
                    }
                    /*$resultSubDetails = $detailCart->sub_details()->delete(); */
                }
            }

            if ($resultSubDetails) {
                /* $this->totalCart($idUser); */
                $this->traitTotalCart($idUser);
                return response()->json([
                    'status' => '200',
                ]);
            }
            return response()->json([
                'status' => '400',
            ]);
        } else {
            $lang = session()->get('locale');
            if (is_null($lang)) {
                $lang = "pt-br";
            }
            $lang = Language::where('lang', $lang)->first();
            $lang = $lang->id;
        }
    }
    public function deleteRecipeCart(LanguageRecipe $idLangRecipe)
    {

        if (Auth::user()) {
            $lang = Auth::user()->language_id;
            $idUser = Auth::user()->id;
            $cart = Cart::where('user_id', $idUser)->where('status_cart', 0)->first();
            $detailsCart = DetailsCart::where('cart_id', $cart->id)->where('language_recipes_id', $idLangRecipe->id)->first();
            $resultSubDetails = $detailsCart->sub_details()->delete();
            $resultDetails = $detailsCart->delete();
            if ($resultDetails and $resultSubDetails) {
                /* $this->totalCart($idUser); */
                $this->traitTotalCart($idUser);

                return response()->json([
                    'status' => '200',
                ]);
            }
            return response()->json([
                'status' => '400',
            ]);
        } else {
            $lang = session()->get('locale');
            if (is_null($lang)) {
                $lang = "pt-br";
            }
            $lang = Language::where('lang', $lang)->first();
            $lang = $lang->id;
        }
    }


    public function listCart(Request $request)
    {

        $idLang = Auth::user()->language_id;
        /* $users = User::pluck('email','id'); */
        $notificationsCarts = CartNotifications::with('language')->with('status_take_cart')->where('language_id', $idLang)->get();
        $users = User::all();
        /* $StatusTakeCarts = StatusTakeCarts::pluck('description','id'); */
        $StatusTakeCarts = StatusTakeCarts::all();
        $statesMercadPago = StatusCarts::all();
        $orderTime = OrderTime::first();

        /*         $readyToSend = $request->readyToSend; */


        $btoFormFilter = $request->btoFormFilter;
        if ($btoFormFilter === 'reset') {
            if (auth()->user()->role_id === 2)
                return redirect('/carts/list');
            if (auth()->user()->role_id === 3)
                return redirect('/cartsCustomerSupport/list');
            if (auth()->user()->role_id === 4)
                return redirect('/cartsOrderAssembly/list');
            if (auth()->user()->role_id === 5)
                return redirect('/cartsOrderDispatch/list');
            if (auth()->user()->role_id === 6)
                return redirect('/cartsDelivery/list');
        }

        $orderNumber = $request->orderNumber;
        $userId = $request->userFind;
        $dateOrder = $request->dateOrder;



        $statusCart = intVal($request->statusCart);
        $stateMercadoPago = $request->stateMercadoPago;
        if ($dateOrder) {
            $partDate = explode('/', $dateOrder);
            $dateOrder = $partDate[2] . '-' . $partDate[1] . '-' . $partDate[0];
        }
        /*          dd($dateOrder); */
        $stateTakeCartId = $request->stateFind;

        $dateNow = Carbon::now();
        $dateFormat = $dateNow->format('Y-m-d');


        if ((auth()->user()->role_id === 2) or (auth()->user()->role_id === 3)) {
            $carts = HistoryCart::with('status_take_cart')->with(['mercadopago' => function ($queryMercadoPago) use ($orderNumber) {
                $queryMercadoPago->orderNumber($orderNumber)->get();
            }])->with('user')->findUser($userId)->findStateTakeCart($stateTakeCartId)->dateOrder($dateOrder)->stateCart($statusCart)->orderBy('date_shipping_retirement', 'DESC')->get();
        } else {
            /*  DB::connection()->enableQueryLog(); */

            $carts = HistoryCart::with('status_take_cart')->with(['mercadopago' => function ($queryMercadoPago) use ($orderNumber) {
                $queryMercadoPago->orderNumber($orderNumber)->get();
            }])->with('user')
                ->findUser($userId)
                ->findStateTakeCart($stateTakeCartId)
                ->dateOrder($dateOrder)
                ->stateCart($statusCart)
                ->whereDate('date_shipping_retirement', $dateFormat)
                ->orderBy('date_shipping_retirement', 'DESC')->get();

            /*  $queries = DB::getQueryLog();
            dd($queries); */
        }






        if ($dateOrder) {
            $dateOrder = $partDate[0] . '-' . $partDate[1] . '-' . $partDate[2];
        }
        /*   dd($carts); */
        /* ->readyToSend($readyToSend) */
        if (auth()->user()->role_id === 6)
            return view('admin.carts.list_delivery', compact('carts', 'users', 'StatusTakeCarts', 'userId', 'stateTakeCartId', 'orderNumber', 'dateOrder', 'statusCart', 'statesMercadPago', 'stateMercadoPago', 'orderTime', 'notificationsCarts'));
        if ((auth()->user()->role_id === 2) or (auth()->user()->role_id === 3))
            return view('admin.carts.list_admin_at', compact('carts', 'users', 'StatusTakeCarts', 'userId', 'stateTakeCartId', 'orderNumber', 'dateOrder', 'statusCart', 'statesMercadPago', 'stateMercadoPago', 'orderTime', 'notificationsCarts'));
        if (auth()->user()->role_id === 4)
            return view('admin.carts.list_order', compact('carts', 'users', 'StatusTakeCarts', 'userId', 'stateTakeCartId', 'orderNumber', 'dateOrder', 'statusCart', 'statesMercadPago', 'stateMercadoPago', 'orderTime', 'notificationsCarts'));
        if (auth()->user()->role_id === 5)
            return view('admin.carts.list_dispach', compact('carts', 'users', 'StatusTakeCarts', 'userId', 'stateTakeCartId', 'orderNumber', 'dateOrder', 'statusCart', 'statesMercadPago', 'stateMercadoPago', 'orderTime', 'notificationsCarts'));

        /* return view('admin.carts.list',compact('carts','users','StatusTakeCarts','userId','stateTakeCartId','orderNumber','dateOrder','statusCart','statesMercadPago','stateMercadoPago','orderTime','notificationsCarts')); */
    }


    public function detailsCart(HistoryCart $idCart)
    {
        $cart = HistoryCart::where('id', $idCart->id)->with('status_take_cart')->with('mercadopago')->first();
        /*         dd($cart->hourInitial.'---'.$cart->hourFinaly); */
        /*  dd($cart); */
        $userAccount = User::where('id', $idCart->user_id)->with('userdata')->first();


        if ($cart->type_origin == 'address') {
            $addressShipping = Address::where('id', $cart->address_offices_id)->with(['location' => function ($queryLocation) {
                $queryLocation->with('province')->get();
            }])->first();
        } else {
            $addressShipping = DeviveryPoints::where('id', $cart->address_offices_id)->with(['location' => function ($queryLocation) {
                $queryLocation->with('province')->get();
            }])->first();
        }


        $detailsCart = HistoryDetailsCart::where('historycart_id', $cart->id)->get();
        $calculoIngrediente = [];
        $arrayIdHistory = [];
        $ingredients = [];
        $recipes = [];
        foreach ($detailsCart as $valueDetailsCart) {
            $languageRecipe = LanguageRecipe::where('id', $valueDetailsCart->language_recipes_id)->first();
            array_push($recipes, [
                'description'   =>  $languageRecipe->description
            ]);
            $historySubDetail = HistorySubDetailsCart::where('histroydetailscart_id', $valueDetailsCart->id)->get();
            foreach ($historySubDetail as $valueSubDetailsCart) {
                if (!$valueSubDetailsCart->status) {
                    $calculoIngrediente[$valueSubDetailsCart->ingredient_language_id] = 0;
                    $arrayIdHistory[$valueSubDetailsCart->ingredient_language_id] = 0;
                }
            }
        }
        foreach ($detailsCart as $valueDetailsCart) {
            $historySubDetail = HistorySubDetailsCart::where('histroydetailscart_id', $valueDetailsCart->id)->get();
            foreach ($historySubDetail as $valueSubDetailsCart) {
                if (!$valueSubDetailsCart->status) {
                    $calculoIngrediente[$valueSubDetailsCart->ingredient_language_id] += $valueSubDetailsCart->cant_ingredient;
                    $arrayIdHistory[$valueSubDetailsCart->ingredient_language_id] = $valueSubDetailsCart->id;
                }
            }
        }
        foreach ($calculoIngrediente as $key => $value) {
            $languageIngredient = IngredientLanguage::where('id', $key)->with('ingredient_only')->first();
            $historySubDetail = HistorySubDetailsCart::where('id', $arrayIdHistory[$key])->first();

            $valAbsolute =  intval(ceil($value / $historySubDetail->unit_min));

            array_push($ingredients, [
                'totalIng'          =>  $valAbsolute,
                'type_unit'         =>  $historySubDetail->type_unit,
                'unit_min'          =>  $historySubDetail->unit_min,
                'description'       =>  $languageIngredient->description,
                'description_brand' =>  $languageIngredient->description_brand,
                'import'            =>  $historySubDetail->cost_price,

            ]);
        }
        /*         dd($cart->hourInitial.'---'.$cart->hourFinaly); */
        return view('admin.carts.cart', compact('cart', 'ingredients', 'addressShipping', 'userAccount', 'recipes'));
    }

    protected function saveHistoryCart($userId, $cartUser, $statusC, $collection_status)
    {

        /* ****************************************** CHECKKKKKKKKKKK THAT  ********************************************************** */


        $user = UsersData::where('user_id', $userId)->first();
        if ($cartUser->type_origin == 'address') {
            $address = Address::where('id', $cartUser->address_offices_id)->first();
            $location_id = $address->location_id;
            $street = $address->street;
            $number = $address->number;
            /* $hourInitial = $address->hourInitial;
            $hourFinaly = $address->hourFinaly; */
            $hourInitial = $cartUser->hourInitial;
            $hourFinaly = $cartUser->hourFinaly;
        } else {
            $deliveryPoint = DeviveryPoints::where('id', $cartUser->address_offices_id)->first();
            $location_id = $deliveryPoint->location_id;
            $street = $deliveryPoint->street;
            $number = $deliveryPoint->number;
            $hourInitial = $deliveryPoint->hourInitial;
            $hourFinaly = $deliveryPoint->hourFinaly;
        }
        $historyCart = new HistoryCart();
        $historyCart->cart_id = $cartUser->id;
        $historyCart->date_cart = $cartUser->date_cart;
        $historyCart->date_shipping_retirement = $cartUser->date_shipping_retirement . ' ' . $hourInitial;
        $historyCart->user_id = $cartUser->user_id;
        $historyCart->statuscart_id = $statusC;

        if ($collection_status === 'approved' or $collection_status === 'in_process') {
            $historyCart->status_take_cart_id = 1;
        } else {
            $historyCart->status_take_cart_id = null;
        }

        $historyCart->language_id = $cartUser->language_id;
        $historyCart->status_cart = $cartUser->status_cart;
        $historyCart->total_price = $cartUser->total_price;
        $historyCart->shipping_price = $cartUser->shipping_price;
        $historyCart->address_offices_id = $cartUser->address_offices_id;
        $historyCart->type_origin = $cartUser->type_origin;
        $historyCart->first_last_name = $user->last_name . ', ' . $user->first_name;
        $historyCart->location_id = $location_id;
        $historyCart->street = $street;
        $historyCart->number = $number;
        $historyCart->hourInitial = $hourInitial;
        $historyCart->hourFinaly = $hourFinaly;
        $historyCart->save();

        $detailsCartDataBase = DetailsCart::where('cart_id', $cartUser->id)->get();
        if (!is_null($detailsCartDataBase)) {
            foreach ($detailsCartDataBase as $valueDetailsCart) {

                $LanguageRecipe = LanguageRecipe::where('id', $valueDetailsCart->language_recipes_id)->with('recipe_only')->first();

                $descriptionRecipe = $LanguageRecipe->description;
                $minPerson = $LanguageRecipe->recipe_only->min_person;
                $statusFractional = 1;
                $selectPeron = $valueDetailsCart->selectPerson;
                $subDetailsCart = SubdetailsDetailsCarts::where('details_cart_id', $valueDetailsCart->id)->get();

                foreach ($subDetailsCart as $valueSubDetailsCart) {
                    $languageIngredient = IngredientLanguage::where('id', $valueSubDetailsCart->ingredient_language_id)->with('ingredient_only')->first();
                    if (!$languageIngredient->ingredient_only->fractional) {
                        $statusFractional = 0;
                    }
                }


                $historyDetailsCart = new HistoryDetailsCart();
                $historyDetailsCart->historycart_id = $historyCart->id;
                $historyDetailsCart->selectPerson = $selectPeron;
                $historyDetailsCart->cart_id = $valueDetailsCart->cart_id;
                $historyDetailsCart->language_recipes_id = $valueDetailsCart->language_recipes_id;
                $historyDetailsCart->description_recipe = $descriptionRecipe;
                $historyDetailsCart->fractional = $statusFractional;
                $historyDetailsCart->save();

                $calculo = 0;
                if (!is_null($historyDetailsCart)) {
                    $subDetailsCart = SubdetailsDetailsCarts::where('details_cart_id', $valueDetailsCart->id)->get();
                    foreach ($subDetailsCart as $valueSubDetailsCart) {
                        $languageIngredient = IngredientLanguage::where('id', $valueSubDetailsCart->ingredient_language_id)->with('ingredient_only')->first();
                        $ingredientsRecipe = IngredientsRecipe::where('ingredient_id', $languageIngredient->ingredient_id)->where('language_recipe_id', $valueDetailsCart->language_recipes_id)->first();
                        if (!$valueSubDetailsCart->status) {

                            if ($statusFractional == 1) {

                                $calculo = ($selectPeron * $ingredientsRecipe->quantity) / $minPerson;
                            } else {

                                $calculo = $selectPeron * $ingredientsRecipe->quantity;
                            }
                        }

                        $historySubDeteailsCart = new HistorySubDetailsCart();
                        $historySubDeteailsCart->histroydetailscart_id = $historyDetailsCart->id;
                        $historySubDeteailsCart->ingredient_language_id = $valueSubDetailsCart->ingredient_language_id;
                        $historySubDeteailsCart->status = $valueSubDetailsCart->status;
                        $historySubDeteailsCart->total_unit = $valueSubDetailsCart->total_unit;
                        $historySubDeteailsCart->unit_min = $languageIngredient->ingredient_only->unit_min;
                        $historySubDeteailsCart->type_unit = $languageIngredient->ingredient_only->type_unit;
                        $historySubDeteailsCart->description_ingredient = $languageIngredient->description;
                        $historySubDeteailsCart->cant_ingredient = $calculo;
                        $historySubDeteailsCart->cost_price = $languageIngredient->cost_price + (($languageIngredient->increase * $languageIngredient->cost_price) / 100);
                        $historySubDeteailsCart->save();
                    }
                }
            }
        }
    }


    public function updateStateTakeCart(HistoryCart $cart, Request $request)
    {
        if (!is_null($request->stateCart)) {
            $cart->status_take_cart_id = $request->stateCart;
            $cart->save();
            if (!is_null($cart)) {
                event(new StateCartEvent($cart->id));
                if ($request->stateCart === 5) {
                    $description = __('messages.Received at delivery point');
                } elseif ($request->stateCart === 6) {
                    $description = __('messages.Received the customer');
                } elseif ($request->stateCart === 7) {
                    $description = __('messages.The client did not receive');
                } else {
                    $description = __('messages.Not received at the delivery point');
                }

                return response()->json([
                    'status'        => '200',
                    'stateCart'     =>  $request->stateCart,
                    'description'   => $description,
                ]);
            }
        } else {
            switch ($cart->status_take_cart_id) {
                case 1:
                    $this->updateStockIngredientHistory($cart);
                    $cart->status_take_cart_id = 2;
                    $cart->save();
                    if (!is_null($cart)) {
                        event(new StateCartEvent($cart->id));
                        $description = __('messages.Assembling Request');
                        return response()->json([
                            'status'        => '200',
                            'stateCart'     =>  2,
                            'description'   => $description,

                        ]);
                    }
                    break;
                case 2:
                    $cart->status_take_cart_id = 3;
                    $cart->save();
                    if (!is_null($cart)) {
                        event(new StateCartEvent($cart->id));
                        $description = __('messages.Ready To Send');
                        return response()->json([
                            'status'        => '200',
                            'stateCart'     =>  3,
                            'description'   => $description,

                        ]);
                    }
                    break;
                case 3:
                    $cart->status_take_cart_id = 4;
                    $cart->save();
                    if (!is_null($cart)) {
                        event(new StateCartEvent($cart->id));
                        $description = __('messages.Received the customer');
                        return response()->json([
                            'status'        => '200',
                            'stateCart'     =>  4,
                            'description'   => $description,

                        ]);
                    }
                    break;
                case 4:
                    $cart->status_take_cart_id = 5;
                    $cart->save();
                    if (!is_null($cart)) {
                        event(new StateCartEvent($cart->id));
                        $description = __('messages.The client did not receive');
                        return response()->json([
                            'status'        => '200',
                            'stateCart'     =>  5,
                            'description'   => $description,

                        ]);
                    }
                    break;
            }
        }


        return response()->json([
            'status'        => '400',
            'description'   => '',

        ]);
    }
    public function checkStock(HistoryCart $idCart, Request $request)
    {
        /*       dd($request); */

        $cart = HistoryCart::where('id', $idCart->id)->first();



        $detailsCart = HistoryDetailsCart::where('historycart_id', $cart->id)->get();
        $calculoIngrediente = [];
        $arrayIdHistory = [];
        $ingredients = [];
        $recipes = [];

        foreach ($detailsCart as $valueDetailsCart) {
            $historySubDetail = HistorySubDetailsCart::where('histroydetailscart_id', $valueDetailsCart->id)->get();
            foreach ($historySubDetail as $valueSubDetailsCart) {
                if (!$valueSubDetailsCart->status) {
                    $calculoIngrediente[$valueSubDetailsCart->ingredient_language_id] = 0;
                    $arrayIdHistory[$valueSubDetailsCart->ingredient_language_id] = 0;
                }
            }
        }

        foreach ($detailsCart as $valueDetailsCart) {
            $historySubDetail = HistorySubDetailsCart::where('histroydetailscart_id', $valueDetailsCart->id)->get();
            foreach ($historySubDetail as $valueSubDetailsCart) {
                if (!$valueSubDetailsCart->status) {
                    $calculoIngrediente[$valueSubDetailsCart->ingredient_language_id] += $valueSubDetailsCart->cant_ingredient;
                    $arrayIdHistory[$valueSubDetailsCart->ingredient_language_id] = $valueSubDetailsCart->id;
                }
            }
        }
        /*     dd($calculoIngrediente); */
        foreach ($calculoIngrediente as $key => $value) {

            $historySubDetail = HistorySubDetailsCart::where('id', $arrayIdHistory[$key])->first();
            $valAbsolute =  intval(ceil($value / $historySubDetail->unit_min));
            $ingredientLanguage = IngredientLanguage::where('id', $key)->first();
            $stockActuality = BranchOfficeIngredient::where('ingredient_id', $ingredientLanguage->ingredient_id)->first();
            /*                 if(!is_null($stockActuality))
                    echo($stockActuality->stock); */
            if (!is_null($stockActuality)) {
                if ($stockActuality->stock <= $valAbsolute && $stockActuality->stock > 0) {
                    $quantity = $stockActuality->stock - $valAbsolute;
                    array_push($ingredients, [
                        'description'               =>  $ingredientLanguage->description,
                        'quantity'                  =>  $quantity,
                    ]);
                }
            }
        }
        /*             dd($ingredients); */
        /*            dd('stop'); */

        if (count($ingredients) > 0) {
            return [
                'status'            =>  '200',
                'ingredients'       => $ingredients,
                'statusActualyCart' =>  $cart->status_take_cart_id,
                'statusStockIngredient' =>  count($ingredients) == count($calculoIngrediente) ? true : false
            ];
        }
        return [
            'status'        =>  '400',
            'ingredients' => 0,
            'statusActualyCart' =>  0,
        ];
    }
    public function returnOrder(HistoryCart $idCart)
    {
        $mercadoPago = MercadopagoPayments::where('cart_id', $idCart->cart_id)->first();
        if ($idCart->status_take_cart_id === 3) {
            $idCart->status_take_cart_id = 2;
            $idCart->save();
            event(new StateReturnOrder($mercadoPago->merchant_order_id, $idCart->id));
        }
        if (!is_null($idCart)) {
            return [
                'status' => '200',
            ];
        }
        return [
            'status' => '400',
        ];
    }


    public function historyTakeCart(Request $request)
    {
        $idHistoryCart = $request->idHistory;
        $idUser = $request->idUser;
        $historyCart = HistoryCart::where('id', $idHistoryCart)->where('user_id', $idUser)->first();
        if (!is_null($historyCart)) {
            return response()->json([
                'status' => '200',
            ]);
        }

        return response()->json([
            'status' => '400',
        ]);
    }

    public function userHistoryTakeCart(HistoryCart $idCart)
    {

        $historyCart = HistoryCart::where('id', $idCart->id)->with('status_take_cart')->first();
        if (!is_null($historyCart)) {
            switch ($historyCart->status_take_cart->description) {
                case 'To Assemble':
                    $msj = 'To Assemble';
                    $statusSpan = 'stateToAssemble';
                    break;
                case 'Assembling Request':
                    $msj = 'Assembling Request';
                    $statusSpan = 'stateAssemblingRequest';
                    break;
                case 'Ready To Send':
                    $msj = 'Ready To Send';
                    $statusSpan = 'stateReadyToSend';
                    break;
                case 'Sent':
                    $msj = 'Sent';
                    $statusSpan = 'stateSent';
                    break;
                case 'Received the customer':
                    $msj = 'Received the customer';
                    $statusSpan = 'stateReceivedOk';
                    break;
                case 'Received at delivery point':
                    $msj = 'Received at delivery point';
                    $statusSpan = 'stateReceivedOk';
                    break;
                case 'The client did not receive':
                    $msj = 'The client did not receive';
                    $statusSpan = 'stateReceivedFail';
                    break;

                case 'Not received at the delivery point':
                    $msj = 'Not received at the delivery point';
                    $statusSpan = 'stateReceivedFail';
                    break;
            }







            $description = __('messages.' . $msj);

            return response()->json([
                'status' => '200',
                'idHistoryCart' =>  $historyCart->id,
                'stateTakeCart' =>  $description,
                'statusSpan'    =>  $statusSpan,
            ]);
        }
        return response()->json([
            'status' => '400',
            'idHistoryCart' => '',
            'stateTakeCart' => '',
            'statusSpan'    => '',
        ]);
    }


    public function updateStockIngredientBIZZZZZZZZZZZZZ(Request $request)
    {
        $arrayTicket = $request->arrayTicket;
        $long = count($arrayTicket);
        $idIngredient = 0;
        $totalIngredient = 0;
        for ($x = 0; $x < $long; $x++) {
            foreach ($arrayTicket[$x] as $key => $valTicket) {
                if ($key === 'idIngredient') {
                    $idIngredient = $valTicket;
                }
                if ($key === 'totalUnidad') {
                    $totalIngredient = $valTicket;
                }
            }
            $stockCurrent = BranchOfficeIngredient::where('branch_office_id', 1)->where('ingredient_id', $idIngredient)->first();
            if (!is_null($stockCurrent)) {
                $stockCurrent -= $totalIngredient;
                $stockCurrent->save();
            }
        }

        if (!is_null($stockCurrent)) {
            return response()->json([
                'status' => '200',
            ]);
        }
        return response()->json([
            'status' => '400',
        ]);
    }

    protected function updateStockIngredient($cartUser)
    {
        $detailsHistoryCart = HistoryDetailsCart::where('cart_id', $cartUser->id)->get();
        $arrayProduct = [];
        if (!is_null($detailsHistoryCart)) {
            foreach ($detailsHistoryCart as $valDetailsHistory) {
                $subDetailsHistory = HistorySubDetailsCart::where('histroydetailscart_id', $valDetailsHistory->id)->get();
                foreach ($subDetailsHistory as $valSubDetailsCart) {
                    if ($valSubDetailsCart->status == 0) {
                        $arrayProduct[$valSubDetailsCart->ingredient_language_id] = 0;
                    }
                }
            }
            foreach ($detailsHistoryCart as $valDetailsHistory) {

                $subDetailsHistory = HistorySubDetailsCart::where('histroydetailscart_id', $valDetailsHistory->id)->get();
                foreach ($subDetailsHistory as $valSubDetailsCart) {
                    if ($valSubDetailsCart->status == 0) {
                        $arrayProduct[$valSubDetailsCart->ingredient_language_id] += $valSubDetailsCart->cant_ingredient;
                    }
                }
            }

            foreach ($arrayProduct as $keyId => $val) {
                $languageIngredient = IngredientLanguage::where('id', $keyId)->with('ingredient_only')->first();

                $valAbsolute =  intval(ceil($val / $languageIngredient->ingredient_only->unit_min));
                $stockCurrent = BranchOfficeIngredient::where('branch_office_id', 1)->where('ingredient_id', $languageIngredient->ingredient_id)->first();
                if (!is_null($stockCurrent)) {
                    $stockCurrent->stock -= $valAbsolute;
                    $stockCurrent->save();
                }
            }
        }
    }
    protected function updateStockIngredientHistory($cart)
    {
        $detailsHistoryCart = HistoryDetailsCart::where('cart_id', $cart->cart_id)->get();
        $arrayProduct = [];
        if (!is_null($detailsHistoryCart)) {
            foreach ($detailsHistoryCart as $valDetailsHistory) {
                $subDetailsHistory = HistorySubDetailsCart::where('histroydetailscart_id', $valDetailsHistory->id)->get();
                foreach ($subDetailsHistory as $valSubDetailsCart) {
                    if ($valSubDetailsCart->status == 0) {
                        $arrayProduct[$valSubDetailsCart->ingredient_language_id] = 0;
                    }
                }
            }
            foreach ($detailsHistoryCart as $valDetailsHistory) {

                $subDetailsHistory = HistorySubDetailsCart::where('histroydetailscart_id', $valDetailsHistory->id)->get();
                foreach ($subDetailsHistory as $valSubDetailsCart) {
                    if ($valSubDetailsCart->status == 0) {
                        $arrayProduct[$valSubDetailsCart->ingredient_language_id] += $valSubDetailsCart->cant_ingredient;
                    }
                }
            }
            $arrayEvent = [];
            foreach ($arrayProduct as $keyId => $val) {
                $languageIngredient = IngredientLanguage::where('id', $keyId)->with('ingredient_only')->first();

                $valAbsolute =  intval(ceil($val / $languageIngredient->ingredient_only->unit_min));
                $stockCurrent = BranchOfficeIngredient::where('branch_office_id', 1)->where('ingredient_id', $languageIngredient->ingredient_id)->first();
                if (!is_null($stockCurrent)) {
                    $stockCurrent->stock -= $valAbsolute;
                    if ($stockCurrent->stock <= $stockCurrent->stockAlert) {
                        $arrayEvent[$stockCurrent->id] = $languageIngredient->id;
                    }
                    $stockCurrent->save();
                }
            }

            if ($arrayEvent != '') {
                event(new StateStockAlert($arrayEvent));
            }
        }
    }

    protected function controlStockIngredient($cartUser)
    {







        /*
        foreach ($valueDetailsCart->sub_details as $valueSubDetailsCart) {

            $languageIngredient = IngredientLanguage::where('id',$valueSubDetailsCart->ingredient_language_id)->with('ingredient_only')->first();
            $ingredientsRecipe = IngredientsRecipe::where('ingredient_id',$languageIngredient->ingredient_id)->where('language_recipe_id',$valueDetailsCart->language_recipes_id)->first();

            if($valueSubDetailsCart->status == 0){
                if($statusFractional==0){
                    $arrayProduct[$valueSubDetailsCart->ingredient_language_id] += (($selectPeron*$ingredientsRecipe->quantity)/$minPerson);
                }else{
                    $arrayProduct[$valueSubDetailsCart->ingredient_language_id] += ($selectPeron*$ingredientsRecipe->quantity);
                }
            }
        } */







        $detailsCart = DetailsCart::where('cart_id', $cartUser->id)->with('sub_details')->get();
        $arrayProduct = [];
        if (!is_null($detailsCart)) {
            foreach ($detailsCart as $valueDetailsCart) {
                foreach ($valueDetailsCart->sub_details as $valueSubDetailsCart) {
                    if ($valueSubDetailsCart->status == 0) {
                        $arrayProduct[$valueSubDetailsCart->ingredient_language_id] = 0;
                    }
                }
            }
            foreach ($detailsCart as $valDetailsCart) {
                $selectPeron = $valDetailsCart->selectPerson;
                $LanguageRecipe = LanguageRecipe::where('id', $valDetailsCart->language_recipes_id)->with('recipe_only')->first();
                $minPerson = $LanguageRecipe->recipe_only->min_person;
                $statusFractional = 0;


                foreach ($valDetailsCart->sub_details as $valueSubDetailsCart) {
                    $languageIngredient = IngredientLanguage::where('id', $valueSubDetailsCart->ingredient_language_id)->with('ingredient_only')->first();
                    if ($languageIngredient->ingredient_only->fractional == 0) {
                        $statusFractional = 1;
                    }
                }
                foreach ($valDetailsCart->sub_details as $valueSubDetailsCart) {

                    $languageIngredient = IngredientLanguage::where('id', $valueSubDetailsCart->ingredient_language_id)->with('ingredient_only')->first();
                    $ingredientsRecipe = IngredientsRecipe::where('ingredient_id', $languageIngredient->ingredient_id)->where('language_recipe_id', $valDetailsCart->language_recipes_id)->first();

                    if ($valueSubDetailsCart->status == 0) {
                        if ($statusFractional == 0) {
                            $arrayProduct[$valueSubDetailsCart->ingredient_language_id] += (($selectPeron * $ingredientsRecipe->quantity) / $minPerson);
                        } else {
                            $arrayProduct[$valueSubDetailsCart->ingredient_language_id] += ($selectPeron * $ingredientsRecipe->quantity);
                        }
                    }
                }
                /*                 echo('<br>----------------------------------------------<br>');
                echo('<br>'.$valDetailsCart->sub_details.'<br>'); */








                /*  $subDetailsCart = SubdetailsDetailsCarts::where('details_cart_id',$valDetailsCart->id)->get();
                if(!is_null($subDetailsCart)){
                    foreach ($subDetailsCart as $valSubDetailsCart) {
                        echo('<br>'.$valSubDetailsCart.'<br>');
                    }
                }
                echo('<br>----------------------------------------------<br>'); */
            }

            $arrayDelete = $arrayProduct;
            foreach ($arrayProduct as $keyId => $val) {
                $languageIngredient = IngredientLanguage::where('id', $keyId)->with('ingredient_only')->first();
                $valAbsolute =  intval(ceil($val / $languageIngredient->ingredient_only->unit_min));
                $stockIngredient = BranchOfficeIngredient::where('ingredient_id', $languageIngredient->ingredient_id)->first();
                /*                 echo('<br> KEY: '.$keyId.'  val: '.$valAbsolute.'<br>'); */
                if ($valAbsolute > $stockIngredient->stock) {
                    $arrayDelete[$keyId] = 1;
                } else {
                    $arrayDelete[$keyId] = 0;
                }
                /*                 if($val>0){
                    $totalGeneral += (($languageIngredient->cost_price + (($languageIngredient->cost_price * $languageIngredient->increase)/100)) * $valAbsolute);
                } */
            }

            /*             echo('<br>----------------------------------------------<br>');
            print_r($arrayProduct); */
            /*             echo('<br>----------------------------------------------<br>');
            print_r($arrayDelete);
            echo('<br>----------------------------------------------<br>'); */
            /*             $arrayIngredientToDelete = []; */

            foreach ($detailsCart as $valueDetailsCart) {
                foreach ($valueDetailsCart->sub_details as $valueSubDetailsCart) {
                    foreach ($arrayDelete as $keyId => $val) {
                        if ($val == 1) {
                            //echo('<br> subdetails'.$valueSubDetailsCart);
                            if ($valueSubDetailsCart->ingredient_language_id === $keyId) {
                                //echo('<br>sacar ingrediente KEY: '.$keyId.' ----status '.$valueSubDetailsCart->status.' CAMBIAR STATUS ID SUB DETAILS: '.$valueSubDetailsCart->id);
                                $valueSubDetailsCart->status = 1;
                                $valueSubDetailsCart->save();
                            }
                        }
                    }
                }
            }
            /* echo('<br>----------------------------------------------<br>'); */
        }

        //dd($cartUser);
    }

    function updateCart(Request $request)
    {
        $status = true;
        $userId = Auth::user()->id;
        $langId = Auth::user()->language_id;
        $cartUser = Cart::where('user_id', $userId)->where('status_cart', 0)->where('language_id', $langId)->first();
        $detailsCart = DetailsCart::where('cart_id', $cartUser->id)->with('sub_details')->get();
        if (!is_null($request->ingredientIdArray)) {
            $count = count($request->ingredientIdArray);
            for ($i = 0; $i < $count; $i++) {
                if (!is_null($detailsCart)) {
                    foreach ($detailsCart as $valDetailsCart) {
                        $ingredientLanguage = IngredientLanguage::where('ingredient_id', $request->ingredientIdArray[$i])->where('language_id', $langId)->first();
                        if (!is_null($ingredientLanguage)) {
                            $subDetailsCart = SubdetailsDetailsCarts::where('details_cart_id', $valDetailsCart->id)->where('ingredient_language_id', $ingredientLanguage->id)->first();
                            if (!is_null($subDetailsCart)) {
                                $subDetailsCart->total_unit = $request->ingredientTotalUnitArray[$i];
                                $result = $subDetailsCart->save();
                                if (is_null($result)) {
                                    $status = false;
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($status) {
            return [
                'status' => 200
            ];
        }
        return [
            'status' => 400
        ];
    }
}
