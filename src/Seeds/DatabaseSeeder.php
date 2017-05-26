<?php
namespace Finetune\Finetune\Seeder;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{

    /**
     * @var array
     */
    protected $seeders = [
        'Finetune\Finetune\Seeder\RoleTableSeeder',
        'Finetune\Finetune\Seeder\NodeTableSeeder',
        'Finetune\Finetune\Seeder\TypeTableSeeder'
    ];

    /**
     * @var array
     */
    protected $tables = [
        'ft_assigned_roles',
        'ft_assigned_sites',
        'ft_blocks',
        'ft_failedlogins',
        'ft_fields',
        'ft_folders',
        'ft_media',
        'ft_media_folders',
        'ft_node',
        'ft_node_errors',
        'ft_node_roles',
        'ft_node_tags',
        'ft_password_reminders',
        'ft_permission_role',
        'ft_permissions',
        'ft_roles',
        'ft_site',
        'ft_snippet_groups',
        'ft_snippets',
        'ft_tags',
        'ft_type',
        'ft_users',
        'ft_values'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        $this->cleanDatabase();
        foreach ($this->seeders as $seedClass) {
            $this->call($seedClass);
        }
    }

    /**
     * Clean Database reader for seeding
     */
    private function cleanDatabase()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($this->tables as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
