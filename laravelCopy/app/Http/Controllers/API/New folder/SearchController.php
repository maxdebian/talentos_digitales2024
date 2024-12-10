<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\nucooks\entities\Language;
use App\nucooks\entities\LanguageRecipe;
use App\nucooks\entities\CategoryLanguage;
use App\nucooks\entities\FavoritesRecipesUser;
use App\nucooks\entities\LanguageSubcategory;
use App\nucooks\entities\RecipeTag;
use App\nucooks\entities\Search;
use App\nucooks\entities\Tag;

use App\User;
use Carbon\Carbon;
use Session;

class SearchController extends Controller
{


    public function index($content,$lang){
        $contentOriginal = $content;

        if(Auth::user()){
            $lang = Auth::user()->language_id;
        }else{
            $lang = $lang;
            if(is_null($lang)){
                $lang="pt-br";
            }
            $lang = Language::where('lang',$lang)->first();
            if(!is_null($lang)){
                $lang = $lang->id;
            }else{
                $lang="pt-br";
            }

        }
        $content = $this->removeLetters($content,$lang);

        $partsContents= explode(' ',$content);

        $recipesSubcategories = array();
        $index= 0;


        if(count($partsContents)>0){
            for ($i=0; $i <count($partsContents) ; $i++) {
                if($partsContents[$i]!=""){
                    $format = htmlspecialchars($partsContents[$i]);
                    /* echo('<br> ---> '.$partsContents[$i]); */

                    /* RECIPES GLOBAL */
                    $recipes = LanguageRecipe::with(['recipe_only' => function($query){$query->with('images')->get();}])
                                    ->with('ingredient_language_recipes')
                                    ->where('short_description','like','%'.$format.'%')
                                    ->orWhere('description','like','%'.$format.'%')
                                    ->where('language_id',$lang)->get();

                    foreach ($recipes as $valRecipes) {
                        $recipesSubcategories[$index] = LanguageRecipe::where('id',$valRecipes->id)
                        ->with('subcategory')
                        ->with(['recipe_only' => function($query){$query->with('images')->get();}])
                        ->with('language')
                        ->where('language_id',$lang)->get();

                        $index++;
                    }


                    /* CATEGORIES */

                    $categories = CategoryLanguage::where('description','like','%'.$format.'%')->with(['category' => function($queryCategory) {
                        $queryCategory->with('subcategories')->where('view_menu',1)->get();
                    }])->where('language_id',$lang)->get();




                    foreach ($categories as $valCategory) {
                        foreach ($valCategory->category->subcategories as $valSubcategory) {
                            $recipesSubcategories[$index] = LanguageRecipe::where('language_subcategory_id',$valSubcategory->id)
                            ->with('subcategory')
                            ->with(['recipe_only' => function($query){$query->with('images')->get();}])
                            ->with('language')
                            ->where('language_id',$lang)->get();

                            $index++;
                        }
                    }

                    /*  SUBCATEGORIES */

                    $subcategoryLanguages = LanguageSubcategory::where('description','like','%'.$format.'%')->where('language_id',$lang)->get();

                    foreach ($subcategoryLanguages as $valSubcategory) {


                        $recipesSubcategories[$index] = LanguageRecipe::where('language_subcategory_id',$valSubcategory->id)
                        ->with('subcategory')
                        ->with(['recipe_only' => function($query){$query->with('images')->get();}])
                        ->with('language')
                        ->where('language_id',$lang)->get();


                        $index++;


                }
                    /*  dd($recipesSubcategories); */
                    /* TAGS */
                    $tags = Tag::where('tag','like','%'.$format.'%')->where('language_id',$lang)->get();

                    foreach ($tags as $valTag) {
                            $recipeTags = RecipeTag::where('tag_id',$valTag->id)->get();
                            foreach ($recipeTags as $valRecipesTag) {
                                $recipesSubcategories[$index] = LanguageRecipe::where('id',$valRecipesTag->language_recipe_id)
                                ->with('subcategory')
                                ->with(['recipe_only' => function($query){$query->with('images')->get();}])
                                ->with('language')
                                ->where('language_id',$lang)->get();
                                $index++;
                            }
                    }


                }
            }
        }


    /*     dd($partsContents); */






       /* $count=0; */
       if(count($recipesSubcategories)>0){
            $recipeArray = [];
            foreach ($recipesSubcategories as $key => $recipeIndividualArray) {
                foreach ($recipeIndividualArray as $key => $recipe) {
                    $flagFavorite = false;
                    if(Auth::user()){
                        $resultFavorite =  FavoritesRecipesUser::where('user_id',Auth::user()->id)->where('language_recipe_id',$recipe->id)->first();
                        if(!is_null($resultFavorite)) $flagFavorite=true;

                    }

                    $imgPath=null;

                    if(!is_null($recipe->recipe_only->images)) $imgPath = $recipe->recipe_only->images[0]->path;
                    array_push($recipeArray,[
                        'idLanguageRecipe'  =>  $recipe->id,
                        'short_description' =>  $recipe->short_description,
                        'description'       =>  $recipe->description,
                        'imagePath'         =>  $imgPath,
                        'favorite'           =>  $flagFavorite,
                    ]);
                }
            }
            if(count($recipeArray)>0){
                $this->saveSearch($contentOriginal,$lang,1);
                return response()->json([
                    'status' => 200,
                    'searchArrayNew' => $recipeArray,
                ]);
/*                 $recipeArray = json_encode($recipeArray);
                return view('users.recipes.search',compact('recipeArray')); */
            }


        }else{
            $this->saveSearch($contentOriginal,$lang,0);
        }
        return redirect('/')->with('search',TRUE);
    }

