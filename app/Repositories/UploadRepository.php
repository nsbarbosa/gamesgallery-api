<?php
namespace App\Repositories;

use App\User;
use App\Image;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;

class UploadRepository
{
    protected $user;
    protected $image;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(User $user, Image $image)
    {
        $this->user = $user;
        $this->image = $image;
    }
    public function uploadImage($url,$user){
        $path = asset($url);
        if (file_exists($path))
            File::delete($path);
 
        $user->image = $path;
        $user->save();
        
        return $user->url;
    }

    public function uploadGallery($url, $game_id)
    {
        $path = asset($url);
        $this->image->name = $path;
        $this->image->game_id = $game_id;
        $this->image->save();

        return $this->image;
    }
}
