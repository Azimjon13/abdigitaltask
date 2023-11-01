<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use function Symfony\Component\Translation\t;

class PostController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index(Request $request){
       try {
            $per_page = isset($request->per_page) ? intval($request->per_page) : 10;
            $keyword = isset($request->search) ? $request->search : null;
            $data = new PostCollection(Post::with('author')->when($keyword, function ($query) use ($keyword){
                $query->where('title', 'like', '%'.$keyword.'%');
            })->paginate($per_page));
            return $this->responseSuccessPaginate($data, 'ABDigitalTask->Posts: Загружено успешно!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function posts_by_user(Request $request){
        try {
            $keyword = isset($request->search) ? $request->search : null;
            $data = User::with(['posts' => function($query) use ($keyword){
                $query->when($keyword, function ($query) use ($keyword){
                    $query->where('title', 'like', '%'.$keyword.'%');
                });
            }])->first()->toResource();
            return $this->responseSuccess($data, 'ABDigitalTask->Posts: Загружено успешно!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){

        $validator = Validator::make($request->only('photo', 'title', 'body'), [
            'photo' => ['required', File::types(['jpg', 'png', 'jpeg'])->max('5mb')],
            'title' => ['required'],
            'body' => ['required'],
        ], [
            'required' => 'Поле :attribute является обязательным.',
            'photo.file' => 'Поле :attribute должно быть файлом.',
            'photo.mimes' => 'Поле :attribute должно быть файлом типа: jpg, png, jpeg.',
            'photo.max' => 'Поле :attribute не должно превышать 5MB.'
        ]);

        if ($validator->fails()) {
            return $this->responseError(null, $validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        try{
            $path = 'not_uploaded';
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $path = $request->file('photo')->storeAs(
                    'images',  $request->file('photo')->hashName(), 'public'
                );
            }
            $data = Post::create([
                'photo' => $path,
                'title' => $request->title,
                'body' => $request->body,
                'user_id' => $request->user()->id
            ]);
            return $this->responseSuccess($data->toResource(), 'ABDigitalTask->Post: Создано успешно!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id){
        try {
            $data = Post::findOrFail($id)->load('author')->toResource();
            return $this->responseSuccess($data, 'ABDigitalTask->Posts: Загружено успешно!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id){
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id){
        $validator = Validator::make($request->only( 'title', 'body'), [
            'title' => ['required'],
            'body' => ['required'],
        ], [
            'required' => 'Поле :attribute является обязательным.'
        ]);

        if ($validator->fails()) {
            return $this->responseError(null, $validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        try{
            $post = Post::findOrFail($id);
            $post->title = $request->title;
            $post->body = $request->body;
            $post->save();
            return $this->responseSuccess($post->toResource(), 'ABDigitalTask->Post: Успешно Обновлено!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update_photo(Request $request, string $id){

        $validator = Validator::make($request->only('photo'), [
            'photo' => ['required', File::types(['jpg', 'png', 'jpeg'])->max('5mb')]
        ], [
            'required' => 'Поле :attribute является обязательным.',
            'photo.file' => 'Поле :attribute должно быть файлом.',
            'photo.mimes' => 'Поле :attribute должно быть файлом типа: jpg, png, jpeg.',
            'photo.max' => 'Поле :attribute не должно превышать 5MB.'
        ]);

        if ($validator->fails()) {
            return $this->responseError(null, $validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        try{
            $post = Post::find($id);
            $path = 'not_uploaded';
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                if (!is_null($post->photo)) {
                    Storage::disk('public')->delete($post->photo);
                }
                $path = $request->file('photo')->storeAs(
                    'images',  $request->file('photo')->hashName(), 'public'
                );
            }
            $post->photo = $path;
            $post->save();
            return $this->responseSuccess($post->toResource(), 'ABDigitalTask->Post: Успешно Обновлено!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id){
        try {
            return $this->responseSuccess(Post::destroy($id), 'ABDigitalTask->Post: Мягкое удаление! Удаленный идентификатор - '.$id);
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
