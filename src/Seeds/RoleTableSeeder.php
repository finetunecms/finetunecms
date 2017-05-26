<?php
namespace Finetune\Finetune\Seeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder {
 
    public function run()
    {

        $adminPerms = [];
 		// create the superadmin role
        $superadmin = new \Finetune\Finetune\Entities\Role;
		$superadmin->name = config('auth.superadminRole');
        $superadmin->usable = 0;
        $superadmin->deleteable = 0;
		$superadmin->save();

		// create the admin role
		$admin = new \Finetune\Finetune\Entities\Role;
		$admin->name = 'admin';
        $admin->usable = 1;
        $admin->site_id = 1;
        $admin->parent_id = 0;
        $admin->deleteable = 0;
		$admin->save();

		$manageAll = new \Finetune\Finetune\Entities\Permission;
		$manageAll->name = 'manage_all';
		$manageAll->display_name = 'Manage Everything';
        $manageAll->usable = 0;
        $manageAll->deleteable = 0;
		$manageAll->save();

        $canAdminister = new \Finetune\Finetune\Entities\Permission;
        $canAdminister->name = 'can_administer_website';
        $canAdminister->display_name = 'Can login into the admin system on the website';
        $canAdminister->usable = 1;
        $canAdminister->deleteable = 0;
        $canAdminister->save();
        $adminPerms[] = $canAdminister->id;

        // Users

        $manageUsers = new \Finetune\Finetune\Entities\Permission;
        $manageUsers->name = 'can_manage_users';
        $manageUsers->display_name = 'Can Manage Users';
        $manageUsers->usable = 1;
        $manageUsers->deleteable = 0;
        $manageUsers->save();
        $adminPerms[] = $manageUsers->id;


        // Nodes

        $manageContent = new \Finetune\Finetune\Entities\Permission;
        $manageContent->name = 'can_manage_content';
        $manageContent->display_name = 'Can Manage Content';
        $manageContent->usable = 1;
        $manageContent->deleteable = 0;
        $manageContent->save();
        $adminPerms[] = $manageContent->id;

        $manageContent = new \Finetune\Finetune\Entities\Permission;
        $manageContent->name = 'can_manage_nodes';
        $manageContent->display_name = 'Can Manage nodes';
        $manageContent->usable = 1;
        $manageContent->deleteable = 0;
        $manageContent->save();
        $adminPerms[] = $manageContent->id;

        $manageContent = new \Finetune\Finetune\Entities\Permission;
        $manageContent->name = 'advanced_user';
        $manageContent->display_name = 'Advanced User';
        $manageContent->usable = 1;
        $manageContent->deleteable = 0;
        $manageContent->save();
        $adminPerms[] = $manageContent->id;

        // Snippets

        $manageMedia = new \Finetune\Finetune\Entities\Permission;
        $manageMedia->name = 'can_manage_snippets';
        $manageMedia->display_name = 'Can Manage Snippets';
        $manageMedia->usable = 1;
        $manageMedia->deleteable = 0;
        $manageMedia->save();
        $adminPerms[] = $manageMedia->id;

        $manageMedia = new \Finetune\Finetune\Entities\Permission;
        $manageMedia->name = 'can_manage_media';
        $manageMedia->display_name = 'Can Manage Media';
        $manageMedia->usable = 1;
        $manageMedia->deleteable = 0;
        $manageMedia->save();
        $adminPerms[] = $manageMedia->id;

        $publishNodes = new \Finetune\Finetune\Entities\Permission;
        $publishNodes->name = 'can_publish';
        $publishNodes->display_name = 'Can Publish';
        $publishNodes->usable = 1;
        $publishNodes->deleteable = 0;
        $publishNodes->save();
        $adminPerms[] = $publishNodes->id;

        $deleteNodes = new \Finetune\Finetune\Entities\Permission;
        $deleteNodes->name = 'can_delete';
        $deleteNodes->display_name = 'Can Delete';
        $deleteNodes->usable = 1;
        $deleteNodes->deleteable = 0;
        $deleteNodes->save();
        $adminPerms[] = $deleteNodes->id;

        $manageTags = new \Finetune\Finetune\Entities\Permission;
        $manageTags->name = 'can_manage_tags';
        $manageTags->display_name = 'Can Manage Tags';
        $manageTags->usable = 1;
        $manageTags->deleteable = 0;
        $manageTags->save();
        $adminPerms[] = $manageTags->id;

        $managePlugins = new \Finetune\Finetune\Entities\Permission;
        $managePlugins->name = 'can_manage_plugins';
        $managePlugins->display_name = 'Can Manage Plugins';
        $managePlugins->usable = 1;
        $managePlugins->deleteable = 0;
        $managePlugins->save();
        $adminPerms[] = $managePlugins->id;

        $managePlugins = new \Finetune\Finetune\Entities\Permission;
        $managePlugins->name = 'can_manage_sites';
        $managePlugins->display_name = 'Can Manage Sites';
        $managePlugins->usable = 1;
        $managePlugins->deleteable = 0;
        $managePlugins->save();
        $adminPerms[] = $managePlugins->id;

        // asign premissons to the roles
		$superadmin->perms()->sync(array($manageAll->id, $canAdminister->id));
		$admin->perms()->sync($adminPerms);

    }

}