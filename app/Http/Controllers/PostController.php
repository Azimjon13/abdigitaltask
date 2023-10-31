<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

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
        Validator::make($request->all(), [
            'photo' => ['required', 'file|size:5120', 'mimes:jpg,bmp,png'],
            'title' => ['required'],
            'body' => ['required'],
        ], [
            'required' => 'Поле :attribute является обязательным.'
        ])->validate();

        try{
            $data = Post::create([
                'photo' => $request->photo,
                'title' => $request->title,
                'body' => $request->body,
                'user_id' => $request->user()->id
            ]);
            return $this->responseSuccess($data, 'ABDigitalTask->Post: Создано успешно!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id){
        try {
            $data = Post::find($id)->load('author')->toResource();
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
        Validator::make($request->all(), [
            'photo' => ['required', 'file|size:5120', 'mimes:jpg,bmp,png'],
            'title' => ['required'],
            'body' => ['required'],
        ], [
        'required' => 'Поле :attribute является обязательным.'
        ])->validate();

        try{
            $data = Post::find($id)->update($request->all());
            return $this->responseSuccess($data, 'ABDigitalTask->Post: Успешно Обновлено!');
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
