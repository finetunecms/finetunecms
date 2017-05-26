<?php
namespace Finetune\Finetune\Controllers;

use Finetune\Finetune\Entities\PasswordReminder;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Repositories\User\UserInterface;
use Finetune\Finetune\Entities\FailedLogins;
use Finetune\Finetune\Requests\PasswordReset;
use Finetune\Finetune\Requests\PasswordChange;
use Finetune\Finetune\Requests\LoginRequest;
use \Illuminate\Http\Request;
use \Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Contracts\Session\Session as Session;


/**
 * Class AuthController
 */
class AuthController extends BaseController
{
    protected $user;
    protected $auth;
    protected $session;

    public function __construct(SiteInterface $site, Request $request,UserInterface $user, Auth $auth, Session $session)
    {
        parent::__construct($site, $request);
        $this->user = $user;
        $this->auth = $auth;
        $this->session = $session;
    }

    public function index()
    {
        return view('finetune::auth.login');
    }

    public function show()
    {
        return view('finetune::auth.forgot');
    }

    public function getReset($key)
    {
        $user = PasswordReminder::where('token', '=', $key)->first();
        if (!empty($user)) {
            return view('finetune::auth.reset', compact('user', 'key'));
        } else {
            return Lang::get('passwords.token');
        }
    }

    public function postPasswordChange(PasswordChange $request)
    {
        $user = PasswordReminder::where('token', '=', $request->input('key'))->first();
        $this->user->updatePassword($user, $request);
        return redirect()->to('/admin')
            ->with('message', Lang::get('passwords.reset'))
            ->with('class', 'success');
    }

    public function postReset(PasswordReset $request)
    {
        $reset = $this->user->resetPassword($request);
        if ($reset) {
            return redirect()->back()
                ->with('message', Lang::get('passwords.sent'))
                ->with('class', 'success');
        } else {
            return redirect()->back()
                ->with('message', Lang::get('passwords.user'))
                ->with('class', 'danger');
        }
    }

    public function store(LoginRequest $request)
    {
        $userData = [];
        $identifier = $request->identity;
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $userData['email'] = $identifier;
        } else {
            $userData['username'] = $identifier;
        }
        $userData['password'] = $request->password;

        if($this->auth->attempt($userData)){
            if (empty($this->site)) {
                die('no site found, please insert it');
            }
            $userObj = $this->auth->user();
            $authorisedSites = $userObj->sites()->get();

            if($userObj->hasRole(config('auth.superadminRole'))){
                return redirect('/admin/content');
            }else{
                foreach ($authorisedSites as $authorised) {
                    if ($this->site->id == $authorised->id) {
                        return redirect('/admin/content');
                    }
                }
                return $this->flush();
            }

        } else {
            $failed = FailedLogins::where('ip', '=', $_SERVER['REMOTE_ADDR'])->first();
            if (empty($failed)) {
                $failed = new FailedLogins();
                $failed->ip = $_SERVER['REMOTE_ADDR'];
                $failed->failed_logins = 1;
                $failed->locked_out = 0;
                $failed->expire_time = \Carbon\Carbon::now()->addMinutes(5);
                $failed->last_attempt = \Carbon\Carbon::now();
                $failed->save();
            } else {
                $failed->failed_logins = $failed->failed_logins + 1;
                if ($failed->failed_logins >= 5) {
                    $failed->locked_out = 1;
                } else {
                    $failed->locked_out = 0;
                }
                $failed->expire_time = \Carbon\Carbon::now()->addMinutes(5);
                $failed->last_attempt = \Carbon\Carbon::now();
                $failed->save();
            }
            return redirect()->to('/auth')->with('login_errors', true);
        }
    }

    public function destroy()
    {
        $this->auth->logout();
        $this->session->flush();
        return redirect('/auth');
    }

    private function flush()
    {
        $this->auth->logout();
        $this->session->flush();
        $this->session->regenerate();
        return redirect('/auth');
    }

}