<?php
namespace Finetune\Finetune\Requests\Permissions;

use Finetune\Finetune\Requests\Request;

class PermissionsRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->checkRole(['can_manage_permissions']);
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
                    'name' => 'required|unique:ft_permissions,name',
                    'display_name' => 'required'
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'name' => 'required|unique:ft_permissions,name,'.$this->route('permission'),
                    'display_name' => 'required'
                ];
            }
            default:
                break;
        }
    }

}
