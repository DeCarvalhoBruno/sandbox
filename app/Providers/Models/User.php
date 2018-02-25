<?php namespace App\Providers\Models;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Support\Str;
use App\Contracts\Models\User as UserInterface;
use App\Contracts\Models\Person as PersonInterface;

/**
 * Class User
 * @see \Illuminate\Auth\EloquentUserProvider
 * @method \App\Models\User createModel(array $attributes = [])
 * @method \App\Models\User getOne($id, $columns = ['*'])
 */
class User extends Model implements UserProvider, UserInterface
{
    protected $model = \App\Models\User::class;
    /**
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;
    /**
     * @var \App\Providers\Models\Person
     */
    private $person;

    public function __construct(PersonInterface $p)
    {
        parent::__construct();
        $this->hasher = new BcryptHasher();
        $this->person = $p;
    }

    /**
     * @param \App\Models\User $model
     * @param string $field
     * @param mixed $value
     * @param array $data
     * @return int
     */
    public function updateOneUser($model, $field, $value, $data)
    {
        $user = $this->createModel()->newQuery()->select('person_id')->where($field, $value)->first();
        $this->person->createModel()->where('person_id', $user->person_id)->update($this->person->filterFillables($data));

        return $model->newQueryWithoutScopes()->where($field, $value)
            ->update($this->filterFillables($data));
    }

    /**
     * @param int $id
     * @param array $data
     * @return int
     */
    public function updateOneById($id, $data)
    {
        $model = $this->createModel();
        return $this->updateOneUser($model, $model->getKeyName(), $id, $data);
    }

    /**
     * @param string $username
     * @param array $data
     * @return int
     */
    public function updateOneByUsername($username, $data)
    {
        return $this->updateOneUser($this->createModel(), 'username', $username, $data);
    }

    /**
     * @param string $username
     * @param array $columns
     * @return \App\Models\User
     */
    public function getOneByUsername($username, $columns = ['*'])
    {
        return $this->createModel()->newQuery()->select($columns)->where('username', $username);
    }


    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        return $model->newQueryWithoutScopes()
            ->select(['users.user_id', 'users.username', 'entity_type_id'])
            ->entityType($identifier)
            ->where($model->getQualifiedKeyName(), $identifier)
            ->first();
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();

        $model = $model->newQueryWithoutScopes()->select([
            'users.user_id',
            'username',
            'remember_token'
        ])->where($model->getQualifiedKeyName(), $identifier)->first();

        if (!$model) {
            return null;
        }

        $rememberToken = $model->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token) ? $model : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token)
    {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
                array_key_exists('password', $credentials))) {
            return;
        }

        $query = $this->createModel()->newQueryWithoutScopes()->select(['users.user_id', 'password', 'username']);

        foreach ($credentials as $key => $value) {
            if (!Str::contains($key, 'password')) {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    /**
     * Gets the hasher implementation.
     *
     * @return \Illuminate\Contracts\Hashing\Hasher
     */
    public function getHasher()
    {
        return $this->hasher;
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->createModel()->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}