<?php
namespace Finetune\Finetune\Requests\Type;

use Finetune\Finetune\Requests\Request;

class TypeRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->checkRole(['can_manage_types']);
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
                    'title' => 'required',
                    'outputs' => 'required',
                    'layout' => 'required',
                    'blocks' => 'name_validator',
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'title' => 'required',
                    'outputs' => 'required',
                    'layout' => 'required',
                    'blocks' => 'name_validator',
                ];
            }
            default:
                break;
        }
    }

}
