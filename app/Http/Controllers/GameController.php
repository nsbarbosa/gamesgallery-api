<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use App\Repositories\GameRepository;

class GameController extends Controller
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
    
    public function create(Request $request)
    {
        $game = $this->game->create($request);

        return $this->success(compact('game'));
    }

    public function get($id)
    {
        $game = $this->game->findOrFail($id);

        return $this->success(compact('game'));
    }

    public function getAll()
    {
        $games = $this->game->getAll();

        return $this->success(compact('games'));
    }

    public function getByUser($id)
    {
        $games = $this->game->getByUser($id);
        
        return $this->success(compact('games'));
    }

    public function update(Request $request, $id)
    {
        $game = $this->game->updateGame($request->all(), $id);

        return $this->success(compact('game'));
    }

    public function delete($id) {
        $game = $this->game->deleteGame($id);

        return $this->success(compact('game'));
    }
}
