<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\nucooks\entities\LanguageRecipe;
use App\nucooks\entities\FavoritesRecipesUser;
use App\nucooks\entities\Cart;
use App\nucooks\entities\DetailsCart;
use App\nucooks\entities\SubdetailsDetailsCarts;
use App\nucooks\entities\IngredientsRecipe;
use App\nucooks\entities\Ingredient;
use App\nucooks\entities\IngredientLanguage;
use App\nucooks\entities\LanguageRecipeUtensil;
use App\nucooks\entities\Recipe;
use App\nucooks\entities\UtensilLanguage;
use Illuminate\Support\Facades\DB;
use App\Traits\CalculateCart;
class FavoritsController extends Controller
{
    use CalculateCart;
    public function favorits(LanguageRecipe $idLanguageRecipe){
        $flag = true;
        $existFavoritUser = FavoritesRecipesUser::where('user_id',Auth::user()->id)->where('language_recipe_id',$idLanguageRecipe->id)->first();
/*         $type = ""; */
        if(is_null($existFavoritUser)){
            $favoritUser = new FavoritesRecipesUser();
            $favoritUser->user_id = Auth::user()->id;
            $favoritUser->language_recipe_id = $idLanguageRecipe->id;
            $favoritUser->save();
/*             $type="add"; */
        }else{
            $flag = null;
            $existFavoritUser->delete();
/*             $type="delete"; */
        }
        return response()->json([
            'status' => '200',
            'flag'  => $flag,
/*             'type'  =>  $type */
            ]);
    }

    public function favoritList(){
        return view('users.favorites.index');
    }

    public function delete(Request $request){
        $idUser = Auth::user()->id;
        $status='401';
        if(!empty($request->elementRecipes)){
            $status = '400';
            foreach ($request->elementRecipes as $idLanguageRecipe) {
                $existFavoritUser = FavoritesRecipesUser::where('user_id',$idUser)->where('language_recipe_id',$idLanguageRecipe)->first();
                if(!is_null($existFavoritUser)){
                    $existFavoritUser->delete();
                }
                $status='200';
            }
        }

        return response()->json([
            'status' => $status,
            ]);
    }


/*     public function showRecipe(){
            $langId = Auth::user()->language_id;
            $idUser = Auth::user()->id;
            $statusFavorites=0;
            $recipeStepLanguage =  LanguageRecipe::with(['favorit' => function($queryFavorit) use($idUser) {
                $queryFavorit->where('user_id',$idUser)->get();
            }])->with('subcategory')->with(['recipe_only' => function($query){$query->with('images')->get();}])->with('recipe_steps')->with('ingredients')->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('language_id',$langId)->get();


            foreach ($recipeStepLanguage as $valueRecipe) {
                if(!is_null($valueRecipe->favorit)){
                    $statusFavorites=1;
                }
            }

            return response()->json([
                'status' => '200',
                'statusFavoritesRecipe' => $statusFavorites,
                'recipies' => $recipeStepLanguage]);




    } */

