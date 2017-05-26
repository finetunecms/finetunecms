<?php
namespace Finetune\Finetune\Repositories\Type;

/**
 * Interface TypeInterface
 * @package Repositories\Type
 */
interface TypeInterface
{
    public function all();

    public function find($id);

    public function create($input);

    public function update($id, $input);

    public function destroy($id);

    public function getTypeList();

    public function findByTitle($tag);

    public function findDefault();
}