    public function indexBiz($content,$lang){
        $contentOriginal = $content;

        if(Auth::user()){
            $lang = Auth::user()->language_id;
        }else{
            $lang = $lang;
            if(is_null($lang)){
                $lang="pt-br";
            }
            $lang = Language::where('lang',$lang)->first();
            if(!is_null($lang)){
                $lang = $lang->id;
            }else{
                $lang="pt-br";
            }

        }
        $content = $this->removeLetters($content,$lang);
        $partsContents= explode(' ',$content);

        $recipesSubcategories = array();
        $index= 0;


        if(count($partsContents)>0){
            for ($i=0; $i <count($partsContents) ; $i++) {
                if($partsContents[$i]!=""){
                    $format = htmlspecialchars($partsContents[$i]);

                    /* RECIPES GLOBAL */
                    $recipes = LanguageRecipe::with(['recipe_only' => function($query){$query->with('images')->get();}])
                                    ->with('ingredient_language_recipes')
                                    ->where('short_description','like','%'.$format.'%')
                                    ->orWhere('description','like','%'.$format.'%')
                                    ->where('language_id',$lang)->get();

                    foreach ($recipes as $valRecipes) {
                        $recipesSubcategories[$index] = LanguageRecipe::where('id',$valRecipes->id)
                        ->with('subcategory')
                        ->with(['recipe_only' => function($query){$query->with('images')->get();}])
                        ->with('language')
                        ->where('language_id',$lang)->get();

                        $index++;
                    }


                    /* CATEGORIES */

                    $categories = CategoryLanguage::where('description','like','%'.$format.'%')->with(['category' => function($queryCategory) {
                        $queryCategory->with('subcategories')->where('view_menu',1)->get();
                    }])->where('language_id',$lang)->get();




                    foreach ($categories as $valCategory) {
                        foreach ($valCategory->category->subcategories as $valSubcategory) {
                            $recipesSubcategories[$index] = LanguageRecipe::where('language_subcategory_id',$valSubcategory->id)
                            ->with('subcategory')
                            ->with(['recipe_only' => function($query){$query->with('images')->get();}])
                            ->with('language')
                            ->where('language_id',$lang)->get();

                            $index++;
                        }
                    }

                    /*  SUBCATEGORIES */

                    $subcategoryLanguages = LanguageSubcategory::where('description','like','%'.$format.'%')->where('language_id',$lang)->get();

                    foreach ($subcategoryLanguages as $valSubcategory) {


                        $recipesSubcategories[$index] = LanguageRecipe::where('language_subcategory_id',$valSubcategory->id)
                        ->with('subcategory')
                        ->with(['recipe_only' => function($query){$query->with('images')->get();}])
                        ->with('language')
                        ->where('language_id',$lang)->get();


                        $index++;


                }
                    /*  dd($recipesSubcategories); */
                    /* TAGS */
                    $tags = Tag::where('tag','like','%'.$format.'%')->where('language_id',$lang)->get();

                    foreach ($tags as $valTag) {
                            $recipeTags = RecipeTag::where('tag_id',$valTag->id)->get();
                            foreach ($recipeTags as $valRecipesTag) {
                                $recipesSubcategories[$index] = LanguageRecipe::where('id',$valRecipesTag->language_recipe_id)
                                ->with('subcategory')
                                ->with(['recipe_only' => function($query){$query->with('images')->get();}])
                                ->with('language')
                                ->where('language_id',$lang)->get();
                                $index++;
                            }
                    }


                }
            }
        }


    /*     dd($partsContents); */






       /* $count=0; */
       if(count($recipesSubcategories)>0){
            $recipeArray = [];
            foreach ($recipesSubcategories as $key => $recipeIndividualArray) {
                foreach ($recipeIndividualArray as $key => $recipe) {
                    $flagFavorite = false;
                    if(Auth::user()){
                        $resultFavorite =  FavoritesRecipesUser::where('user_id',Auth::user()->id)->where('language_recipe_id',$recipe->id)->first();
                        if(!is_null($resultFavorite)) $flagFavorite=true;

                    }

                    $imgPath=null;

                    if(!is_null($recipe->recipe_only->images)) $imgPath = $recipe->recipe_only->images[0]->path;
                    array_push($recipeArray,[
                        'idLanguageRecipe'  =>  $recipe->id,
                        'short_description' =>  $recipe->short_description,
                        'description'       =>  $recipe->description,
                        'imagePath'         =>  $imgPath,
                        'favorite'           =>  $flagFavorite,
                    ]);
                }
            }
            if(count($recipeArray)>0){
                $this->saveSearch($contentOriginal,$lang,1);
                $recipeArray = json_encode($recipeArray);
                return view('users.recipes.search',compact('recipeArray'));
            }


        }else{
            $this->saveSearch($contentOriginal,$lang,0);
        }
        return redirect('/')->with('search',TRUE);
    }

