<?php namespace App\Http\Requests\Admin;

use App\Support\Requests\FormRequest;
use Naraki\Permission\Traits\ProcessesPermissions;

class UpdateUser extends FormRequest
{
    use ProcessesPermissions;

    public function rules()
    {
        return [
            'email' => 'email|unique:people,email',
            'username' => 'regex:/^\w+$/|min:5|max:25|unique:users,username'
        ];
    }

    public function afterValidation()
    {
        $input = $this->input();

        if (isset($input['permissions'])) {
            $this->processPermissions($input['permissions']);
        }

        unset($input['permissions']);
        $this->replace($input);
    }
}
