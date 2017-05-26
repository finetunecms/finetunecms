<?php
namespace Finetune\Finetune\Requests\Site;

use Finetune\Finetune\Requests\Request;

class SiteRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->checkRole(['can_manage_sites']);
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
                    'domain' => 'required|unique:ft_site',
                    'title' => 'required',
                    'dscpn' => 'required',
                    'keywords' => 'required',
                    'theme' => 'required',
                    'company' => 'required',
                    'person' => 'required',
                    'email' => 'required',
                    'tag' => 'required',
                    'key' => 'required'
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'domain' => 'required|unique:ft_site,domain,'.$this->route('site'),
                    'title' => 'required',
                    'dscpn' => 'required',
                    'keywords' => 'required',
                    'theme' => 'required',
                    'company' => 'required',
                    'person' => 'required',
                    'email' => 'required',
                    'tag' => 'required',
                    'key' => 'required'
                ];
            }
            default:
                break;
        }
    }

}
