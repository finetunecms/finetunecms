<?php
namespace Finetune\Finetune\Requests\Media;

use Finetune\Finetune\Requests\Request;

class FolderRequest extends Request {

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
                return [
                    'tag' => 'required',
                    'title' => 'required',
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'id' => 'required',
                    'tag' => 'required',
                    'title' => 'required',
                ];
            }
            default:
                break;
        }
    }

}
