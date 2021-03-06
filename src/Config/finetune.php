<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Website Protocol
    |--------------------------------------------------------------------------
    |
    |
    */
    'protocol' => 'http://',
    /*
    |--------------------------------------------------------------------------
    | Date
    |--------------------------------------------------------------------------
    |
    |
    */
    'date' => 'j F Y',

    /*
    |--------------------------------------------------------------------------
    | Finetune Version
    |--------------------------------------------------------------------------
    |
    |
    */
    'name' => 'Finetune CMS 5',

    /*
    |--------------------------------------------------------------------------
    | Node Roles
    |--------------------------------------------------------------------------
    |
    |
    */
    'nodeRoles' => true,

    /*
    |--------------------------------------------------------------------------
    | Redirects
    |--------------------------------------------------------------------------
    |
    |
    */
    'redirects' => false,

    /*
    |--------------------------------------------------------------------------
    | Finetune Theme
    |--------------------------------------------------------------------------
    |
    |
    */
    'admingui' => 'finetune',

    /*
   |--------------------------------------------------------------------------
   | Cache Finetune
   |--------------------------------------------------------------------------
   |
   |
   */
    'cache' => false, // Page frontend cache

    'nodecache' => false, // node list cache

    /*
     |--------------------------------------------------------------------------
     | Search View
     |--------------------------------------------------------------------------
     | Add to index using this command :  php artisan scout:import "Finetune\Finetune\Entities\Node"
     |
     */

    'searchView' => 'search.display',
    'searchItems' => 10,
    'predictiveItems' => '10',


    /*
     * Allow for group unpublish
     */

    'groupunpublish' => true,



    'mobile' => true,
    'mobileSize' => 500,

];