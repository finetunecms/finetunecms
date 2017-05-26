<?php
namespace Finetune\Finetune\Requests\User;

use Finetune\Finetune\Requests\Request;

class UserRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->checkRole(['can_manage_users']);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch($this->method()) {
            case 'GET':
            case 'DELETE': {
                return [];
            }
            case 'POST': {
                return [
                    'firstname'=>'required|min:2',
                    'lastname'=>'required|min:2',
                    'email'=>'required|email|unique:ft_users',
                    'username'=>'required|unique:ft_users',
                    'password'=>'required|between:6,12|confirmed',
                    'password_confirmation'=>'required|between:6,12',
                    'roles' => 'required'
                ];
            }
            case 'PUT':
            case 'PATCH': {
            $user = $this->route('user');
                return [
                    'firstname'=>'required|min:2',
                    'lastname'=>'required|min:2',
                    'email'=>'required|email|unique:ft_users,email,'.$user,
                    'username'=>'required|unique:ft_users,username,'.$user,
                    'roles'=>'required'
                ];
            }
            default:
                return [];
                break;
        }
    }
}
