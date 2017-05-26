<?php namespace Finetune\Finetune\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Illuminate\Contracts\Auth\Guard as Auth;

abstract class Request extends FormRequest {

    public $auth;
    public $user;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        $this->user = $this->auth->user();
    }

    public function response(array $errors)
    {
        if (parent::ajax() || parent::wantsJson())
        {
            return new JsonResponse($errors, 422);
        }

        $modal_id = $this->request->get('modal_id');

        if(!empty($modal_id)){
            return $this->redirector->to(parent::getRedirectUrl())
                ->withInput(parent::except($this->dontFlash))
                ->withErrors($errors, $this->errorBag)
                ->with('modal_id',$modal_id);
        }else{
            return $this->redirector->to(parent::getRedirectUrl())
                ->withInput(parent::except($this->dontFlash))
                ->withErrors($errors, $this->errorBag);
        }
    }

    public function checkRole($role){
        if($this->user->hasRole('Superadmin')){
            return true;
        }else{
            return $this->user->can($role);
        }
    }


}
