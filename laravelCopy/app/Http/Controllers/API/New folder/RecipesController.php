<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\nucooks\entities\LanguageRecipe;
use App\nucooks\entities\FavoritesRecipesUser;
use App\nucooks\entities\Tag;
use App\nucooks\entities\Language;
use App\nucooks\entities\Subcategory;
use App\nucooks\entities\LanguageSubcategory;
use App\nucooks\entities\Category;
use App\nucooks\entities\CategoryLanguage;
use App\nucooks\entities\Recipe;
use App\nucooks\entities\RecipeUser;
use App\nucooks\entities\Cart;
use App\nucooks\entities\CategorySubcategory;
use App\nucooks\entities\DetailsCart;
use App\nucooks\entities\IngredientsRecipe;
use App\nucooks\entities\IngredientLanguage;
use App\nucooks\entities\Utensil;
use App\nucooks\entities\UtensilLanguage;
use Notification;

class RecipesController extends Controller
{
    public function __construct()
    {
        /* SACADOS PORQUE SE REFACTORIZO PROBAR TODO ANTES DE BORRAR.

        ,'showRecipeLogout', 'showPopularsLogout' */
        /*
            SACAMOS ESTO PARA PODER NO CALCULAR EL TOTAL SI TIENE CARRITO
        , 'calculateTotal'

        */
        $this->middleware('auth', ['except' => ['showNewsLogout', 'showOtherNewsLogout', 'showPopulars', 'showRecipe', 'showCategorySubcategoryLogout', 'showRecipeMenuLogout', 'recipeOnlyLogout' ,'utensilOnlyLogout', 'changeRecipeOnlyLogout', 'changeUtensilOnlyLogout','showRecipesSubcategories', 'recipeSubcategory']]);
    }

    public function showOtherNewsLogout($lang)
    {
        $recipeLanguage = [];
        if (!is_null(Auth::user())) {

            $user = Auth::user();
            $idLang = $user->language_id;
            $recipe = Recipe::where('view_menu', 1)->get()->random(9);
            foreach ($recipe as $valRecipe) {
                $recipeStepLanguage =  LanguageRecipe::where('recipe_id', $valRecipe->id)->with('favorit')->with(['recipe_only' => function ($queryRecipe) {
                    $queryRecipe->with('images')->first();
                }])->where('language_id', $idLang)->first();
                if (!is_null($recipeStepLanguage)) {
                    $recipeLanguage[] = [
                        'id'                    =>  $recipeStepLanguage->id,
                        'short_description'     =>  $recipeStepLanguage->short_description,
                        'description'           =>  $recipeStepLanguage->description,
                        'recipe'                =>  $recipeStepLanguage->recipe_only,
                        'favorit'               =>  !is_null($recipeStepLanguage->favorit) ? true : false
                    ];
                }
            }
            $newArray = array();
            $relationUserRecipe = RecipeUser::where('user_id', $user->id)->get();
            foreach ($recipeLanguage as $keyOne => $valRecipeLang) {
                foreach ($valRecipeLang as $keyTwo => $valRecipe) {
                    foreach ($relationUserRecipe as $keyDos => $valueRelation) {
                        if ($keyTwo === 'recipe') {
                            if ($valRecipe->id === $valueRelation->recipe_id)
                                $newArray[$valRecipe->id] = true;
                        }
                    }
                }
            }
            if (count($newArray) <= 0) {
                foreach ($recipeLanguage as $keyOne => $valRecipeLang) {
                    foreach ($valRecipeLang as $keyTwo => $valRecipe) {
                        foreach ($relationUserRecipe as $keyDos => $valueRelation) {
                            if ($keyTwo === 'recipe') {
                                if ($valRecipe->id === $valueRelation->recipe_id)
                                    $newArray[$valRecipe->id] = false;
                            }
                        }
                    }
                }
            }
            /*    CHECK HEAR */
            /*  */
            if (count($recipeLanguage) > 0) {
                return response()->json([
                    'status' => '200',
                    'news' => $recipeLanguage,
                    'efectInstagram' => $newArray,
                ]);
            } else {
                return response()->json([
                    'status' => '400',
                ]);
            }
        } else {
            if (($lang != "es") && ($lang != "en") && ($lang != "pt-br")) {
                $lang = "pt-br";
            }
            $langId = Language::where('lang', $lang)->first();
            $recipe = Recipe::where('view_menu', 1)->get()->random(9);
            foreach ($recipe as $valRecipe) {
                $recipeStepLanguage =  LanguageRecipe::where('recipe_id', $valRecipe->id)->with(['recipe_only' => function ($queryRecipe) {
                    $queryRecipe->with('images')->first();
                }])->where('language_id', $langId->id)->first();
                if (!is_null($recipeStepLanguage)) {
                    $recipeLanguage[] = [
                        'id'                    =>  $recipeStepLanguage->id,
                        'short_description'     =>  $recipeStepLanguage->short_description,
                        'description'           =>  $recipeStepLanguage->description,
                        'recipe'                =>  $recipeStepLanguage->recipe_only,
                    ];
                }
            }
            if (count($recipeLanguage) > 0) {
                return response()->json([
                    'status' => '200',
                    'news' =>  $recipeLanguage
                ]);
            } else {
                return response()->json([
                    'status' => '400',
                ]);
            }
        }
    }

