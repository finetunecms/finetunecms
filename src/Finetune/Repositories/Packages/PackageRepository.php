<?php
namespace Finetune\Finetune\Repositories\Packages;

use \Config;
use \View;

class PackageRepository implements PackageInterface
{
    public function find($site, $area, $node = null){
        $packages = config('packages.'.$area);
        $jsonArray = [];
        foreach($packages as $indexPackage => $package){
            $jsonArray[$package['name']] = [
                'fields' => [],
                'values' => []
            ];
            foreach($package['fields'] as $field){
                $jsonArray[$package['name']]['fields'][] = $field;
            }
            if(isset($node)){
                foreach($packages as $indexPackage => $package){
                    $class = resolve($package['class']);
                    $values = $class->{$package['values']}($site, $node);
                    foreach($package['fields'] as $index => $field){
                        foreach($values as $value){
                            if($value['name'] == $field['name']){
                                $packages[$indexPackage]['fields'][$index]['value'] = $value['value'];
                            }
                        }
                    }
                }
            }
        }
        return $packages;
    }
}
