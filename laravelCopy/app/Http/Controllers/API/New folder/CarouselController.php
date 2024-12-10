<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\nucooks\entities\CarouselAdministration;
use App\nucooks\entities\FavoritesRecipesUser;
use App\nucooks\entities\Language;
use App\nucooks\entities\LanguageRecipe;
use App\nucooks\entities\LanguageSubcategory;
use App\nucooks\entities\Recipe;
use App\nucooks\entities\RecipeUser;
use App\nucooks\entities\Subcategory;
use App\nucooks\entities\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarouselController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['carousels']]);
    }
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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




    public function carousels($lang){
        $flagUser = false;
        if (Auth::user()){
            $langId = Auth::user()->language_id;
            $flagUser=true;
        }else{
            if (($lang != "es") && ($lang != "en") && ($lang != "pt-br")) {
                $lang = "pt-br";
            }
            $langId = Language::where('lang', $lang)->first();
            $langId = $langId->id;
        }

        $carousels = CarouselAdministration::select('id','is_active','position')->where('is_active',1)
                        ->with(['carousellang' => function($queryCaroselLang) use($langId){
                            $queryCaroselLang->select('id','title','carousel_admin_id','language_id',)
                               /*  ->with('carousel_lang_subcategories') */
                                ->with(['carousel_lang_subcategories' => function ($querySubcategory){
                                    $querySubcategory->with('languageSubcategory')->get();
                                }])
                                ->where('language_id',$langId)->get();
                        }])->orderBy('position','ASC')->get();


        $tagArray=[];
        $carouselArray = [];
        if(!is_null($carousels)){
            foreach ($carousels as $carousel) {
                if(!is_null($carousel->carousellang)){
                    foreach ($carousel->carousellang as $carouseLang) {
                        $carouselArray[] = [
                            'id'                =>  $carouseLang->id,
                            'title'             =>  $carouseLang->title,
                        ];

                        $key = array_search([
                                'id'    =>  $carouseLang->id,
                                'title' =>  $carouseLang->title,
                                ],$carouselArray);



                       if(!is_null($key) && !is_null($carouseLang->carousel_lang_subcategories) && count($carouseLang->carousel_lang_subcategories)>0){


                            foreach ($carouseLang->carousel_lang_subcategories as $langSubcategory) {

                                if(!is_null($langSubcategory->languageSubcategory)){
                                    foreach ($langSubcategory->languageSubcategory as $subcategory) {

                                       /*  echo('<pre>');
                                        print_r($subcategory);
                                        echo('</pre>');
                                        dd('stop'); */
                                        $tagArray[$subcategory->id]= $carouseLang->language_id;
                                    }

                                }

                            }
                            $carouselArray[$key]['subCategories'] = $tagArray;

                            $tagArray=[];
                        }

                    }
                }
            }
        }
        # code...
/*         echo('<pre>');
        print_r($carouselArray);
        echo('</pre>');
        echo('<br><br><br>_________________________________________________________________________________<br><br><br>'); */
       /*  dd($carousels); */
        foreach ($carouselArray as $key => $valueHead) {
            foreach ($valueHead['subCategories'] as $keySubcategory => $valueLang) {
                /* echo(' keySubcategory: '.$keySubcategory.'    valueLang: '.$valueLang); */

                $subcategoryRecipes = LanguageSubcategory::where('id',$keySubcategory)->where('language_id',$valueLang)
                    ->with(['sub_category' => function ($querySubcategory){
                        $querySubcategory->with('categories')->get();
                    }])
                    ->with(['language_recipe' => function($queryRecipeLanguage)use($valueLang){
                        $queryRecipeLanguage->with(['recipe_only' => function ($queryRecipeOnly){
                            $queryRecipeOnly->with('images')->get();
                        }])->where('language_id',$valueLang)->get();
                }])->get();
              /*   echo('<pre>');
                print_r($subcategoryRecipes[0]);
                echo('</pre>');
                echo($subcategoryRecipes[0]->sub_category->subcategory.'  ....  '.$subcategoryRecipes[0]->sub_category->categories[0]->category);
                dd($carousels); */
                $dateNow = Carbon::now();
                if(!is_null($subcategoryRecipes)){
                    $subCategoryDescription='';
                    $categoryDescription='';
                    foreach ($subcategoryRecipes as $subCategory) {

                        if(!is_null($subCategory->language_recipe)){
                            $imgPath='/images/recipes/avatarRecipe.png';
                            if(!is_null($subCategory->sub_category->subcategory) && !is_null($subCategory->sub_category->categories[0]->category)){
                                $subCategoryDescription= $subCategory->sub_category->subcategory;
                                $categoryDescription = $subCategory->sub_category->categories[0]->category;
                            }

                            if(!is_null($subCategory->language_recipe)){
                                foreach ($subCategory->language_recipe as $langSubcategory) {
                                    $dateDiference = $langSubcategory->created_at->diffInDays($dateNow);
/*                                     echo('<br>-----> '.$langSubcategory->created_at.'---'.$dateNow.'-----'.$dateDiference.'----'.$langSubcategory->id); */
                                    $flagFavorite = false;
                                    if($flagUser){
                                        $resultFavorite =  FavoritesRecipesUser::where('user_id',Auth::user()->id)->where('language_recipe_id',$langSubcategory->id)->first();
                                        if(!is_null($resultFavorite)) $flagFavorite=true;

                                    }


                                    if(!is_null($langSubcategory->recipe_only->images[0])) $imgPath = $langSubcategory->recipe_only->images[0]->path;
                                    $carouselArray[$key]['recipe_language'][$langSubcategory->id]=[
                                        'id' => $langSubcategory->id,
                                        'categorySubcategory'   =>  $categoryDescription.' / '.$subCategoryDescription,
                                        'short_description' => $langSubcategory->short_description,
                                        'description'       => $langSubcategory->description,
                                        'imagePath'         => $imgPath,
                                        'favorite'          => $flagFavorite,
                                        'stateRecipeNew'         => $dateDiference <= 15 ? true : false,
                                    ];
                                    /* $res = $dateDiference <= 15 ? true : false;
                                    echo('<br>...... '.$res); */
                                }

                            }
/*                             dd('stop'); */
                        }
                    }
                }


               /*  $recipeStepLanguage =  LanguageRecipe::recipeScope()->where('language_id', $valueLang)->with(['tags_carousels' => function($queryTag)use($keyTag){
                    $queryTag->where('id',$keyTag)->get();
                }])->get();

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
                } */


            }

        }


/*        echo('<pre>');
        print_r($carouselArray);
        echo('</pre>');

     dd('stop'); */
        if(count($carouselArray)>0){
            $arrayHeadCarousel = [];
            $i=0;

            $auxI=0;
            foreach ($carouselArray as $keyFirst => $firtsPart) {

                $idCarousel = null;
                $title = null;
                foreach ($firtsPart as $keySecond => $valueSecond) {
                    if($keySecond=='id') $idCarousel = $valueSecond;
                    if($keySecond=='title') $title = $valueSecond;
                    if(!is_null($idCarousel) && !is_null($title)){

                        $arrayHeadCarousel[$i]= [
                            'idCarousel'    => $idCarousel,
                            'title'         => $title,
                            'details'       => ''
                        ];



                        $auxI = $i;
                        $i++;
                        $idCarousel = null;
                        $title = null;
                    }
                    $arrayDetail=[];
                    if($keySecond=='recipe_language'){

                        foreach ($valueSecond as $keyLanguageRecipe => $valueLanguageRecipe) {
                            $idLanguageRecipe = null;
                            $shortDescription = null;
                            $description = null;
                            $imagePath = null;
                            $favorite = null;
                            $categorySubcategory = null;
                            $stateRecipeNew = null;
                            foreach ($valueLanguageRecipe as $keyDetailRecipe => $valueDetailRecipe) {
                                 if($keyDetailRecipe=='id') $idLanguageRecipe = $valueDetailRecipe;
                                if($keyDetailRecipe=='short_description') $shortDescription = $valueDetailRecipe;
                                if($keyDetailRecipe=='description') $description = $valueDetailRecipe;
                                if($keyDetailRecipe=='imagePath') $imagePath = $valueDetailRecipe;
                                if($keyDetailRecipe=='favorite') $favorite = $valueDetailRecipe;
                                if($keyDetailRecipe=='categorySubcategory') $categorySubcategory = $valueDetailRecipe;
                                if($keyDetailRecipe=='stateRecipeNew') $stateRecipeNew = $valueDetailRecipe;


                                /* REPARAR LAS DESCRIPCIONES ESTAN MAL PARA CADA IDIOMA Y TAMBIEN TRAE MAL LA CARGA DE LAS SUBCATEGORIAS EN EL carousels */


                                if(!is_null($idLanguageRecipe) && !is_null($shortDescription) && !is_null($description) && !is_null($imagePath) && !is_null($favorite) && !is_null($categorySubcategory) && !is_null($stateRecipeNew)){
                                    array_push($arrayDetail,[
                                        'idLanguageRecipe'      =>  $idLanguageRecipe,
                                        'categorySubcategory'   =>  $categorySubcategory,
                                        'shortDescription'      =>  $shortDescription,
                                        'description'           =>  $description,
                                        'imagePath'             =>  $imagePath,
                                        'favorite'              =>  $favorite,
                                        'stateRecipeNew'        =>  $stateRecipeNew
                                    ]);

                  /*                   echo('<pre>');
                                    print_r($arrayDetail);
                                    echo('</pre>');

                                 dd('stop'); */
                                    $idLanguageRecipe = null;
                                    $shortDescription = null;
                                    $description = null;
                                    $imagePath = null;
                                }

                            }

                        }
                        $arrayHeadCarousel[$auxI]['details']=$arrayDetail;
                    }




                }

            }
        }

/*        echo('<pre>');
        print_r($arrayHeadCarousel);
        echo('</pre>');
        dd('stop'); */


        return response()->json([
            'status'    => 200,
            'carouselArray' => $arrayHeadCarousel
        ]);

    }

}