    public function addCart(Request $request){
        dd($request);
        DB::beginTransaction();

        $userId = Auth::user()->id;
        $langId = Auth::user()->language_id;

        $cartUser = Cart::where('user_id',$userId)->where('status_cart',0)->first();

        if(is_null($cartUser)){

            $cartUser = new Cart();
            $cartUser->date_cart  = date('Y-m-d');
            $cartUser->user_id  = $userId;
            $cartUser->language_id = $langId;
            $cartUser->status_cart = 0;
/*             $cartUser->total_price = $total; */
            $cartUser->save();
        }








        foreach ($request->objectCart as $key => $value) {
            if($key==='languageRecipeId'){
                $languageRecipeId = $value;
            }
            if($key==='selectPerson'){
                $selectPerson = $value;
            }
            if($key==='idR'){
                $idR = $value;
            }
        }





            $detailsCart = DetailsCart::where('language_recipes_id',$languageRecipeId)->where('cart_id',$cartUser->id)->first();

            if(is_null($detailsCart)){
                $detailsCart = new DetailsCart();
                $detailsCart->selectPerson = $selectPerson;
                $detailsCart->cart_id                           = $cartUser->id;
                $detailsCart->language_recipes_id               = $languageRecipeId;
                $detailsCart->save();
            }else{
                $detailsCart->selectPerson = $selectPerson;
                $detailsCart->save();
            }






          /*   dd($request->ingredientCart); */
            $longitud = count($request->ingredientCart);

            for($x=0;$x<$longitud;$x++){

                $ingLangIng='';
                $status = false;
                foreach ($request->ingredientCart[$x] as $key => $value) {
                    if($key=='ingredientLanguageId'){
                        $ingLangIng = $value;
                    }

                    if($key=='status'){
                        if($value){
                            $status=true;
                        }
                    }

                   if($key=='ingredient_id'){
                       $ingredient_id=$value;
                   }

                }

               /*  echo('<br> X: '.$x.' Status: '.$status.'   detailId: '.$detailsCart->id.'  ingredient_language_id: '.$ingLangIng.'  ingredient ID: '.$ingredient_id); */
/*                 echo('<br>ingredient ID '.$ingLangIng.'----status---'.$status); */

                    $subDetailsCart = SubdetailsDetailsCarts::where('details_cart_id',$detailsCart->id)->where('ingredient_language_id',$ingLangIng)->first();

                    if(is_null($subDetailsCart)){
                       /*  echo('                              create -------- '); */
                        $subDetailsCart = new SubdetailsDetailsCarts();
                        $subDetailsCart->details_cart_id = $detailsCart->id;
                        $subDetailsCart->ingredient_language_id = $ingLangIng;
                        $subDetailsCart->utensil_language_id = null;
                        $subDetailsCart->status = $status;
                     }else{
                       /*  echo('                              update -------- '); */
                        $subDetailsCart->status = $status;
                    }
                    $result = $subDetailsCart->save();
                   /*  echo('<br> X: '.$x.' Status: '.$status.'  ----result: '.$result); */
                    /* .'   detailId: '.$detailsCart->id.'  ingredient_language_id: '.$ingLangIng */
            }



            $longitudUtensil = count($request->utensilLanguage);

            for($x=0;$x<$longitudUtensil;$x++){

                $utensilLang='';
                $status = false;
                foreach ($request->utensilLanguage[$x] as $key => $value) {
                    if($key=='utensilLanguageId'){
                        $utensilLang = $value;
                    }

                   /*  if($key=='status'){
                        if($value){
                            $status=true;
                        }
                    } */

                   if($key=='utensil_id'){
                       $utensil_id=$value;
                   }

                }

            /*     dd($utensilLang); */
               /*  echo('<br> X: '.$x.' Status: '.$status.'   detailId: '.$detailsCart->id.'  ingredient_language_id: '.$ingLangIng.'  ingredient ID: '.$ingredient_id); */
/*                 echo('<br>ingredient ID '.$ingLangIng.'----status---'.$status); */

                    $subDetailsCart = SubdetailsDetailsCarts::where('details_cart_id',$detailsCart->id)->where('utensil_language_id',$utensilLang)->first();
                    if(is_null($subDetailsCart)){
                        $subDetailsCart = new SubdetailsDetailsCarts();
                        $subDetailsCart->details_cart_id = $detailsCart->id;
                        $subDetailsCart->ingredient_language_id = null;
                        $subDetailsCart->utensil_language_id = $utensilLang;
                        $subDetailsCart->status = $status;
                     }else{
                        $subDetailsCart->status = $status;
                    }
                    $result = $subDetailsCart->save();
            }



/*             dd($request); */
           /*  dd($request->ingredientCart); */
            $this->controlDeleteRecipeOnly($detailsCart);

            /* $resultTotal = $this->totalCart($userId); */
            $resultTotal = $this->traitTotalCart($userId);


/*             dd($request->utensilLanguage); */

            if($resultTotal){
                /* dd('stop'); */
                DB::commit();
                return response()->json([
                    'status' => '200',
                ]);
            }



    }
    public function updateStockCart(Request $request){
        DB::beginTransaction();
        $userId = Auth::user()->id;
        $langId = Auth::user()->language_id;
        $cartUser = Cart::where('user_id',$userId)->where('status_cart',0)->where('language_id',$langId)->first();
        $detailsCart = DetailsCart::where('cart_id',$cartUser->id)->get();

        $statusSave = 0;
        $longitud = count($request->ingredientCart);

        for($x=0;$x<$longitud;$x++){
            foreach ($request->ingredientCart[$x] as $key => $value) {
                if($key=='id'){
                    $idIngredientLanguage = $value;
/*                     echo('<br>id discriminados '.$idIngredientLanguage); */
                }

           }


           foreach ($detailsCart as $detailCart) {
                $subDetailsCart = SubdetailsDetailsCarts::where('details_cart_id',$detailCart->id)->where('ingredient_language_id',$idIngredientLanguage)->first();
/*                 echo('<br>details cart: '.$detailCart->id.'  id ingrediente: '.$idIngredientLanguage.' -subdetails -> '.$subDetailsCart); */
                if(!is_null($subDetailsCart)){
                    $subDetailsCart->status = 1;
                    $subDetailsCart->save();
                    if($subDetailsCart){
                        $statusSave = 1;
                    }
                }
           }
        }
/*         dd('---> '.$statusSave); */
        $this->controlDeleteRecipeAll($detailsCart);
        /* $resultTotal = $this->totalCart($userId); */
        $resultTotal = $this->traitTotalCart($userId);
        if($statusSave && $resultTotal){
            DB::commit();
            return response()->json([
                'status' => '200',
            ]);
        }else{
            return response()->json([
                'status' => '400',
            ]);
        }
    }



