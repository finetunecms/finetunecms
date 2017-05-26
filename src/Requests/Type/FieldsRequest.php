<?php
namespace Finetune\Finetune\Requests\Type;

use Finetune\Finetune\Requests\Request;

class FieldsRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->checkRole(['can_manage_fields']);
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
                    'label' => 'required',
                    'name' => 'required',
                    'type' => 'required',
                    'type_id' => 'required'
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'label' => 'required',
                    'name' => 'required',
                    'type' => 'required',
                    'type_id' => 'required'
                ];
            }
            default:
                break;
        }
    }

}
