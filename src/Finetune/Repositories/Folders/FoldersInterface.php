<?php namespace Finetune\Finetune\Repositories\Folders;

/**
 * Interface FoldersInterface
 * @package Repositories\Folders
 */
interface FoldersInterface
{
    public function all($site);

    public function find($id);

    public function create($site, $request);

    public function update($site, $id, $request);

    public function destroy($site, $id);

}