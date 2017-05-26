<?php
namespace Finetune\Finetune\Requests\Role;

use Finetune\Finetune\Requests\Request;

class RoleRequest extends Request {

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
                    'name' => 'required|unique:ft_roles'
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'name' => 'required|unique:ft_roles,name,'.$this->route('role')
                ];
            }
            default:
                break;
        }
    }

}
