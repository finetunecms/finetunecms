<?php
namespace Finetune\Finetune\Commands;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Install extends Command {

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
    }
}