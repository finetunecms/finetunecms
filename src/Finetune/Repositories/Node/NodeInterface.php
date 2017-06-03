<?php
namespace Finetune\Finetune\Repositories\Node;

interface NodeInterface
{

    public function all($site, $parent = 0, $area = 0, $frontend = false, $noEager = false);

    public function find($id, $frontend = false);

    public function findByTag($site, $tag, $area, $frontend = false);

    public function create($site, $request);

    public function update($site, $id, $request);

    public function delete($site,$id);

    public function slugBuilder($node);

    public function updateOrder($nodes);

    public function moveNodes($nodes, $parent);

    public function publish($id);

    public function eagerLoad($collection, $frontend = false, $itemWithType = null);

    public function makeBread($node);

    public function search($site, $searchTerm, $area = null, $frontend = false);

    public function filterContent($content);

    public function links($site);

    public function checkIfUserCanView($user, $node);

    public function checkIfUserCanEdit($user, $node);

    // Frontend Functions

    public function findBySlug($site, $slug);

    public function findHomepage($site);

    public function movable($site);

    public function findValue($node, $fieldTag);

    public function makeBreadFrontend($site, $node);

    public function frontEndSearch($site, $searchTerm);

    public function savePackages($site, $node, $packages);

}