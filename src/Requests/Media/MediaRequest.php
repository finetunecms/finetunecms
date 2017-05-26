<?php
namespace Finetune\Finetune\Requests\Media;

use Finetune\Finetune\Requests\Request;

class MediaRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->checkRole(['can_manage_media']);
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
                return [];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'media' => 'required',
                ];
            }
            default:
                break;
        }
    }

}
