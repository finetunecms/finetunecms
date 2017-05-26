<?php
namespace Finetune\Finetune\Seeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class NodeTableSeeder extends Seeder {

    public function run()
    {
        DB::table('ft_node')->insert(
            array(
                array(
                    'id' => 1,
                    'site_id' => 1,
                    'type_id' => 1,
                    'author_id' => 1,
                    'area_fk' => 0,
                    'locked' => 0,
                    'area' => 1,
                    'parent' => 0,
                    'order' => 1,
                    'exclude' => 0,
                    'publish' => 1,
                    'soft_publish' => 0,
                    'homepage' => 1,
                    'tag' => 'home',
                    'url_slug' => '/home',
                    'title' => 'Home',
                    'dscpn' => 'homepage',
                    'keywords' => 'home',
                    'body' => 'This is a homepage',
                    'image' => '',
                    'redirect' => '',
                    'meta_title' => 'Homepage',
                    'publish_on' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
                )
            )
        );
    }
}