    public function countFavorits(){
        $favoritsUser = FavoritesRecipesUser::where('user_id',Auth::user()->id)->count();
        if($favoritsUser){
            return response()->json([
                    'status' => '200',
                    'countFavorits' => $favoritsUser]);

        }
        return response()->json([
                'status' => '400',
                'countFavorits' => 0]);
    }





            protected function totalCartAAAAAAAAAAAAAUUUUUUUUUUUUUXXXXXXXXXXXXXXXXXXX($userId){


                $total = 0;
                $cartUser = Cart::where('user_id',$userId)->where('status_cart',0)->first();
                $detailsCart = DetailsCart::where('cart_id',$cartUser->id)->with('sub_details')->get();

                $arrayProductIngredient = [];
                $arrayProductUtensil = [];

                foreach ($detailsCart as $valueDetailsCart) {
                    foreach ($valueDetailsCart->sub_details as $valueSubDetailsCart) {
                        if(!is_null($valueSubDetailsCart->ingredient_language_id)){
                            if($valueSubDetailsCart->status == 0){
                                $arrayProductIngredient[$valueSubDetailsCart->ingredient_language_id] =0;
                            }
                        }
                        if(!is_null($valueSubDetailsCart->utensil_language_id)){
                            if($valueSubDetailsCart->status == 0){
                                $arrayProductUtensil[$valueSubDetailsCart->utensil_language_id ] =0;
                            }
                        }
                    }

                }

/*   dd($detailsCart); */

                foreach ($detailsCart as $valueDetailsCart) {
                    $selectPeron = $valueDetailsCart->selectPerson;
                    $LanguageRecipe = LanguageRecipe::where('id',$valueDetailsCart->language_recipes_id)->with('recipe_only')->first();
                    $minPerson = $LanguageRecipe->recipe_only->min_person;
                    $statusFractional = 0;
                    foreach ($valueDetailsCart->sub_details as $valueSubDetailsCart) {
                        if(!is_null($valueSubDetailsCart->ingredient_language_id )){
                            $languageIngredient = IngredientLanguage::where('id',$valueSubDetailsCart->ingredient_language_id)->with('ingredient_only')->first();
                            if($languageIngredient->ingredient_only->fractional==0){
                                $statusFractional = 1;
                            }
                        }
                    }
                    foreach ($valueDetailsCart->sub_details as $valueSubDetailsCart) {
                        if(!is_null($valueSubDetailsCart->ingredient_language_id )){
                            $languageIngredient = IngredientLanguage::where('id',$valueSubDetailsCart->ingredient_language_id)->with('ingredient_only')->first();
                            $ingredientsRecipe = IngredientsRecipe::where('ingredient_id',$languageIngredient->ingredient_id)->where('language_recipe_id',$valueDetailsCart->language_recipes_id)->first();
                            if($valueSubDetailsCart->status == 0){
                                if($statusFractional==0){
                                    $arrayProductIngredient[$valueSubDetailsCart->ingredient_language_id] += (($selectPeron*$ingredientsRecipe->quantity)/$minPerson);
                                }else{
                                    $arrayProductIngredient[$valueSubDetailsCart->ingredient_language_id] += ($selectPeron*$ingredientsRecipe->quantity);
                                }
                            }
                        }
                        if(!is_null($valueSubDetailsCart->utensil_language_id)){
                            $utensilLanguage = UtensilLanguage::where('id',$valueSubDetailsCart->utensil_language_id)->first();
                            $languageUtensilRecipe = LanguageRecipeUtensil::where('utensil_id',$utensilLanguage->utensil_id)->where('language_recipe_id',$valueDetailsCart->language_recipes_id)->first();
                            /*
                            ACA SI ES NULL LA RECETA ES DONDE VAMOS A TOMAR QUE ES UTENSIL INDEPENDIENTE
                            dd($languageUtensilRecipe);
                            dd($utensilLanguage); */
                            /* utensil_only */
                            if($valueSubDetailsCart->status == 0){
                                $arrayProductUtensil[$valueSubDetailsCart->utensil_language_id] += $languageUtensilRecipe->quantity;
                            }
                        }

                    }




                }

                $totalGeneral=0;
                if(count($arrayProductIngredient)>0){
                    foreach($arrayProductIngredient as $keyId => $val){
                        $languageIngredient = IngredientLanguage::where('id',$keyId)->with('ingredient_only')->first();
                        $valAbsolute =  intval(ceil($val / $languageIngredient->ingredient_only->unit_min));
                        if($val>0){
                            $totalGeneral += (($languageIngredient->cost_price + (($languageIngredient->cost_price * $languageIngredient->increase)/100)) * $valAbsolute);
                        }

                    }
                }
              /*   echo('<br> --> '.$totalGeneral); */
                if(count($arrayProductUtensil)>0){
                    foreach($arrayProductUtensil as $keyUtensilId => $valUtensil){
                        $languageUtensil = UtensilLanguage::where('id',$keyUtensilId)->with('utensil_only')->first();
                        $valAbsolute =  intval(ceil($valUtensil / $languageUtensil->utensil_only->unit_min));
/*                         echo('<br> valUtensil: '.$valUtensil.'--- unit min: '.$languageUtensil->utensil_only->unit_min.'  val absolute: '.$valAbsolute); */
                        if($valUtensil>0){
                            $totalGeneral += (($languageUtensil->cost_price + (($languageUtensil->cost_price * $languageUtensil->increase)/100)) * $valAbsolute);
                        }

                    }
                }
             /*    echo('<br> --> '.$totalGeneral);
                dd($arrayProductUtensil); */

                $cartUser->total_price = $totalGeneral;
                $result = $cartUser->save();
                return $result;

        }
        protected function controlDeleteRecipeAll($detailsCart){
            foreach ($detailsCart as $detCart) {
                $statusDelete = 1;
                $subDetailsCart = SubdetailsDetailsCarts::where('details_cart_id',$detCart->id)->get();
                foreach ($subDetailsCart as $subDetails) {
                    if(!$subDetails->status){
                        $statusDelete = 0;
                    }
                }
                if($statusDelete){
                    $detCart->delete();
                }
            }

        }
        protected function controlDeleteRecipeOnly($detailsCart){
                $statusDelete = 1;
                $subDetailsCart = SubdetailsDetailsCarts::where('details_cart_id',$detailsCart->id)->get();
                foreach ($subDetailsCart as $subDetails) {
                    if(!$subDetails->status){
                        $statusDelete = 0;
                    }
                }
                if($statusDelete){
                    $detailsCart->delete();
                }
        }







}
