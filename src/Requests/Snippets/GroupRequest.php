<?php
namespace Finetune\Finetune\Requests\Snippets;

use Finetune\Finetune\Requests\Request;

class GroupRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->checkRole(['can_manage_snippets']);
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
                    'tag' => 'unique:ft_snippet_groups,tag',
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'title' => 'required',
                    'tag' => 'unique:ft_snippet_groups,tag,' . $this->route('snippetgroup'),
                ];
            }
            default:
                break;
        }
    }

}
