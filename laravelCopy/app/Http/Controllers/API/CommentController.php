<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(){
        // aca podemos implementar API Resources que nos provee laravel
        /* return Comment::all(); */
        return CommentResource::collection(Comment::paginate(10));

    }
    public function store(CommentRequest $request){

        /*
            Aca usaremos que solo guarde los campos validados entonces usamos $request->validated()
            Cabe aclarar que cualquier otro dato que venga en el frmulario y no este en nuestra base dara error
        */

        /* image */
        if($request->hasFile('image')){
            $fileName = $request->image->getClientOriginalName();
            // si queremos hacer un testeo sin hacer el proceso de guardar la imagen lo podemos mandar al log
            info($fileName);
        }


        $comment = Comment::create($request->validated());
        return new CommentResource($comment);
    }


    public function show(Comment $comment){
        return new CommentResource($comment);
    }

    public function update(Comment $comment, CommentRequest $request){
        $comment->update($request->validated());
    }
    public function destroy(Comment $comment){
        $comment->delete();
        return response()->noContent();
    }
}
