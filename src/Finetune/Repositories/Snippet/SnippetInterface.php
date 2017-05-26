<?php namespace Finetune\Finetune\Repositories\Snippet;


interface SnippetInterface
{
    public function all($site);

    public function find($id);

    public function create($site, $input);

    public function update($site, $id, $input);

    public function delete($id);

    public function findSnippetByTag($site, $tag);

    public function updateOrder($snippets);

    public function publish($id);

}