<?php
namespace Finetune\Finetune\Requests\Tagging;

use Finetune\Finetune\Requests\Request;

class TaggingRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->checkRole(['can_manage_tags']);
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
                    'title' => 'required|unique:ft_tags,title',
                    'tag' => 'unique:ft_tags,tag',
                ];
            }
            case 'PUT':
            case 'PATCH': {
                $id = $this->route('tag');
                return [
                    'title' => 'required|unique:ft_tags,title,' . $id,
                    'tag' => 'required|unique:ft_tags,tag,' . $id,
                ];
            }
            default:
                break;
        }
    }

}