    public function showNewsLogout($lang)
    {
        /*         dd($lang); */
        $recipeLanguage = [];
        if (!is_null(Auth::user())) {

            $user = Auth::user();
            $idLang = $user->language_id;
            $recipe = Recipe::where('view_menu', 1)->get()->random(4);
            foreach ($recipe as $valRecipe) {
                $recipeStepLanguage =  LanguageRecipe::where('recipe_id', $valRecipe->id)->with('favorit')->with(['recipe_only' => function ($queryRecipe) {
                    $queryRecipe->with('images')->first();
                }])->where('language_id', $idLang)->first();
                if (!is_null($recipeStepLanguage)) {
                    $recipeLanguage[] = [
                        'id'                    =>  $recipeStepLanguage->id,
                        'short_description'     =>  $recipeStepLanguage->short_description,
                        'description'           =>  $recipeStepLanguage->description,
                        'recipe'                =>  $recipeStepLanguage->recipe_only,
                        'favorit'               =>  !is_null($recipeStepLanguage->favorit) ? true : false
                    ];
                }
            }
            $newArray = array();
            $relationUserRecipe = RecipeUser::where('user_id', $user->id)->get();
            foreach ($recipeLanguage as $keyOne => $valRecipeLang) {
                foreach ($valRecipeLang as $keyTwo => $valRecipe) {
                    foreach ($relationUserRecipe as $keyDos => $valueRelation) {
                        if ($keyTwo === 'recipe') {
                            if ($valRecipe->id === $valueRelation->recipe_id)
                                $newArray[$valRecipe->id] = true;
                        }
                    }
                }
            }
            if (count($newArray) <= 0) {
                foreach ($recipeLanguage as $keyOne => $valRecipeLang) {
                    foreach ($valRecipeLang as $keyTwo => $valRecipe) {
                        foreach ($relationUserRecipe as $keyDos => $valueRelation) {
                            if ($keyTwo === 'recipe') {
                                if ($valRecipe->id === $valueRelation->recipe_id)
                                    $newArray[$valRecipe->id] = false;
                            }
                        }
                    }
                }
            }
            /*    CHECK HEAR */
            /*  */
            if (count($recipeLanguage) > 0) {
                return response()->json([
                    'status' => '200',
                    'news' => $recipeLanguage,
                    'efectInstagram' => $newArray,
                ]);
            } else {
                return response()->json([
                    'status' => '400',
                ]);
            }
        } else {
            if (($lang != "es") && ($lang != "en") && ($lang != "pt-br")) {
                $lang = "pt-br";
            }
            $langId = Language::where('lang', $lang)->first();
            $recipe = Recipe::where('view_menu', 1)->get()->random(4);
            foreach ($recipe as $valRecipe) {
                $recipeStepLanguage =  LanguageRecipe::where('recipe_id', $valRecipe->id)->with(['recipe_only' => function ($queryRecipe) {
                    $queryRecipe->with('images')->first();
                }])->where('language_id', $langId->id)->first();
                if (!is_null($recipeStepLanguage)) {
                    $recipeLanguage[] = [
                        'id'                    =>  $recipeStepLanguage->id,
                        'short_description'     =>  $recipeStepLanguage->short_description,
                        'description'           =>  $recipeStepLanguage->description,
                        'recipe'                =>  $recipeStepLanguage->recipe_only,
                    ];
                }
            }
            if (count($recipeLanguage) > 0) {
                return response()->json([
                    'status' => '200',
                    'news' =>  $recipeLanguage
                ]);
            } else {
                return response()->json([
                    'status' => '400',
                ]);
            }
        }
    }

    /* --------------------------------------------------- */

    public function showPopulars()
    {
        if (Auth::user()) {
            $langId = Auth::user()->language_id;
        } else {
            $lang = session()->get('locale');
            if (is_null($lang)) {
                $lang = "pt-br";
            }
            $lang = Language::where('lang', $lang)->first();
            $langId = $lang->id;
        }
        $populars = Tag::where('language_id', $langId)->take(5)->get();
        return response()->json([
            'status' => '200',
            'populars' => $populars
        ]);
    }
    /*     public function showPopularsLogout($lang){
        $langId = Language::where('lang',$lang)->first();
        $populars = Tag::where('language_id',$langId->id)->get();
        return response()->json([
            'status' => '200',
            'populars' => $populars]);
    } */

    /* --------------------------------------------------- */

    /* ---------------------------------------------- */
    public function showRecipe($language)
    {
        $recipeLanguage = [];
        if (($language != 'es') && ($language != 'en') && ($language != 'pt-br')) {
            return response()->json([
                'status' => '400',
                'statusFavoritesRecipe' => [],
                'recipies' => []
            ]);
        }
        $statusFavorites = 0;
        if (!is_null(Auth::user())) {
            $langId = Auth::user()->language_id;
            $idUser = Auth::user()->id;
            /*
                CONSULTA ORIGINAL
$recipeStepLanguage =  LanguageRecipe::with(['favorit' => function ($queryFavorit) use ($idUser) {
                $queryFavorit->where('user_id', $idUser)->get();
            }])->with('subcategory')->with(['recipe_only' => function ($query) {
                $query->with('images')->get();
            }])->with('recipe_steps')->with('ingredients')->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('language_id', $langId)->get(); */
            $recipeStepLanguage =  LanguageRecipe::recipeScope()->where('language_id', $langId)->get();
        } else {
            $lang = Language::where('lang', $language)->first();
            $lang = $lang->id;
            $recipeStepLanguage =  LanguageRecipe::recipeScope()->where('language_id', $lang)->get();
        }
        if (!is_null($recipeStepLanguage)) {

            foreach ($recipeStepLanguage as $valRecipe) {
                if (!is_null($valRecipe->recipe_only)) {
                    $recipeLanguage[] = [
                        'id'                    =>  $valRecipe->id,
                        'short_description'     =>  $valRecipe->short_description,
                        'description'           =>  $valRecipe->description,
                        'recipe'                =>  $valRecipe->recipe_only,
                    ];
                }
            }
        }


        if (!is_null($recipeStepLanguage)) {
            return response()->json([
                'status' => '200',
                'recipiesArray' =>  $recipeLanguage
            ]);
        } else {
            return response()->json([
                'status' => '400',
            ]);
        }
    }

