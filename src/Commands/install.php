<?php
namespace Finetune\Finetune\Commands;

use Finetune\Finetune\Entities\Site;
use Finetune\Finetune\Entities\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Install extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'finetune:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finetune install.';

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
        $databaseSeeder = new \Finetune\Finetune\Seeder\DatabaseSeeder();
        $databaseSeeder->run();
        $this->info("Making a new Superuser");
        $userUsername = $this->ask('Username');
        $userFirstname = $this->ask('firstname');
        $userLastname = $this->ask('lastname');
        $userEmail = $this->ask('Email');
        $password = $this->secret('Password');
        $this->info("Adding a Superuser");
        $user = new User();
        $user->username = $userUsername;
        $user->firstname = $userFirstname;
        $user->lastname = $userLastname;
        $user->email = $userEmail;
        $user->password = \Hash::make($password);  //TODO FIND A SERVICE PROVIDER / CONTRACT /LIBRARY
        $user->save();
        $user->attachRole(1);
        $this->info("Adding the new site");
        $this->info('Site details,everything below can be changed later, but cannot be left empty at this stage');
        $this->info("Dont include the protocol or www, you may include sub-domains");
        $siteDomain = $this->ask('domain');

        $this->info("The title of the new site");
        $siteTitle = $this->ask('title');
        $this->info("The dscpn of the new site");
        $siteDscpn = $this->ask('dscpn');
        $this->info("The tag of the new site, no spaces and usually one word, the theme will be also named after this tag and a folder created");
        $siteTag = $this->ask('tag');
        $siteCompanyName = $this->ask('Company Name');
        $siteCompanyPerson = $this->ask('Company Person');
        $siteCompanyEmail = $this->ask('Company Email');
        $siteCompanyStreet = $this->ask('Company Street');
        $siteCompanyTown = $this->ask('Company Town');
        $siteCompanyPostcode = $this->ask('Company Postcode');
        $siteCompanyTel = $this->ask('Company Tel');
        $siteCompanyRegion = $this->ask('Company Region');

        $site = new Site();
        $site->domain = $siteDomain;
        $site->title = $siteTitle;
        $site->dscpn = $siteDscpn;
        $site->keywords = implode(", ", explode(' ', $siteTitle));
        $site->theme = $siteTag;
        $site->company = $siteCompanyName;
        $site->person = $siteCompanyPerson;
        $site->email = $siteCompanyEmail;
        $site->street = $siteCompanyStreet;
        $site->town = $siteCompanyTown;
        $site->postcode = $siteCompanyPostcode;
        $site->tel = $siteCompanyTel;
        $site->region = $siteCompanyRegion;
        $site->tag = $siteTag;
        $site->key = $siteTag;
        $site->save();

        $user->sites()->attach($site->id);

        $this->info("Creating uploads folder");
        $fileSystem = new Filesystem();
        $path = storage_path().'/uploads';
        $fileSystem->makeDirectory($path, 0777, true, true);
        $fileSystem->makeDirectory($path.'/'.$site->tag, 0777, true, true);
        $fileSystem->makeDirectory($path.'/'.$site->tag.'/resized', 0777, true, true);
    }
}
