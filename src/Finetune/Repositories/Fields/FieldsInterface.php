<?php namespace Finetune\Finetune\Repositories\Fields;

/**
 * Interface FieldsInterface
 * @package Repositories\Fields
 */
interface FieldsInterface
{

    /**
     * @return mixed
     */
    public function all();

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param $request
     * @return mixed
     */
    public function create($request);

    /**
     * @param $id
     * @param $request
     * @return mixed
     */
    public function update($id, $request);

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id);

    /**
     * @param $typeId
     * @return mixed
     */
    public function getFieldsByType($typeId);

}