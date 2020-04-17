<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use App\Repositories\GameRepository;
use App\Http\Requests\ImageRequest;
// use Illuminate\Support\Str;

class UploadController extends Controller
{
    protected $user;
    protected $game;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $user, GameRepository $game)
    {
        $this->user = $user;
        $this->game = $game;

    }
    public function upload(ImageRequest $request){

        $user_id = Auth::id();
        $user = $this->user->findOrFail($user_id);

        $upload_path = 'uploads/' . $user->id;
        $extension = request()->image->getClientOriginalExtension();
        request()->image->move($upload_path, 'image.'.$extension);
        $url = '/'.$upload_path.'/image.'.$extension;
        $image = $this->user->uploadImage($user,$url);

        return $this->success(compact('url','image'));
    }

    public function uploadGallery(ImageRequest $request){

        $user_id = Auth::id();
        $game = $this->game->findOrFail(request()->game_id);
        $name_image = str_random(30);
        $upload_path = 'uploads/gallery/' . $game->id;
        $extension = request()->image->getClientOriginalExtension();
        request()->image->move($upload_path, $name_image.'.'.$extension);
        $url = '/'.$upload_path.'/'.$name_image.'.'.$extension;

        $image = $this->game->uploadImage($url, $game->id);

        return $this->success(compact('url','image'));
    }

    public function deleteImageGallery($id) {
        $image = $this->game->deleteImage($id);

        return $this->success(compact('image'));
    }
}
