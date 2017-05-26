<?php namespace Finetune\Finetune\Requests;

class PasswordChange extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'key' => 'required|exists:password_reminders,token,deleted_at,NULL',
            'password'=>'required|between:6,12|confirmed',
            'password_confirmation'=>'required|between:6,12'
        ];
    }

}