    public function showRecipeFavorite(Request $request)
    {

        if (!is_null(Auth::user())) {
            $langId = Auth::user()->language_id;
            $idUser = Auth::user()->id;
            /*

            ORIGINAL
            $recipeStepLanguage = FavoritesRecipesUser::where('user_id', $idUser)->with(['langrecipe' => function ($queryRecipies) use ($langId) {
                $queryRecipies->with('subcategory')->with(['recipe_only' => function ($query) {
                    $query->with('images')->get();
                }])->with('ingredients')->with('language')->with('ingredient_language_recipes')->where('language_id', $langId)->get();
            }])->orderBy('id', 'DESC')->paginate(4);

            return [
                'status' => '200',
                'pagination' => [
                    'total'         =>  $recipeStepLanguage->total(),
                    'current_page'  =>  $recipeStepLanguage->currentPage(),
                    'per_page'      =>  $recipeStepLanguage->perPage(),
                    'last_page'     =>  $recipeStepLanguage->lastPage(),
                    'from'          =>  $recipeStepLanguage->firstItem(),
                    'to'            =>  $recipeStepLanguage->lastItem(),
                ],
                'recipies' => $recipeStepLanguage,
            ]; */

            $recipesFavorites = FavoritesRecipesUser::where('user_id', $idUser)->with(['langrecipe' => function ($queryRecipies) use ($langId) {
                $queryRecipies->with('subcategory')->with(['recipe_only' => function ($query) {
                    $query->with('images')->get();
                }])->with('ingredients')->with('language')->with('ingredient_language_recipes')->where('language_id', $langId)->get();
            }])->orderBy('id', 'DESC')->get();

            /*   dd($recipesFavorites); */
            $recipeFavoriteArray = [];

            if (!is_null($recipesFavorites)) {
                foreach ($recipesFavorites as $recipeFavorite) {
                    $imgPath = null;
                    if (!is_null($recipeFavorite->langrecipe->recipe_only->images)) $imgPath = $recipeFavorite->langrecipe->recipe_only->images[0]->path;

                    $categoryDescription = '';
                    $subCategoryDescription = '';
                    if (!is_null($recipeFavorite->langrecipe->subcategory)) {
                        $categorySubcategory = CategorySubcategory::with('category', 'subcategory')->where('subcategory_id', $recipeFavorite->langrecipe->subcategory->subcategory_id)->first();
                        if (!is_null($categorySubcategory)) {
                            $categoryDescription = $categorySubcategory->category->category;
                            $subCategoryDescription = $categorySubcategory->subcategory->subcategory;
                        }
                    }

                    array_push($recipeFavoriteArray, [
                        'idLanguageRecipe'      =>  $recipeFavorite->langrecipe->id,
                        'categorySubcategory'   =>  $categoryDescription . ' / ' . $subCategoryDescription,
                        'short_description'     =>  $recipeFavorite->langrecipe->short_description,
                        'description'           =>  $recipeFavorite->langrecipe->description,
                        'imagePath'             =>  $imgPath,
                        'favorite'              =>  true,
                    ]);
                }
            }


            return [
                'status' => 200,
                'recipies' => $recipeFavoriteArray,
            ];
        }

        return response()->json([
            'status' => '400',
            'recipies' => []
        ]);
    }

    public function showRecipeFavoritePaginate(Request $request)
    {
        if (!is_null(Auth::user())) {
            $langId = Auth::user()->language_id;
            $idUser = Auth::user()->id;
            $recipeStepLanguage =  LanguageRecipe::with(['favorit' => function ($queryFavorit) use ($idUser) {
                $queryFavorit->where('user_id', $idUser)->get();
            }])->with('subcategory')->with(['recipe_only' => function ($query) {
                $query->with('images')->get();
            }])->with('ingredients')->with('language')->with('ingredient_language_recipes')->where('language_id', $langId)->paginate(2);

            return [
                'pagination' => [
                    'total'         =>  $recipeStepLanguage->total(),
                    'current_page'  =>  $recipeStepLanguage->currentPage(),
                    'per_page'      =>  $recipeStepLanguage->perPage(),
                    'last_page'     =>  $recipeStepLanguage->lastPage(),
                    'from'          =>  $recipeStepLanguage->firstItem(),
                    'to'            =>  $recipeStepLanguage->lastItem(),
                ],
                'tasks' => $recipeStepLanguage,
            ];
        }
    }


















    /*     public function showRecipeLogout($lang){
        $langId = Language::where('lang',$lang)->first();

        $recipeStepLanguage =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function($query){$query->with('images')->get();}])->with('recipe_steps')->with('ingredients')->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('language_id',$langId->id)->get();
        return response()->json([
            'status' => '200',
            'recipies' => $recipeStepLanguage]);
    } */

    /* ---------------------------------------------- */



    public function showCategorySubcategory($idLang)
    {

        /*         $categorySubcategories = CategoryLanguage::where('language_id',$idLang)->with(['category' => function($query) use ($idLang){
            $query->with(['subcategories' => function($q2) use ($idLang){
                $q2->with(['language_subcategory' => function ($q3) use ($idLang){
                    $q3->where('language_id',$idLang)->get();
                }])->get();
            }])->get();
        }])->get();



        return response()->json([
            'status' => '200',
            'categorySubcategories' => $categorySubcategories]); */
        $categorySubcategories = Category::where('view_menu', TRUE)
            ->with(['catlang' => function ($q) use ($idLang) {
                $q->where('language_id', $idLang)->get();
            }])
            ->with(['subcategories' => function ($q2) use ($idLang) {
                $q2->where('view_menu', TRUE)->with(['language_subcategory' => function ($q3) use ($idLang) {
                    $q3->where('language_id', $idLang)->get();
                }])->get();
            }])
            ->orderBy('position', 'ASC')->get();


        return response()->json([
            'status' => '200',
            'categorySubcategories' => $categorySubcategories
        ]);
    }



