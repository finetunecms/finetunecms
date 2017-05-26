<?php
namespace Finetune\Finetune\Seeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeTableSeeder extends Seeder {

    public function run()
    {
        DB::table('ft_type')->insert(
            array(
                array(
                    'id' => 1,
                    'title' => 'Home',
                    'outputs' => 'display',
                    'layout' => 'home',
                    'blocks' => 'page:aside',
                    'order_by' => null,
                    'nesting' => 0,
                    'date' => 0,
                    'today_future' => 0,
                    'today_past' => 0,
                    'pagination' => 0,
                    'pagination_limit' => 10,
                    'access' => 0,
                    'rss' => 0
                ),
                array(
                    'id' => 2,
                    'title' => 'Page',
                    'outputs' => 'display',
                    'layout' => 'page',
                    'blocks' => 'page:aside',
                    'order_by' => null,
                    'nesting' => 1,
                    'date' => 0,
                    'today_future' => 0,
                    'today_past' => 0,
                    'pagination' => 0,
                    'pagination_limit' => 10,
                    'access' => 0,
                    'rss' => 0
                )
            )
        );
    }
}
