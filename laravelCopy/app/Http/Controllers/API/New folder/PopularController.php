<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\nucooks\entities\CategorySubcategory;
use App\nucooks\entities\FavoritesRecipesUser;
use App\nucooks\entities\Language;
use App\nucooks\entities\LanguageRecipe;
use App\nucooks\entities\PopularLanguage;
use App\nucooks\entities\RecipeTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PopularController extends Controller
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
    public function popularShowLanguage($popularId,$language){

        if($popularId<5 && $language=='es'){
            $idNew = $popularId + 5;
        }else if($popularId>5 && $popularId<10 && $language=='pt-br'){
            $idNew = $popularId - 5;
        }
        if(!is_null($idNew)){
            if (Auth::user()){
                dd('stop');
            }else{
                $popular = PopularLanguage::where('id',$idNew)->with('populars_tags')->first();
                $recipeArray = [];
                if(!is_null($popular)){

                    foreach ($popular->populars_tags as $popularTag) {
                        $popularTagId = $popularTag->tag_id;
                        $recipesTags = LanguageRecipe::where('language_id',$popular->language_id)
                            ->with(['language_tag_recipe' => function ($queryRecipeTag) use($popularTagId){
                                $queryRecipeTag->where('tag_id',$popularTagId)->get();
                            }])
                            ->with('recipe_only')
                        ->get();

                        $recipesTags = $recipesTags->filter(function ($recipeTag, $key) {
                            return ($recipeTag->language_tag_recipe != null && count($recipeTag->language_tag_recipe)>0);
                        });
                        if(!is_null($recipesTags)){
                            foreach ($recipesTags as $recipeTag) {
                                $imgPath=null;
                                $categoryDescription='';
                                $subCategoryDescription='';
                                if(!is_null($recipeTag->language_subcategory_id)){
                                    $categorySubcategory = CategorySubcategory::with('category','subcategory')->where('subcategory_id',$recipeTag->language_subcategory_id)->first();
                                    if(!is_null($categorySubcategory)){
                                        $categoryDescription=$categorySubcategory->category->category;
                                        $subCategoryDescription=$categorySubcategory->subcategory->subcategory;
                                    }
                                }

                                if(!is_null($recipeTag->recipe_only->images)) $imgPath = $recipeTag->recipe_only->images[0]->path;

                                array_push($recipeArray,[
                                    'idPopular'         =>  $idNew,
                                    'idLanguageRecipe'  =>  $recipeTag->id,
                                    'categorySubcategory'   =>  $categoryDescription.' / '.$subCategoryDescription,
                                    'short_description' =>  $recipeTag->short_description,
                                    'description'       =>  $recipeTag->description,
                                    'imagePath'         =>  $imgPath,
                                    'favorite'           =>  false,
                                ]);
                            }
                        }

                    }
                    return response()->json([
                        'status' => 200,
                        'recipeArrayNew' => $recipeArray,
                    ]);
                   /*  dd($recipeArray); */
                    /* return view('users.popular.index',compact('recipeArray')); */
                }
            }
        }
        return redirect('/')->with('popularTag', TRUE);
    }
    public function popularShow($popularId){
 /*        if (Auth::user()){
            dd('stop xxxxxxxxxx');
        }else{ */
            $popular = PopularLanguage::where('id',$popularId)->with('populars_tags')->first();
            $recipeArray = [];
            if(!is_null($popular)){

                foreach ($popular->populars_tags as $popularTag) {
                    $popularTagId = $popularTag->tag_id;
                    $recipesTags = LanguageRecipe::where('language_id',$popular->language_id)
                        ->with(['language_tag_recipe' => function ($queryRecipeTag) use($popularTagId){
                            $queryRecipeTag->where('tag_id',$popularTagId)->get();
                        }])
                        ->with('recipe_only')
                    ->get();

                    $recipesTags = $recipesTags->filter(function ($recipeTag, $key) {
                        return ($recipeTag->language_tag_recipe != null && count($recipeTag->language_tag_recipe)>0);
                    });
                    if(!is_null($recipesTags)){
                        foreach ($recipesTags as $recipeTag) {
                            $imgPath=null;
                            $categoryDescription='';
                            $subCategoryDescription='';
                            if(!is_null($recipeTag->language_subcategory_id)){
                                $categorySubcategory = CategorySubcategory::with('category','subcategory')->where('subcategory_id',$recipeTag->language_subcategory_id)->first();
                                if(!is_null($categorySubcategory)){
                                    $categoryDescription=$categorySubcategory->category->category;
                                    $subCategoryDescription=$categorySubcategory->subcategory->subcategory;
                                }
                            }


                            if(!is_null($recipeTag->recipe_only->images)) $imgPath = $recipeTag->recipe_only->images[0]->path;
                            $favoriteStatus = false;
                            if(Auth::user()) {
                                $existFavoritUser = FavoritesRecipesUser::where('user_id',Auth::user()->id)->where('language_recipe_id',$recipeTag->id)->first();
                                if(!is_null($existFavoritUser)) $favoriteStatus = true;
                            }

                            array_push($recipeArray,[
                                'idPopular'         =>  $popularId,
                                'idLanguageRecipe'  =>  $recipeTag->id,
                                'categorySubcategory'   =>  $categoryDescription.' / '.$subCategoryDescription,
                                'short_description' =>  $recipeTag->short_description,
                                'description'       =>  $recipeTag->description,
                                'imagePath'         =>  $imgPath,
                                'favorite'           => $favoriteStatus,
                            ]);
                        }
                    }

                }
               /*  dd($recipeArray); */
                return view('users.popular.index',compact('recipeArray'));
            }
  /*       } */
        return redirect('/')->with('popularTag', TRUE);
    }

    public function populars(){
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
        $populars = PopularLanguage::select('id','description','language_id')->where('language_id', $langId)->orderBy('position','ASC')->get();
        $length=0;
        if(!is_null($populars)){
            foreach ($populars as $popular) {
                if(strlen($popular->description) > $length) $length = strlen($popular->description);
            }
            $stringPopular='';
            switch ($length) {
                case $length<=6:
                    $style='font-size: 50px;';
                    break;
                case ($length>6 and $length<=9):
                    $style='font-size: 41px;';
                    break;
                case ($length>9 and $length<=13):
                    $style='font-size: 30px;';
                    break;
                case ($length>13 and $length<=18):
                    $style='font-size: 26px;';
                    break;
                case ($length>18 and $length<=26):
                    $style='font-size: 17px;';
                    break;
                default:
                    break;
            }
            foreach ($populars as $key => $popular) {
                $stringPopular .= '<div
                    style="
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: center;
                    max-height: 150px;
                    overflow: hidden;
                    color: #F05739;
                    font-family: dosis_regular !important; '.$style.'">'.mb_strtoupper($popular->description,'utf-8').'</div>';
                if($key+1 < count($populars))
                    $stringPopular .= '<div
                        style="
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: center;
                        max-height: 150px;
                        overflow: hidden;
                        color: #FFCB38;
                        font-family: dosis_regular !important; '.$style.'">/</div>';
            }
        }

        return response()->json([
            'status' => '200',
            'populars' => $populars,
            'length' => $length,
            'stringPopular' => $stringPopular
        ]);
    }

}
