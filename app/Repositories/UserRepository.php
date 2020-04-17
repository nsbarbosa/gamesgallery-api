<?php
namespace App\Repositories;

use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserRepository
{
    protected $user;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user  = $user;
    }

    /**
     * Create a new user.
     *
     * @param array $params
     */

    public function create($params)
    {
        $user = $this->user->forceCreate($this->formatCreate($params));
        return $user;
    }


    /**
     * Find user by Id
     *
     * @param integer $id
     * @return User
     */

    public function findOrFail($id = null)
    {
        $user = $this->user->find($id);

        if (! $user) {
            throw ValidationException::withMessages(['message' => trans('user.could_not_find')]);
        }

        return $user;
    }

    /**
     * Find user by Email
     *
     * @param email $email
     * @return User
     */

    public function findByEmail($email = null)
    {
        return $this->user->filterByEmail($email)->first();
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */

    private function formatCreate($params)
    {
        $formatted = [
            'email' => isset($params['email']) ? $params['email'] : null,
            'password' => isset($params['password']) ? bcrypt($params['password']) : null,
            'status' => 'activated',
            'name' => isset($params['name']) ? $params['name'] : null,
            'image' => isset($params['image']) ? $params['image'] : null,
        ];

        return $formatted;
    }

   /**
     * Update given user.
     *
     * @param User $user
     * @param array $params
     * @return User
     */
    public function update(User $user, $params)
    {
        $formatted = $this->formatUpdate($user, $params);
        $user->name = $formatted['name'];
        $user->email = $formatted['email'];
        $user->password = $formatted['password'];
        $user->image = $formatted['image'];
        $user->save();
        return $params;
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param User
     * @param array $params
     * @return array
     */


    private function formatUpdate($user, $params)
    {
        $formatted = [
            'email' => isset($params['email']) ? $params['email'] : $user->email,
            'password' => isset($params['password']) ? bcrypt($params['password']) : $user->password,
            'name' => isset($params['name']) ? $params['name'] : $user->name,
            'image' => isset($params['image']) ? $params['image'] : $user->image,
        ];


        return $formatted;
    }

    /**
     * Update given user status.
     *
     * @param User $user
     * @param string $status
     *
     * @return User
     */
    public function status(User $user, $status = null)
    {
        if (!in_array($status, ['activated','pending_activation','pending_approval','banned','disapproved'])) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        $user->status = $status;
        $user->save();

        return $user;
    }

    /**
     * Force reset user password.
     *
     * @param User $user
     * @param string $password
     *
     * @return User
     */
    public function forceResetPassword(User $user, $password = null)
    {
        $user->password = bcrypt($password);
        $user->save();

        return $user;
    }

    /**
     * Delete user.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(User $user)
    {
        return $user->delete();
    }

    public function uploadImage(User $user, $url){
        $path = asset($url);
        if (file_exists($path))
            File::delete($path);
 
        $user->image = $path;
        $user->save();
        
        return $user->image;
    }
}