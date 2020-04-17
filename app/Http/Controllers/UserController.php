<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $request;
    protected $repo;
    // protected $upload;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     * 
     */
    public function __construct(Request $request, UserRepository $repo)
    {
        $this->request = $request;
        $this->repo = $repo;
    }

    /**
     * Used to get data of User
     * @get ("/api/user/{id}")
     * @param ({
     *      @Parameter("id", type="string", required="true", description="id of User")
     * })
     * @return Response
     */
    public function get()
    {
        $id = auth()->id();
        $user = $this->repo->findOrFail($id);
        return $this->success(compact('user'));
    }

    /**
     * Used to update User
     * @put ("/api/user/{id}")
     * @param ({
     *      @Parameter("id", type="string", required="true", description="id of User"),
     *      @Parameter("name", type="string", required="false", description="Name of User"),
     *      @Parameter("email", type="string", required="false", description="Email of User"),
     *      @Parameter("password", type="string", required="false", description="Password of User")
     * })
     * @return Response
     */
    public function update(Request $request)
    {
        $id = auth()->id();
        $user = $this->repo->findOrFail($id);
        $updated = $this->repo->update($user, $this->request->all());

        return $this->success(['message' => trans('user.updated'), 'user' => $updated]);
    }

    /**
     * Used to delete User
     * @delete ("/api/user/{id}")
     * @param ({
     *      @Parameter("id", type="string", required="true", description="id of User")
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $id = auth()->id();
        $user = $this->repo->findOrFail($id);

        $this->repo->delete($user);

        return $this->success(['message' => trans('user.deleted')]);
    }
}