    public function showCategorySubcategoryLogout($lang)
    {
        $langId = Language::where('lang', $lang)->first();
        $langId = $langId->id;

        /*         $categorySubcategories = Category::where('view_menu',TRUE)->with(['catlang' => function($queryCatLang) use ($langId){
            $queryCatLang->where('language_id',$langId)->get();
        }])->with(['subcategories' => function($querySubcat) use ($langId){
            $querySubcat->where('view_menu',TRUE)->with(['language_subcategory' => function($queryLangSubcat) use ($langId){
                $queryLangSubcat->where('language_id',$langId)->get();
            }])->get();
        }])->get();

/*         $categorySubcategories = Category::where('view_menu',TRUE)
             ->with(['catlang' => function($q) use ($langId){
                    $q->where('language_id', 1)->get();
                }])
             ->with(['subcategories' => function($q2) use ($langId){
                $q2->with(['language_subcategory'=> function($q3) use ($langId){
                    $q3->where('language_id', $langId)->get();
                }])->get();
             }])
             ->get(); */

        $categorySubcategories = Category::where('view_menu', TRUE)
            ->with(['catlang' => function ($q) use ($langId) {
                $q->where('language_id', $langId)->get();
            }])
            ->with(['subcategories' => function ($q2) use ($langId) {
                $q2->where('view_menu', TRUE)->with(['language_subcategory' => function ($q3) use ($langId) {
                    $q3->where('language_id', $langId)->get();
                }])->get();
            }])
            ->orderBy('position', 'ASC')->get();


        return response()->json([
            'status' => '200',
            'categorySubcategories' => $categorySubcategories
        ]);
    }




    public function showRecipeMenu($idLang)
    {
        /*         $recipeImage =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function($query){$query->with('images')->get();}])->with('recipe_steps')->with('ingredients')->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('language_id',$idLang)->get()->random(2); */
        $recipe = Recipe::where('view_menu', TRUE)->count();

        switch ($recipe) {
            case '2':
                /*                 $recipeStepLanguage =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function($query){$query->with('images')->where('min_time','=',30)->get();}])->with('recipe_steps')->with('ingredients')->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('language_id',$langId->id)->take(2)->get();  */


                /* select('id','view_menu','title')-> */

                $recipeStepLanguage = Recipe::where('view_menu', TRUE)->with('images')->with(['language_recipes' => function ($q) use ($idLang) {
                    $q->where('language_id', $idLang)->get();
                }])->get();
                return response()->json([
                    'status' => '200',
                    'recipeMenu' => $recipeStepLanguage
                ]);
                break;
                /*             case '0': */

            case '1':
                $recipe1 = Recipe::where('view_menu', TRUE)->with('images')->with(['language_recipes' => function ($q) use ($idLang) {
                    $q->where('language_id', $idLang)->get();
                }])->get();



                $recipe2 = Recipe::where('id', '<>', $recipe1[0]->id)->with('images')->with(['language_recipes' => function ($q) use ($idLang) {
                    $q->where('language_id', $idLang)->get();
                }])->inRandomOrder()->take(1)->get();

                $recipe3 = $recipe1->merge($recipe2);


                return response()->json([
                    'status' => '200',
                    'recipeMenu' => $recipe3
                ]);
                break;
            default:
                $recipeStepLanguage = Recipe::with('images')->with(['language_recipes' => function ($q) use ($idLang) {
                    $q->where('language_id', $idLang)->get();
                }])->inRandomOrder()->take(2)->get();

                return response()->json([
                    'status' => '200',
                    'recipeMenu' => $recipeStepLanguage
                ]);
                break;
        }
    }

    public function showRecipeMenuLogout($lang)
    {
        $langId = Language::where('lang', $lang)->first();

        /*         $recipeStepLanguage =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function($query){$query->with('images')->get();}])->with('recipe_steps')->with('ingredients')->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('language_id',$langId->id)->get()->random(2);
        return response()->json([
            'status' => '200',
            'recipeMenu' => $recipeStepLanguage]);
 */

        $recipe = Recipe::where('view_menu', TRUE)->count();

        switch ($recipe) {
            case '2':
                /*                 $recipeStepLanguage =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function($query){$query->with('images')->where('min_time','=',30)->get();}])->with('recipe_steps')->with('ingredients')->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('language_id',$langId->id)->take(2)->get();  */
                $recipeStepLanguage = Recipe::where('view_menu', TRUE)->with('images')->with(['language_recipes' => function ($q) use ($langId) {
                    $q->where('language_id', $langId->id)->get();
                }])->get();
                return response()->json([
                    'status' => '200',
                    'recipeMenu' => $recipeStepLanguage
                ]);
                break;
                /*             case '0': */

            case '1':
                $recipe1 = Recipe::where('view_menu', TRUE)->with('images')->with(['language_recipes' => function ($q) use ($langId) {
                    $q->where('language_id', $langId->id)->get();
                }])->get();



                $recipe2 = Recipe::where('id', '<>', $recipe1[0]->id)->with('images')->with(['language_recipes' => function ($q) use ($langId) {
                    $q->where('language_id', $langId->id)->get();
                }])->inRandomOrder()->take(1)->get();

                $recipe3 = $recipe1->merge($recipe2);


                return response()->json([
                    'status' => '200',
                    'recipeMenu' => $recipe3
                ]);
                break;
            default:
                $recipeStepLanguage = Recipe::with('images')->with(['language_recipes' => function ($q) use ($langId) {
                    $q->where('language_id', $langId->id)->get();
                }])->inRandomOrder()->take(2)->get();

                return response()->json([
                    'status' => '200',
                    'recipeMenu' => $recipeStepLanguage
                ]);
                break;
        }
    }