    protected function removeLetters($content,$lang){
        switch ($lang) {
            case 1:
                $wordlist = array("acerca","agora","algmas","alguns","ali","ambos","antes","apontar","aquela","aquelas","aquele","aqueles","aqui","atrás","bem","bom","cada","caminho","cima","com","como","comprido","conhecido","corrente","das","debaixo","dentro","desde","desligado","deve","devem","deverá","direita","diz","dizer","dois","dos","e","é","ela","ele","eles","em","enquanto","então","está","estado","estão","estar","estará","este","estes","esteve","estive","estivemos","estiveram","eu","fará","faz","fazer","fazia","fez","fim","foi","fora","horas","iniciar","inicio","ir","irá","ista","iste","isto","ligado","maioria","maiorias","mais","mas","mesmo","meu","muito","muitos","não","nome","nós","nosso","novo","o","onde","os","ou","outro","para","parte","pegar","pelo","pessoas","pode","poderá","podia","por","porque","povo","promeiro","qual","qualquer","quando","quê","quem","quieto","saber","são","sem","ser","seu","somente","tal","também","tem","têm","tempo","tenho","tentar","tentaram","tente","tentei","teu","teve","tipo","tive","todos","trabalhar","trabalho","tu","último","um","uma","umas","uns","usa","usar","valor","veja","ver","verdade","verdadeiro","você","a","à","adeus","aí","ainda","além","algo","algumas","ano","anos","ao","aos","apenas","apoio","após","aquilo","área","as","às","assim","até","através","baixo","bastante","boa","boas","bons","breve","cá","catorze","cedo","cento","certamente","certeza","cinco","coisa","conselho","contra","custa","da","dá","dão","daquela","daquelas","daquele","daqueles","dar","de","demais","depois","dessa","dessas","desse","desses","desta","destas","deste","destes","dez","dezanove","dezasseis","dezassete","dezoito","dia","diante","dizem","do","doze","duas","dúvida","elas","embora","entre","era","és","essa","essas","esse","esses","esta","estas","estás","estava","estiveste","estivestes","estou","exemplo","faço","falta","favor","fazeis","fazem","fazemos","fazes","final","fomos","for","foram","forma","foste","fostes","fui","geral","grande","grandes","grupo","há","hoje","hora","isso","já","lá","lado","local","logo","longe","lugar","maior","mal","máximo","me","meio","menor","menos","mês","meses","meus","mil","minha","minhas","momento","na","nada","naquela","naquelas","naquele","naqueles","nas","nem","nenhuma","nessa","nessas","nesse","nesses","nesta","nestas","neste","nestes","nível","no","noite","nos","nossa","nossas","nossos","nova","novas","nove","novos","num","numa","número","nunca","obra","obrigada","obrigado","oitava","oitavo","oito","ontem","onze","outra","outras","outros","parece","partir","paucas","pela","pelas","pelos","perto","pôde","podem","poder","põe","põem","ponto","pontos","porquê","posição","possível","possivelmente","posso","pouca","pouco","poucos","primeira","primeiras","primeiro","primeiros","própria","próprias","próprio","próprios","próxima","próximas","próximo","próximos","puderam","quáis","quanto","quarta","quarto","quatro","que","quer","quereis","querem","queremas","queres","quero","questão","quinta","quinto","quinze","relação","sabe","sabem","se","segunda","segundo","sei","seis","sempre","seria","sete","sétima","sétimo","seus","sexta","sexto","sim","sistema","sob","sobre","sois","somos","sou","sua","suas","talvez","tanta","tantas","tanto","tão","tarde","te","temos","tendes","tens","ter","terceira","terceiro","teus","tivemos","tiveram","tiveste","tivestes","toda","todas","todo","três","treze","tua","tuas","tudo","vai","vais","vão","vários","vem","vêm","vens","vez","vezes","viagem","vindo","vinte","vocês","vos","vós","vossa","vossas","vosso","vossos","zero");
                break;
            case 2:
                $wordlist = array("algún","alguna","algunas","alguno","algunos","ambos","ampleamos","ante","antes","aquel","aquellas","aquellos","aqui","arriba","atras","bajo","bastante","bien","cada","cierta","ciertas","cierto","ciertos","como","con","conseguimos","conseguir","consigo","consigue","consiguen","consigues","cual","cuando","dentro","desde","donde","dos","el","ellas","ellos","empleais","emplean","emplear","empleas","empleo","en","encima","entonces","entre","era","eramos","eran","eras","eres","es","esta","estaba","estado","estais","estamos","estan","estoy","fin","fue","fueron","fui","fuimos","gueno","ha","hace","haceis","hacemos","hacen","hacer","haces","hago","incluso","intenta","intentais","intentamos","intentan","intentar","intentas","intento","ir","la","largo","las","lo","los","mientras","mio","modo","muchos","muy","nos","nosotros","otro","para","pero","podeis","podemos","poder","podria","podriais","podriamos","podrian","podrias","por","por qué","porque","primero","puede","pueden","puedo","quien","sabe","sabeis","sabemos","saben","saber","sabes","ser","si","siendo","sin","sobre","sois","solamente","solo","somos","soy","su","sus","también","teneis","tenemos","tener","tengo","tiempo","tiene","tienen","todo","trabaja","trabajais","trabajamos","trabajan","trabajar","trabajas","trabajo","tras","tuyo","ultimo","un","una","unas","uno","unos","usa","usais","usamos","usan","usar","usas","uso","va","vais","valor","vamos","van","vaya","verdad","verdadera","verdadero","vosotras","vosotros","voy","yo","él","ésta","éstas","éste","éstos","última","últimas","último","últimos","a","añadió","aún","actualmente","adelante","además","afirmó","agregó","ahí","ahora","al","algo","alrededor","anterior","apenas","aproximadamente","aquí","así","aseguró","aunque","ayer","buen","buena","buenas","bueno","buenos","cómo","casi","cerca","cinco","comentó","conocer","consideró","considera","contra","cosas","creo","cuales","cualquier","cuanto","cuatro","cuenta","da","dado","dan","dar","de","debe","deben","debido","decir","dejó","del","demás","después","dice","dicen","dicho","dieron","diferente","diferentes","dijeron","dijo","dio","durante","e","ejemplo","ella","ello","embargo","encuentra","esa","esas","ese","eso","esos","está","están","estaban","estar","estará","estas","este","esto","estos","estuvo","ex","existe","existen","explicó","expresó","fuera","gran","grandes","había","habían","haber","habrá","hacerlo","hacia","haciendo","han","hasta","hay","haya","he","hecho","hemos","hicieron","hizo","hoy","hubo","igual","indicó","informó","junto","lado","le","les","llegó","lleva","llevar","luego","lugar","más","manera","manifestó","mayor","me","mediante","mejor","mencionó","menos","mi","misma","mismas","mismo","mismos","momento","mucha","muchas","mucho","nada","nadie","ni","ningún","ninguna","ningunas","ninguno","ningunos","no","nosotras","nuestra","nuestras","nuestro","nuestros","nueva","nuevas","nuevo","nuevos","nunca","o","ocho","otra","otras","otros","parece","parte","partir","pasada","pasado","pesar","poca","pocas","poco","pocos","podrá","podrán","podría","podrían","poner","posible","próximo","próximos","primer","primera","primeros","principalmente","propia","propias","propio","propios","pudo","pueda","pues","qué","que","quedó","queremos","quién","quienes","quiere","realizó","realizado","realizar","respecto","sí","sólo","se","señaló","sea","sean","según","segunda","segundo","seis","será","serán","sería","sido","siempre","siete","sigue","siguiente","sino","sola","solas","solos","son","tal","tampoco","tan","tanto","tenía","tendrá","tendrán","tenga","tenido","tercera","toda","todas","todavía","todos","total","trata","través","tres","tuvo","usted","varias","varios","veces","ver","vez","y","ya");
                break;
            case 3:
                $wordlist = array("able","about","above","abroad","according","accordingly","across","actually","adj","after","afterwards","again","against","ago","ahead","ain't","all","allow","allows","almost","alone","along","alongside","already","also","although","always","am","amid","amidst","among","amongst","an","and","another","any","anybody","anyhow","anyone","anything","anyway","anyways","anywhere","apart","appear","appreciate","appropriate","are","aren't","around","as","a's","aside","ask","asking","associated","at","available","away","awfully","back","backward","backwards","be","became","because","become","becomes","becoming","been","before","beforehand","begin","behind","being","believe","below","beside","besides","best","better","between","beyond","both","brief","but","by","came","can","cannot","cant","can't","caption","cause","causes","certain","certainly","changes","clearly","c'mon","co","co.","com","come","comes","concerning","consequently","consider","considering","contain","containing","contains","corresponding","could","couldn't","course","c's","currently","dare","daren't","definitely","described","despite","did","didn't","different","directly","do","does","doesn't","doing","done","don't","down","downwards","during","each","edu","eg","eight","eighty","either","else","elsewhere","end","ending","enough","entirely","especially","et","etc","even","ever","evermore","every","everybody","everyone","everything","everywhere","ex","exactly","example","except","fairly","far","farther","few","fewer","fifth","first","five","followed","following","follows","for","forever","former","formerly","forth","forward","found","four","from","further","furthermore","get","gets","getting","given","gives","go","goes","going","gone","got","gotten","greetings","had","hadn't","half","happens","hardly","has","hasn't","have","haven't","having","he","he'd","he'll","hello","help","hence","her","here","hereafter","hereby","herein","here's","hereupon","hers","herself","he's","hi","him","himself","his","hither","hopefully","how","howbeit","however","hundred","i'd","ie","if","ignored","i'll","i'm","immediate","in","inasmuch","inc","inc.","indeed","indicate","indicated","indicates","inner","inside","insofar","instead","into","inward","is","isn't","it","it'd","it'll","its","it's","itself","i've","just","k","keep","keeps","kept","know","known","knows","last","lately","later","latter","latterly","least","less","lest","let","let's","like","liked","likely","likewise","little","look","looking","looks","low","lower","ltd","made","mainly","make","makes","many","may","maybe","mayn't","me","mean","meantime","meanwhile","merely","might","mightn't","mine","minus","miss","more","moreover","most","mostly","mr","mrs","much","must","mustn't","my","myself","name","namely","nd","near","nearly","necessary","need","needn't","needs","neither","never","neverf","neverless","nevertheless","new","next","nine","ninety","no","nobody","non","none","nonetheless","noone","no-one","nor","normally","not","nothing","notwithstanding","novel","now","nowhere","obviously","of","off","often","oh","ok","okay","old","on","once","one","ones","one's","only","onto","opposite","or","other","others","otherwise","ought","oughtn't","our","ours","ourselves","out","outside","over","overall","own","particular","particularly","past","per","perhaps","placed","please","plus","possible","presumably","probably","provided","provides","que","quite","qv","rather","rd","re","really","reasonably","recent","recently","regarding","regardless","regards","relatively","respectively","right","round","said","same","saw","say","saying","says","second","secondly","see","seeing","seem","seemed","seeming","seems","seen","self","selves","sensible","sent","serious","seriously","seven","several","shall","shan't","she","she'd","she'll","she's","should","shouldn't","since","six","so","some","somebody","someday","somehow","someone","something","sometime","sometimes","somewhat","somewhere","soon","sorry","specified","specify","specifying","still","sub","such","sup","sure","take","taken","taking","tell","tends","th","than","thank","thanks","thanx","that","that'll","thats","that's","that've","the","their","theirs","them","themselves","then","thence","there","thereafter","thereby","there'd","therefore","therein","there'll","there're","theres","there's","thereupon","there've","these","they","they'd","they'll","they're","they've","thing","things","think","third","thirty","this","thorough","thoroughly","those","though","three","through","throughout","thru","thus","till","to","together","too","took","toward","towards","tried","tries","truly","try","trying","t's","twice","two","un","under","underneath","undoing","unfortunately","unless","unlike","unlikely","until","unto","up","upon","upwards","us","use","used","useful","uses","using","usually","v","value","various","versus","very","via","viz","vs","want","wants","was","wasn't","way","we","we'd","welcome","well","we'll","went","were","we're","weren't","we've","what","whatever","what'll","what's","what've","when","whence","whenever","where","whereafter","whereas","whereby","wherein","where's","whereupon","wherever","whether","which","whichever","while","whilst","whither","who","who'd","whoever","whole","who'll","whom","whomever","who's","whose","why","will","willing","wish","with","within","without","wonder","won't","would","wouldn't","yes","yet","you","you'd","you'll","your","you're","yours","yourself","yourselves","you've","zero","a","how's","i","when's","why's","b","c","d","e","f","g","h","j","l","m","n","o","p","q","r","s","t","u","uucp","w","x","y","z","I","www","amount","bill","bottom","call","computer","con","couldnt","cry","de","describe","detail","due","eleven","empty","fifteen","fifty","fill","find","fire","forty","front","full","give","hasnt","herse","himse","interest","itse”","mill","move","myse”","part","put","show","side","sincere","sixty","system","ten","thick","thin","top","twelve","twenty","abst","accordance","act","added","adopted","affected","affecting","affects","ah","announce","anymore","apparently","approximately","aren","arent","arise","auth","beginning","beginnings","begins","biol","briefly","ca","date","ed","effect","et-al","ff","fix","gave","giving","heres","hes","hid","home","id","im","immediately","importance","important","index","information","invention","itd","keys","kg","km","largely","lets","line","'ll","means","mg","million","ml","mug","na","nay","necessarily","nos","noted","obtain","obtained","omitted","ord","owing","page","pages","poorly","possibly","potentially","pp","predominantly","present","previously","primarily","promptly","proud","quickly","ran","readily","ref","refs","related","research","resulted","resulting","results","run","sec","section","shed","shes","showed","shown","showns","shows","significant","significantly","similar","similarly","slightly","somethan","specifically","state","states","stop","strongly","substantially","successfully","sufficiently","suggest","thered","thereof","therere","thereto","theyd","theyre","thou","thoughh","thousand","throug","til","tip","ts","ups","usefully","usefulness","'ve","vol","vols","wed","whats","wheres","whim","whod","whos","widely","words","world","youd","youre");
                break;
            default:
                $wordlist = array();
                break;
        }

/*         $wordlist = array("de", "como", "que",'con','la','lo','para','por','en','o','a', 'bajo','ante', 'desde', 'entre', 'mediante', 'según','segun','sobre', 'y'); */

        foreach ($wordlist as &$Word) {
            $Word = '/\b' . preg_quote(mb_strtoupper($Word,'utf-8'), '/') . '\b/';
        }

        $content = preg_replace($wordlist, '', mb_strtoupper($content,'utf-8'));
        return $content;
    }
    protected function saveSearch($contentOriginal,$lang,$state){
        $search = Search::where('search',mb_strtoupper($contentOriginal,'utf-8'))->where('language_id',$lang)->first();
        if(is_null($search)){
            Search::create([
                'language_id'   => $lang,
                'search'        => mb_strtoupper($contentOriginal,'utf-8'),
                'state'         => $state,
                'date'          =>  Carbon::now()->format('Y-m-d'),
            ]);
        }

    }
}
