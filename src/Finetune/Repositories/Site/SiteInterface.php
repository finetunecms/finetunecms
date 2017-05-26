<?php namespace Finetune\Finetune\Repositories\Site;

/**
 * Interface SiteInterface
 * @package Repositories\Site
 */
interface SiteInterface
{
    public function all();

    public function find($id);

    public function create($request);

    public function update($id, $request);

    public function destroy($id);

    public function findSiteByDomain($domain);

    public function first();

    public function getSite($request);

}