    public function recipeOnlyLogout(LanguageRecipe $idRecipe)
    {


        if (Auth::user()) {


            $recipeFather = Recipe::where('id', $idRecipe->recipe_id)->first();
            $user = Auth::user();

            $relationUserRecipe = RecipeUser::where('user_id', $user->id)->where('recipe_id', $recipeFather->id)->first();

            if (is_null($relationUserRecipe)) {
                $user->recipesUser()->attach($recipeFather->id);
            }



            /*             $langId = $idRecipe->language_id;
            $recipeLanguage =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function($query){$query->with('images')->get();}])->with('recipe_steps')->with(['ingredients' => function($queryIngredient) use ($langId){
                $queryIngredient->with(['ingredient_language' => function($queryLangIngredient) use ($langId){
                    $queryLangIngredient->where('language_id',$langId)->get();
                }])->get();
            }])->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('id',$idRecipe->id)->get();


            $recipeReltaionSubcategory =  LanguageRecipe::with('subcategory')->where('id',$idRecipe->id)->first();

            $recipeRelation =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function($query){$query->with('images')->get();}])->with('recipe_steps')->with(['ingredients' => function($queryIngredient) use ($langId){
                $queryIngredient->with(['ingredient_language' => function($queryLangIngredient) use ($langId){
                    $queryLangIngredient->where('language_id',$langId)->get();
                }])->get();
            }])->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('language_subcategory_id',$recipeReltaionSubcategory->language_subcategory_id)->where('id','<>',$idRecipe->id)->inRandomOrder()->take(3)->get();


            return view('users.recipes.index',compact('recipeLanguage','recipeRelation')); */
            /*             return view('users.recipes.index'); */
        }
        /* else{
            $langId = $idRecipe->language_id;
            $recipeLanguage =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function($query){$query->with('images')->get();}])->with('recipe_steps')->with(['ingredients' => function($queryIngredient) use ($langId){
                $queryIngredient->with(['ingredient_language' => function($queryLangIngredient) use ($langId){
                    $queryLangIngredient->where('language_id',$langId)->get();
                }])->get();
            }])->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('id',$idRecipe->id)->get();


            $recipeReltaionSubcategory =  LanguageRecipe::with('subcategory')->where('id',$idRecipe->id)->first();

            $recipeRelation =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function($query){$query->with('images')->get();}])->with('recipe_steps')->with(['ingredients' => function($queryIngredient) use ($langId){
                $queryIngredient->with(['ingredient_language' => function($queryLangIngredient) use ($langId){
                    $queryLangIngredient->where('language_id',$langId)->get();
                }])->get();
            }])->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('language_subcategory_id',$recipeReltaionSubcategory->language_subcategory_id)->where('id','<>',$idRecipe->id)->inRandomOrder()->take(3)->get();


            return view('users.recipes.index',compact('recipeLanguage','recipeRelation'));
        } */


        return view('users.recipes.index');
    }

    public function utensilOnlyLogout(UtensilLanguage $idUtensil){


        /* if (Auth::user()) {


            $recipeFather = Recipe::where('id', $idRecipe->recipe_id)->first();
            $user = Auth::user();

            $relationUserRecipe = RecipeUser::where('user_id', $user->id)->where('recipe_id', $recipeFather->id)->first();

            if (is_null($relationUserRecipe)) {
                $user->recipesUser()->attach($recipeFather->id);
            }
        } */


        return view('users.utensils.index');
    }

