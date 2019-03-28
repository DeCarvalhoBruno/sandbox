<?php namespace App\Support\Providers;

use App\Contracts\Models\Person as PersonInterface;
use App\Contracts\Models\User as UserInterface;

use App\Models\Stats\StatUser;
use App\Models\User as UserModel;
use App\Models\UserActivation;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Str;
use Naraki\Oauth\Contracts\OauthProcessable as OauthProcessableInterface;
use Naraki\Oauth\Traits\OauthProcessable;
use Tymon\JWTAuth\JWTGuard;

/**
 * Class User
 * @see \Illuminate\Auth\EloquentUserProvider
 * @method \App\Models\User createModel(array $attributes = [])
 * @method \App\Models\User getOne($id, $columns = ['*'])
 */
class User extends Model implements UserProvider, UserInterface, OauthProcessableInterface
{
    use OauthProcessable, DispatchesJobs;

    protected $model = \App\Models\User::class;
    /**
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;
    /**
     * @var \App\Contracts\Models\Person|\App\Support\Providers\Person
     */
    private $person;

    /**
     *
     * @param \App\Contracts\Models\Person|\App\Support\Providers\Person $p
     */
    public function __construct(PersonInterface $p)
    {
        parent::__construct();
        $this->hasher = new BcryptHasher();
        $this->person = $p;
    }

    /**
     * @return \App\Contracts\Models\Person|\App\Support\Providers\Person
     */
    public function person()
    {
        return $this->person;

    }

    /**
     * @param $data
     * @return \App\Models\User
     */
    public function createOne($data)
    {
        $user = $this->createModel([
            'username' => $data['username'],
            'password' => bcrypt($data['password'])
        ]);
        $user->save();
        $this->person->createModel([
                'user_id' => $user->getKey(),
                'email' => $data['email'],
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
        try {
            $token = makeHexHashedUuid();
        } catch (\Exception $e) {
        }
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
        $user = $model->newQuery()->where($field, $value)->scopes(['entityType'])->first();

        $this->person->build()
            ->where('person_id', $user->person_id)
            ->update($this->person->filterFillables($data));

        $filteredData = $this->filterFillables($data);
        if (!empty($filteredData)) {
            $model->newQueryWithoutScopes()->where($field, $value)
                ->update($filteredData);
        }

        if (isset($data[$field])) {
            return $model->newQuery()->where($field, $data[$field])
                ->scopes(['entityType'])->first();
        } else {
            return $model->newQuery()->where($field, $value)->scopes(['entityType'])->first();
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
     * @throws \Exception
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildOneByUsername($username, $columns = ['*'])
    {
        return $this
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
        $builder = $model->newQuery()->scopes(['entityType'])
            ->where($model->getIdentifier($identifier), $identifier);
        if (auth()->guard() instanceof JWTGuard) {
            return $builder->scopes(['settings'])->first();
        }
        return $builder->first();
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

        $model = $model->newQuery()->scopes(['entityType'])
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
            return null;
        }

        $builder = $this->createModel()->newQuery()->scopes(['entityType']);
        if (auth()->guard() instanceof JWTGuard) {
            $builder = $builder->scopes(['settings']);
        }

        foreach ($credentials as $key => $value) {
            if (!Str::contains($key, 'password')) {
                $builder->where($key, $value)->where('activated', '=', true);
            }
        }

        return $builder->first();
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
        return $this
            ->buildWithScopes(
                ['full_name as text', 'username as id'],
                ['entityType', 'permissionRecord', 'permissionStore', 'permissionMask' => $userEntityId])
            ->where('full_name', 'like', sprintf('%%%s%%', $search))
            ->limit($limit);
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getAvatars($userId)
    {
        return $this->buildWithScopes(
            ['media_uuid as uuid', 'media_extension as ext', 'media_in_use as used'],
            ['entityType' => $userId, 'avatars' => false])
            ->get()->toArray();
    }


    /**
     * @param string $token
     * @return int
     */
    public function activate($token)
    {
        $model = new UserActivation;
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
     * @param \App\Models\User $user
     * @param array $values
     */
    public function updateStats(UserModel $user, $values)
    {
        if (isset($values['stat_user_timezone']) && is_numeric($values['stat_user_timezone'])) {
            StatUser::query()->where('user_id', $user->getKey())
                ->update(['stat_user_timezone' => intval($values['stat_user_timezone'])]);
        }
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