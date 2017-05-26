<?php
namespace Finetune\Finetune\Requests\Node;

use Finetune\Finetune\Requests\Request;

class NodeRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->checkRole(['can_manage_content']);
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
                    'body' => 'required',
                    'title' => 'required',
                    'type' => 'required',
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'title' => 'required',
                    'body' => 'required',
                    'type' => 'required'
                ];
            }
            default:
                break;
        }
    }

}