    /*

        ESTO FUNCIONA PERO SE SACA PORQUE NO SACAREMOS MAS EL TOTAL GENERAL EN CADA RECETA



   public function calculateTotal($localStorage)
    {

        $total = 0;
        if (Auth::user()) {
            $idUser = Auth::user()->id;
            $total = $this->totalCart($idUser);
        } else {
            $recipesCart = array();
            $cart = json_decode($localStorage);
            $index = 0;
            if ($cart) {
                $selectPerson = 0;
                $minPerson = 0;
                $arrayProduct = [];
                foreach ($cart as $key => $valCart) {
                    $selectPerson = null;
                    $languageRecipeId = null;
                    $valCartTwo = null;
                    $ingredientLanguage = null;
                    foreach ($valCart as $keyTwo => $valCartTwo) {

                        if ($keyTwo === 'selectPerson') {
                            $selectPerson = $valCartTwo;
                        }
                        if ($keyTwo === 'languageRecipeId') $languageRecipeId = $valCartTwo;
                        if ($keyTwo === 'idR') $recipeId = $valCartTwo;
                        if ($keyTwo === "ingredientLanguage") $ingredientLanguage = $valCartTwo;


                        if ((!is_null($selectPerson)) && (!is_null($languageRecipeId)) && (!is_null($valCartTwo)) && (!is_null($ingredientLanguage))) {
                            foreach ($ingredientLanguage as $keyThree => $valCartThree) {
                                $statusArray = null;
                                $languageRecipeId = null;
                                $idIngrediente = null;
                                $ingredientLanguageId = null;
                                foreach ($valCartThree as $keyFour => $valCartFour) {
                                    if ($keyFour === 'language_recipe_id') $languageRecipeId = $valCartFour;
                                    if ($keyFour === 'ingredient_id') $idIngrediente = $valCartFour;
                                    if ($keyFour === 'ingredientLanguageId') $ingredientLanguageId = $valCartFour;
                                    if ($keyFour === 'status') $statusArray = $valCartFour;
                                    if ((!$statusArray) && (!is_null($languageRecipeId)) && (!is_null($idIngrediente)) && (!is_null($ingredientLanguageId))) {
                                        $arrayProduct[$ingredientLanguageId] = 0;
                                    }
                                }
                            }
                            $selectPerson = null;
                            $languageRecipeId = null;
                            $valCartTwo = null;
                            $ingredientLanguage = null;
                        }
                    }
                }


                $selectPerson = null;
                $languageRecipeId = null;
                $valCartTwo = null;
                $ingredientLanguage = null;

                foreach ($cart as $key => $valCart) {


                    foreach ($valCart as $keyTwo => $valCartTwo) {
                        if ($keyTwo === 'selectPerson') $selectPerson = $valCartTwo;
                        if ($keyTwo === 'languageRecipeId') $languageRecipeId = $valCartTwo;
                        if ($keyTwo === 'idR') $recipeId = $valCartTwo;
                        if ($keyTwo === "ingredientLanguage") $ingredientLanguage = $valCartTwo;


                        if ((!is_null($selectPerson)) && (!is_null($languageRecipeId)) && (!is_null($valCartTwo)) && (!is_null($ingredientLanguage))) {

                            $statusFractional = 0;

                            foreach ($valCartTwo as $keyThree => $valCartThree) {

                                $statusArray = null;
                                $languageRecipeId = null;
                                $idIngrediente = null;
                                $ingredientLanguageId = null;
                                foreach ($valCartThree as $keyFour => $valCartFour) {

                                    if ($keyFour === 'language_recipe_id') $languageRecipeId = $valCartFour;
                                    if ($keyFour === 'ingredient_id') $idIngrediente = $valCartFour;
                                    if ($keyFour === 'ingredientLanguageId') $ingredientLanguageId = $valCartFour;
                                    if ($keyFour === 'status') $statusArray = $valCartFour;

                                    if ((!$statusArray) && (!is_null($languageRecipeId)) && (!is_null($idIngrediente)) && (!is_null($ingredientLanguageId))) {
                                        $languageIngredient = IngredientLanguage::where('id', $idIngrediente)->with('ingredient_only')->first();
                                        if (!is_null($languageIngredient)) {
                                            if ($languageIngredient->ingredient_only->fractional == 0) {
                                                $statusFractional = 1;
                                            }
                                        }
                                        $statusArray = null;
                                        $languageRecipeId = null;
                                        $idIngrediente = null;
                                        $ingredientLanguageId = null;
                                    }
                                }
                                $statusArray = null;
                                $languageRecipeId = null;
                                $idIngrediente = null;
                                $ingredientLanguageId = null;
                            }
                        }
                    }


                    foreach ($valCart as $keyTwo => $valCartTwo) {
                        if ($keyTwo === 'selectPerson') $selectPerson = $valCartTwo;
                        if ($keyTwo === 'languageRecipeId') $languageRecipeId = $valCartTwo;
                        if ($keyTwo === 'idR') $recipeId = $valCartTwo;
                        if ($keyTwo === "ingredientLanguage") $ingredientLanguage = $valCartTwo;

                        if ((!is_null($selectPerson)) && (!is_null($languageRecipeId)) && (!is_null($valCartTwo)) && (!is_null($ingredientLanguage))) {

                            foreach ($ingredientLanguage as $keyThree => $valCartThree) {

                                foreach ($valCartThree as $keyFour => $valCartFour) {

                                    if ($keyFour === 'language_recipe_id') $languageRecipeId = $valCartFour;
                                    if ($keyFour === 'ingredient_id') $idIngrediente = $valCartFour;
                                    if ($keyFour === 'ingredientLanguageId') $ingredientLanguageId = $valCartFour;
                                    if ($keyFour === 'status') $statusArray = $valCartFour;



                                    if ((!$statusArray) && (!is_null($languageRecipeId)) && (!is_null($idIngrediente)) && (!is_null($ingredientLanguageId))) {
                                        $ingredientsRecipe = IngredientsRecipe::where('ingredient_id',  $idIngrediente)->where('language_recipe_id', $languageRecipeId)->first();
                                        $LanguageRecipe = LanguageRecipe::where('recipe_id', $recipeId)->with('recipe_only')->first();

                                        $minPerson = $LanguageRecipe->recipe_only->min_person;
                                        if ($statusFractional == 0) {
                                            $arrayProduct[$ingredientLanguageId] += (($selectPerson * $ingredientsRecipe->quantity) / $minPerson);
                                        } else {
                                            $arrayProduct[$ingredientLanguageId] += ($selectPerson * $ingredientsRecipe->quantity);
                                        }
                                        $statusArray = null;
                                        $languageRecipeId = null;
                                        $idIngrediente = null;
                                        $ingredientLanguageId = null;
                                    }
                                }
                            }
                            $selectPerson = null;
                            $languageRecipeId = null;
                            $valCartTwo = null;
                            $ingredientLanguage = null;
                        }
                    }
                }
                $total = 0;
                foreach ($arrayProduct as $keyId => $val) {
                    $languageIngredient = IngredientLanguage::where('id', $keyId)->with('ingredient_only')->first();

                    $valAbsolute =  intval(ceil($val / $languageIngredient->ingredient_only->unit_min));
                    if ($val > 0) {
                        $total += (($languageIngredient->cost_price + (($languageIngredient->cost_price * $languageIngredient->increase) / 100)) * $valAbsolute);
                    }
                }
            }
        }
        return [
            'total' => $total,
        ];
    } */

