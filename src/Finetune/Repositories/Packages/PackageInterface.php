<?php namespace Finetune\Finetune\Repositories\Packages;

/**
 * Interface PackageInterface
 * @package Repositories\Package
 */
interface PackageInterface
{
    public function find($site, $type, $node = null);
}