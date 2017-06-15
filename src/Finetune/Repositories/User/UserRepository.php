<?php
namespace Finetune\Finetune\Repositories\User;

use Finetune\Finetune\Entities\PasswordReminder;
use Finetune\Finetune\Entities\User;
use Illuminate\Contracts\Hashing\Hasher as Hash;
use Illuminate\Contracts\Mail\Mailer as Mail;

class UserRepository implements UserInterface
{
    protected $hash;
    protected $mail;
    public function __construct(Hash $hash, Mail $mail)
    {
        $this->hash = $hash;
        $this->mail = $mail;
    }

    public function all($withoutSuper = false, $site = null)
    {
        $users = User::with('roles', 'roles.perms', 'sites')->whereNull('deleted_at');

        if ($withoutSuper) {
            $users = $users->where("username", '!=', 'superadmin');
            $users = $users->whereHas('sites', function($query) use($site){
                $query->where('site_id', '=', $site->id);
            });
        }
        $users = $users->get();
        $usersArray = [];
        foreach ($users as $user) {
            $usersArray[] = $user;
        }
        return $users;
    }

    public function find($id, $notSuper = false, $site = null)
    {
        $user = User::with('roles', 'roles.perms', 'sites')
            ->whereNull('deleted_at');
        if($notSuper){
            $user = $user->whereHas('sites', function($query) use($site){
                $query->where('id', '=', $site->id);
            });
        }
        return $user->find($id);
    }

    public function create($request, $notSuper = false, $site = null)
    {
        $user = new User();
        $user->firstname = $request['firstname'];
        $user->lastname = $request['lastname'];
        $user->email = $request['email'];
        $user->username = $request['username'];
        if (isset($request['password'])) {
            $user->password = $this->hash->make($request['password']);
        }
        $user->save();
        $user->attachRole($request['roles']);

        $this->addSites($user,$request);
        return $this->all($notSuper, $site);
    }

    public function update($id, $request, $notSuper = false, $site = null)
    {
        $user = $this->find($id);
        $user->firstname = $request['firstname'];
        $user->lastname = $request['lastname'];
        $user->email = $request['email'];
        $user->username = $request['username'];
        if (isset($request['password'])) {
            $user->password = $this->hash->make($request['password']);
        }
        $user->save();
        $user->roles()->sync([]);
        $user->attachRole($request['roles']);
        $this->addSites($user,$request);
        return $this->all($notSuper, $site);
    }

    public function updatePassword($user, $input)
    {
        $user = User::where('email', '=', $user)->first();
        $user->password = $this->hash->make($input['password']);
        $user->save();
        PasswordReminder::where('email', '=', $user->email)->delete();
        return $user->id;
    }

    public function delete($id, $notSuper = false, $site = null)
    {
        $user = $this->find($id, $notSuper, $site);
        if (!empty($user)) {
            $user->delete();
        }
        return $this->all($notSuper, $site);

    }

    public function resetPassword($site, $request)
    {
        foreach ($this->all() as $user) {
            if ($user->email == $request->input('email')) {
                $reminder = new \Finetune\Finetune\Entities\PasswordReminder();
                $reminder->email = $request->input('email');
                $hashKey = $this->hash->make('password_reset_key');
                $key = hash_hmac('sha256', str_random(40), $hashKey);
                $reminder->token = $key;
                $reminder->created_at = \Carbon\Carbon::now();
                $reminder->save();
                $this->mail->send('finetune::emails.reset', ['key' => $key, 'user' => $user, 'site' => $site], function ($message) use ($user, $site) {
                    $message->to($user->email, $user->firstname . ' ' . $user->lastname)->subject('Password reset for finetune');
                });
                return true;
            }
        }
        return false;
    }

    private function addSites($user, $request){
            if (isset($request['sites'])) {
                $user->sites()->sync([]);
                foreach ($request['sites'] as $site) {
                    $user->sites()->attach($site['id']);
                }
            }
    }
}