    public function changeRecipeOnlyLogout(LanguageRecipe $idLangRecipe, $lang)
    {

        $idRecipe = Recipe::where('id', $idLangRecipe->recipe_id)->first();

        if (Auth::user()) {

            $lang = Auth::user()->language_id;
            $idUser = Auth::user()->id;

            $recipeLanguage =  LanguageRecipe::with(['favorit' => function ($queryFavorit) use ($idUser) {
                $queryFavorit->where('user_id', $idUser)->get();
            }])->with('subcategory')->with(['recipe_only' => function ($query) {
                $query->with('images')->get();
            }])->with('recipe_steps')->with(['ingredients' => function ($queryIngredient) use ($lang) {
                $queryIngredient->with(['ingredient_language' => function ($queryLangIngredient) use ($lang) {
                    $queryLangIngredient->where('language_id', $lang)->get();
                }])->get();
            }])
                ->with(['utensils' => function ($queryUtensil) use ($lang) {
                    $queryUtensil->with(['utensil_language' => function ($queryLangUtensil) use ($lang) {
                        $queryLangUtensil->where('language_id', $lang)->get();
                    }])->get();
                }])
                ->with('tags')->with('language')->with('ingredient_language_recipes', 'utensil_language_recipes')->with('tip')->where('recipe_id', $idRecipe->id)->where('language_id', $lang)->get();
        } else {
            /*             $lang = session()->get('locale');
            if (is_null($lang)) {
                $lang = "pt-br";
            } */
            $lang = Language::where('lang', $lang)->first();
            $lang = $lang->id;

            $recipeLanguage =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function ($query) {
                $query->with('images')->get();
            }])->with('recipe_steps')->with(['ingredients' => function ($queryIngredient) use ($lang) {
                $queryIngredient->with(['ingredient_language' => function ($queryLangIngredient) use ($lang) {
                    $queryLangIngredient->where('language_id', $lang)->get();
                }])->get();
            }])
                ->with(['utensils' => function ($queryUtensil) use ($lang) {
                    $queryUtensil->with(['utensil_language' => function ($queryLangUtensil) use ($lang) {
                        $queryLangUtensil->where('language_id', $lang)->get();
                    }])->get();
                }])
                ->with('tags')->with('language')->with('ingredient_language_recipes', 'utensil_language_recipes')->with('tip')->where('recipe_id', $idRecipe->id)->where('language_id', $lang)->get();
        }



        /*     $recipeRelation =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function ($query) {
        $query->with('images')->get();
    }])->with('recipe_steps')->with(['ingredients' => function ($queryIngredient) use ($lang) {
        $queryIngredient->with(['ingredient_language' => function ($queryLangIngredient) use ($lang) {
            $queryLangIngredient->where('language_id', $lang)->get();
        }])->get();
    }])->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('language_subcategory_id', $recipeLanguage[0]->subcategory->id)->where('id', '<>', $idLangRecipe->id)->inRandomOrder()->take(3)->get(); */

        $recipeRelation =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function ($query) {
            $query->with('images')->get();
        }])->with('language')->where('language_id', $lang)->where('language_subcategory_id', $recipeLanguage[0]->subcategory->id)->where('id', '<>', $idLangRecipe->id)->get();


        $carouselArray = [];
        if (!is_null($recipeRelation)) {
            foreach ($recipeRelation as $relationRecipe) {
                $imagePath = null;
                if (!is_null($relationRecipe->recipe_only->images[0]->path)) {
                    $imagePath = $relationRecipe->recipe_only->images[0]->path;
                }
                $idRecipe = $relationRecipe->id;
                $shortDescription = $relationRecipe->short_description;
                $description = $relationRecipe->description;
                array_push($carouselArray, [
                    'idLanguageRecipe'      =>  $idRecipe,
                    'imagePath'             =>  $imagePath,
                    'shortDescription'      =>  $shortDescription,
                    'description'           =>  $description
                ]);
            }
        }
       /*  dd($recipeLanguage); */
        if (!is_null($recipeLanguage) && (!is_null($recipeRelation))) {
            return response()->json([
                'status' => '200',
                'recipe' => $recipeLanguage,
                'reciperelation' => $carouselArray,
            ]);
        } else {
            return response()->json([
                'status' => '400',
                'recipe' => null,
                'recipeRelation' => null,
            ]);
        }
    }

    public function changeUtensilOnlyLogout(UtensilLanguage $idLangUtensil, $lang){


        /* $idUtensil = Utensil::where('id', $idLangUtensil->utensil_id)->first();
        dd($idUtensil); */

        if (Auth::user()) {

            $lang = Auth::user()->language_id;
            $idUser = Auth::user()->id;

/*             $recipeLanguage =  LanguageRecipe::with(['favorit' => function ($queryFavorit) use ($idUser) {
                $queryFavorit->where('user_id', $idUser)->get();
            }])->with('subcategory')->with(['recipe_only' => function ($query) {
                $query->with('images')->get();
            }])->with('recipe_steps')->with(['ingredients' => function ($queryIngredient) use ($lang) {
                $queryIngredient->with(['ingredient_language' => function ($queryLangIngredient) use ($lang) {
                    $queryLangIngredient->where('language_id', $lang)->get();
                }])->get();
            }])
                ->with(['utensils' => function ($queryUtensil) use ($lang) {
                    $queryUtensil->with(['utensil_language' => function ($queryLangUtensil) use ($lang) {
                        $queryLangUtensil->where('language_id', $lang)->get();
                    }])->get();
                }])
                ->with('tags')->with('language')->with('ingredient_language_recipes', 'utensil_language_recipes')->with('tip')->where('recipe_id', $idRecipe->id)->where('language_id', $lang)->get(); */
        } else {

            $lang = Language::where('lang', $lang)->first();
            $lang = $lang->id;

/*             $recipeLanguage =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function ($query) {
                $query->with('images')->get();
            }])->with('recipe_steps')->with(['ingredients' => function ($queryIngredient) use ($lang) {
                $queryIngredient->with(['ingredient_language' => function ($queryLangIngredient) use ($lang) {
                    $queryLangIngredient->where('language_id', $lang)->get();
                }])->get();
            }])
                ->with(['utensils' => function ($queryUtensil) use ($lang) {
                    $queryUtensil->with(['utensil_language' => function ($queryLangUtensil) use ($lang) {
                        $queryLangUtensil->where('language_id', $lang)->get();
                    }])->get();
                }])
                ->with('tags')->with('language')->with('ingredient_language_recipes', 'utensil_language_recipes')->with('tip')->where('recipe_id', $idRecipe->id)->where('language_id', $lang)->get(); */
        }



        /* $recipeRelation =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function ($query) {
            $query->with('images')->get();
        }])->with('language')->where('language_id', $lang)->where('language_subcategory_id', $recipeLanguage[0]->subcategory->id)->where('id', '<>', $idLangRecipe->id)->get();

        $carouselArray = [];
        if (!is_null($recipeRelation)) {
            foreach ($recipeRelation as $relationRecipe) {
                $imagePath = null;
                if (!is_null($relationRecipe->recipe_only->images[0]->path)) {
                    $imagePath = $relationRecipe->recipe_only->images[0]->path;
                }
                $idRecipe = $relationRecipe->id;
                $shortDescription = $relationRecipe->short_description;
                $description = $relationRecipe->description;
                array_push($carouselArray, [
                    'idLanguageRecipe'      =>  $idRecipe,
                    'imagePath'             =>  $imagePath,
                    'shortDescription'      =>  $shortDescription,
                    'description'           =>  $description
                ]);
            }
        } */


        $utensilLanguage = UtensilLanguage::select('id','utensil_id','language_id','description','description_brand','feature','cost_price','increase')
            ->where('id',$idLangUtensil->id)->with(['utensil_only' => function($queryUtensil){
                $queryUtensil->select('id','unit_min','avatar_image','is_active')->get();
            }])->first();

/*         dd($utensilLanguage); */
        if (!is_null($utensilLanguage) && (!is_null($utensilLanguage))) {
            return response()->json([
                'status' => '200',
                'utensil' => $utensilLanguage,
            ]);
        } else {
            return response()->json([
                'status' => '400',
                'utensil' => null,
            ]);
        }
    }




    /*
    public function changeRecipeOnlyCart(LanguageRecipe $idLangRecipe){

        $idRecipe = Recipe::where('id',$idLangRecipe->recipe_id)->first();


        if(Auth::user()){
            $lang = Auth::user()->language_id;
            $idUser = Auth::user()->id;
            $recipeLanguage =  LanguageRecipe::with(['favorit' => function($queryFavorit) use($idUser) {
                $queryFavorit->where('user_id',$idUser)->get();
            }])->with('subcategory')->with(['recipe_only' => function($query){$query->with('images')->get();}])->with('recipe_steps')->with(['ingredients' => function($queryIngredient) use ($lang){
                $queryIngredient->with(['ingredient_language' => function($queryLangIngredient) use ($lang){
                    $queryLangIngredient->where('language_id',$lang)->get();
                }])->get();
            }])->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('recipe_id',$idRecipe->id)->where('language_id',$lang)->get();


        }else{
            $lang = session()->get('locale');
            if(is_null($lang)){
                $lang="pt-br";
            }
            $lang = Language::where('lang',$lang)->first();
            $lang = $lang->id;

            $recipeLanguage =  LanguageRecipe::with('subcategory')->with(['recipe_only' => function($query){$query->with('images')->get();}])->with('recipe_steps')->with(['ingredients' => function($queryIngredient) use ($lang){
                $queryIngredient->with(['ingredient_language' => function($queryLangIngredient) use ($lang){
                    $queryLangIngredient->where('language_id',$lang)->get();
                }])->get();
            }])->with('tags')->with('language')->with('ingredient_language_recipes')->with('tip')->where('recipe_id',$idRecipe->id)->where('language_id',$lang)->get();

        }






        if(!is_null($recipeLanguage)){
            return response()->json([
                'status' => '200',
                'recipe' => $recipeLanguage,
            ]);
        }else{
            return response()->json([
                'status' => '400',
                'recipe' => null,
            ]);
        }



    }


 */










    public function showRecipesSubcategories($idSubcategory)
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
        $idSubcategoryLanguage = LanguageSubcategory::with('sub_category')->where('subcategory_id', $idSubcategory)->where('language_id', $lang)->first();
        $recipe = null;
        $utensils = null;
        /* $auxUtensil = []; */
   /*      $idSubcategory =  */
        if ($idSubcategoryLanguage->sub_category->is_utensil) {
            $idSubcat = $idSubcategoryLanguage->id;
            $utensils = UtensilLanguage::where('language_id', $lang)->with('utensil_only')->with(['subcategory_utensil' => function ($querySubcategory) use ($idSubcat) {
                $querySubcategory->where('language_subcategory_id', $idSubcat)->get();
            }])->get();


            $utensils = $utensils->filter(function ($utensil, $key) {
                return !is_null($utensil->subcategory_utensil) && count($utensil->subcategory_utensil) > 0;

            });
/*             if(!is_null($utensils)){
                foreach ($utensils as $key => $utensil) {
                    array_push($auxUtensil,$utensil);
                }
            }  */
            /*dd($utensils); */
        } else {
            $recipe = LanguageRecipe::where('language_subcategory_id', $idSubcategoryLanguage->id)
                ->with('subcategory')
                ->with(['recipe_only' => function ($query) {
                    $query->with('images')->get();
                }])
                ->with('language')
                ->where('language_id', $lang)->get();
        }

     /*    dd($recipe); */




        /*         remover su subcategory y ver tambien lo de la imagen que le falta y a su vez tambien traer la unidad minima eso lo podemos directamente recorrer
        consultar a la base y con eso estamos para sacar cuanto es su  unidad minima agregando a la coleccion el resultado */
        /*      dd($recipe); */
        if ((!is_null($recipe) && (count($recipe) > 0)) || (!is_null($utensils) && (count($utensils) > 0))) {
            return view('users.recipes.list', compact('recipe', 'utensils','idSubcategory'));
        }




        /*         $notification = Notification::Notification('no hay recetas en ese idioma','error'); */
        /* ->with('notification',$notification) */
        return redirect('/')->with('recipeSubcategory', TRUE);
    }


    public function recipeSubcategory($idSubcategory)
    {
        $recipeStepLanguage = [];
        $utensils = [];
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

        $idSubcategoryLanguage = LanguageSubcategory::where('subcategory_id', $idSubcategory)->where('language_id', $lang)->first();

        $recipeStepLanguage =  LanguageRecipe::where('language_subcategory_id', $idSubcategoryLanguage->id)
            ->with('subcategory')
            ->with(['recipe_only' => function ($query) {
                $query->with('images')->get();
            }])
            ->with('language')
            ->where('language_id', $lang)->get();


        $idSubcat = $idSubcategoryLanguage->id;
        $utensils = UtensilLanguage::where('language_id', $lang)->with('utensil_only')->with(['subcategory_utensil' => function ($querySubcategory) use ($idSubcat) {
            $querySubcategory->where('language_subcategory_id', $idSubcat)->get();
        }])->get();

        $utensils = $utensils->filter(function ($utensil, $key) {
            return !is_null($utensil->subcategory_utensil) && count($utensil->subcategory_utensil) > 0;
        });
        /*         dd($recipeStepLanguage); */
        if ((!is_null($recipeStepLanguage) && count($recipeStepLanguage) > 0) || (!is_null($utensils) && count($utensils) > 0)) {
            return response()->json([
                'status' => '200',
                'recipes' => $recipeStepLanguage,
                'utensils'  =>  $utensils
            ]);
        }
        return response()->json([
            'status' => '400',
            'recipes' => [],
            'utensils'  => []
        ]);
    }

/*

    protected function totalCart($userId)
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
            $selectPerson = $valueDetailsCart->selectPerson;
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
                        $arrayProduct[$valueSubDetailsCart->ingredient_language_id] += (($selectPerson * $ingredientsRecipe->quantity) / $minPerson);
                    } else {
                        $arrayProduct[$valueSubDetailsCart->ingredient_language_id] += ($selectPerson * $ingredientsRecipe->quantity);
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


        return $totalGeneral;
    } */
}
