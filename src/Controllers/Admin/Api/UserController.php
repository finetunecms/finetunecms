<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Repositories\User\UserInterface;
use Finetune\Finetune\Requests\User\UserRequest;
use \Illuminate\Http\Request;
use \Illuminate\Translation\Translator;
use \Entrust;

class UserController extends BaseController
{
    protected $user;
    protected $lang;

    public function __construct(SiteInterface $site, Request $request, UserInterface $user, Translator $lang)
    {
        parent::__construct($site, $request);
        $this->user = $user;
        $this->lang = $lang;
    }

    public function index()
    {
        if (Entrust::hasRole(config('auth.superadminRole'))) {
            return response()->json($this->user->all(), 200);
        }else{
            return response()->json($this->user->all(true, $this->site), 200);
        }

    }

    public function show($id){
        if (Entrust::hasRole(config('auth.superadminRole'))) {
            return response()->json($this->user->find($id), 200);
        }else{
            return response()->json($this->user->find($id, true, $this->site), 200);
        }

    }

    public function store(UserRequest $request)
    {
        if (Entrust::hasRole(config('auth.superadminRole'))) {
            $users = $this->user->create($request->except('_token'));
        } else{
            $users = $this->user->create($request->except('_token'), true, $this->site);
        }
        $array = [
            'users'=> $users->toArray(),
            'alertType'=> 'success',
            'alertMessage' => $this->lang->trans('finetune::users.notifications.created')
        ];
        return Response()->json($array, 200);
    }

    public function update($id, UserRequest $request)
    {
        if (Entrust::hasRole(config('auth.superadminRole'))) {
            $users = $this->user->update($id, $request->except('_token'));
        } else{
            $users = $this->user->update($id, $request->except('_token'), true, $this->site);
        }

        $array = [
            'users'=> $users->toArray(),
            'alertType'=> 'success',
            'alertMessage' => $this->lang->trans('finetune::users.notifications.updated')
        ];
        return Response()->json($array, 200);
    }

    public function destroy(UserRequest $request)
    {
        if (Entrust::hasRole(config('auth.superadminRole'))) {
            $users = $request->get('users');
            foreach ($users as $user) {
                $this->user->delete($user['id']);
            }
        } else{
            $users = $request->get('users');
            foreach ($users as $user) {
                $this->user->delete($user['id'], true, $this->site);
            }
        }

        $users = $this->user->all();
        $array = [
            'users'=> $users->toArray(),
            'alertType'=> 'success',
            'alertMessage' => $this->lang->trans('finetune::users.notifications.updated')
        ];
        return Response()->json($array, 200);
    }
}
