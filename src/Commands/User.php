<?php
namespace Finetune\Finetune\Commands;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Contracts\Hashing\Hasher as Hash;

class User extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'finetune:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a superuser to finetune.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->info("Making a new Superuser");
        $username = $this->ask('Username');
        $firstname = $this->ask('firstname');
        $lastname = $this->ask('lastname');
        $email = $this->ask('Email');
        $password = $this->secret('Password');
        $this->info("Adding a Superuser");
        $user = new User();
        $user->site_id = 0;
        $user->username = $username;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();
        $user->role()->attach(1);
    }
}