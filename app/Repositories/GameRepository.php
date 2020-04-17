<?php
namespace App\Repositories;

use App\Game;
use App\User;
use App\Image;
use App\Repositories\UploadRepository;

class GameRepository {

    protected $game;
    protected $user;
    protected $upload;
    protected $image;

    public function __construct(Game $game, User $user, UploadRepository $upload, Image $image)
    {
        $this->game  = $game;
        $this->user  = $user;
        $this->upload  = $upload;
        $this->image  = $image;
    }

    public function create($params)
    {
        $this->game->name = $params->name;
        $this->game->user_id = $params->user_id;
        $this->game->rating = $params->rating;
        $this->game->description = $params->description;
        $this->game->save();

        return $this->game;
    }

    public function findOrFail($id = null)
    {
        $game = $this->game->with('images')->find($id);

        if (! $game) {
            throw ValidationException::withMessages(['message' => 'Jogo não encontrado']);
        }

        return $game;
    }

    public function getAll()
    {
        $games = $this->game->with('images','user')->get();

        return $games;
    }

    public function getByUser($id)
    {
        $games = $this->user->with('games','games.images')->findOrFail($id);

        return $games;
    }

    public function uploadImage($url, $game_id)
    {
        $image = $this->upload->uploadGallery($url, $game_id);

        return $image;
    }

    public function updateGame($request, $id)
    {
        $game = $this->game->find($id);
        $game->name = isset($request['name']) ? $request['name'] : $game->name;
        $game->description = isset($request['description']) ? $request['description'] : $game->description;
        $game->rating = isset($request['rating']) ? $request['rating'] : $game->rating;
        $game->save();

        return $game;
    }

    public function deleteImage($id)
    {
        $image = $this->image->find($id);
        //$path = asset($image->name);

        if (file_exists($image->name))
            File::delete($image->name);

        $image = $image->delete();

        return $image;
    }

    public function deleteGame($id)
    {
        $game = $this->game->find($id);
        $delete = $game->delete();

        return $delete;
    }
}