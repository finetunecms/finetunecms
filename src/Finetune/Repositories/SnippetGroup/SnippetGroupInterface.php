<?php namespace Finetune\Finetune\Repositories\SnippetGroup;

interface SnippetGroupInterface
{
    public function all($site);

    public function find($id);

    public function create($site, $input);

    public function update($site, $id, $input);

    public function delete($id);

    public function findGroupByTag($site, $tag);

}