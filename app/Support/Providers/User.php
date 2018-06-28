<?php namespace App\Support\Providers;

use App\Models\UserActivation;
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
     * @var \App\Support\Providers\Person
     */
    private $person;

    public function __construct(PersonInterface $p)
    {
        parent::__construct();
        $this->hasher = new BcryptHasher();
        $this->person = $p;
    }

    /**
     * @param $data
     * @return \App\Models\User
     */
    public function createOne($data)
    {
        $user = $this->createModel([
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            'email' => $data['email']
        ]);
        $user->save();
        $this->person->createModel([
                'user_id' => $user->getKey(),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name']
            ]
        )->save();

        return $user->newQuery()->whereKey($user->getKey())->first();
    }


    /**
     * @param \App\Models\User $user
     * @return string
     */
    public function generateActivationToken($user)
    {
        $token = makeHexHashedUuid();
        (new UserActivation([
            'user_id' => $user->getKey(),
            'activation_token' => $token
        ]))->save();
        return $token;

    }

    /**
     * @param \App\Models\User $model
     * @param string $field
     * @param mixed $value
     * @param array $data
     * @return \App\Models\User
     */
    public function updateOne($model, $field, $value, $data)
    {
        $user = $model->newQuery()->select([
            'person_id',
        ])->where($field, $value)->entityType()->first();
        $this->person->createModel()->where('person_id',
            $user->person_id)->update($this->person->filterFillables($data));

        $model->newQueryWithoutScopes()->where($field, $value)
            ->update($this->filterFillables($data));

        if (isset($data[$field])) {
            return $model->newQuery()->select([
                'entity_type_id',
                'people.first_name',
                'people.last_name',
                'users.username',
                'users.email'
            ])->where($field, $data[$field])->entityType()->first();
        } else {
            return $user;
        }
    }

    /**
     * @param int $id
     * @param array $data
     * @return \App\Models\User
     */
    public function updateOneById($id, $data)
    {
        $model = $this->createModel();
        return $this->updateOne($model, $model->getKeyName(), $id, $data);
    }

    /**
     * @param string $username
     * @param array $data
     * @return \App\Models\User
     */
    public function updateOneByUsername($username, $data)
    {
        return $this->updateOne($this->createModel(), 'username', $username, $data);
    }

    /**
     * @param string|array $username
     */
    public function deleteByUsername($username)
    {
        if (is_array($username)) {
            $this->createModel()->newQueryWithoutScopes()
                ->whereIn('username', $username['users'])->delete();
        } else {
            $this->createModel()->newQueryWithoutScopes()
                ->where('username', '=', $username)->delete();
        }
    }

    /**
     * @param string $username
     * @param array $columns
     * @return \App\Models\User
     */
    public function buildOneByUsername($username, $columns = ['*'])
    {
        return $this->createModel()->newQuery()
            ->select($columns)->where('username', $username);
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

        return $model->newQuery()->entityType()
            ->where($model->getIdentifier($identifier), $identifier)
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

        $model = $model->newQuery()->entityType()
            ->where($model->getIdentifier(), $identifier)->first();

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

        $query = $this->createModel()->newQuery()->entityType();

        foreach ($credentials as $key => $value) {
            if (!Str::contains($key, 'password')) {
                $query->where($key, $value)->where('activated', '=', true);
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
     * @param string $search
     * @param int $userEntityId
     * @param int $limit
     * @return mixed
     */
    public function search($search, $userEntityId, $limit)
    {
        return $this->createModel()->newQuery()
            ->select(['full_name as text', 'username as id'])
            ->entityType()
            ->permissionRecord()
            ->permissionStore()
            ->permissionMask($userEntityId)
            ->where('full_name', 'like', sprintf('%%%s%%', $search))
            ->limit($limit);
    }

    public function getAvatars($userId)
    {
        return $this->createModel()->select([
            'media_uuid as uuid',
            'media_extension as ext',
            'media_in_use as used'
        ])->newQuery()
            ->entityType($userId)->avatars()->get()->toArray();
    }


    /**
     * @param string $token
     * @return int
     */
    public function activate($token)
    {
        $model = (new UserActivation);
        $user = $model->newQuery()
            ->select(['user_id'])
            ->where('activation_token', '=', $token)
            ->first();
        if (!is_null($user)) {
            $userId = $user->value('user_id');
            return $model->newQuery()->where('user_id', '=', $userId)->delete();
            //A mysql trigger sets the activated boolean on the users table whenever a delete occurs.
        }
        return 0;
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