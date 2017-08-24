<?php

namespace Finetune\Finetune\Repositories\Fields;

use Finetune\Finetune\Entities\Fields;
use \Illuminate\Support\Facades\Response;
use Finetune\Finetune\Repositories\Helper\HelperInterface;

/**
 * Class FieldsRepository
 * @package Finetune\Finetune\Repositories\Fields
 */
class FieldsRepository implements FieldsInterface
{
    protected $helper;

    public function __construct(HelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return Fields::all();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return Fields::find($id);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function create($request)
    {
        $field = new Fields();
        if (empty($request['name'])) {
            $request['name'] = $this->helper->buildTag($request['label']);
        } else {
            $request['name'] = $this->helper->buildTag($request['name']);
        }
        $field->fill($request);
        $field->save();
        $typeId = $request['type_id'];
        return $this->getFieldsByType($typeId);
    }

    /**
     * @param $id
     * @param $request
     * @return mixed
     */
    public function update($id, $request)
    {
        $field = $this->find($id);
        if (empty($request['name'])) {
            $request['name'] = $this->helper->buildTag($request['label']);
        } else {
            $request['name'] = $this->helper->buildTag($request['name']);
        }
        $field->fill($request);
        $field->save();
        $typeId = $request['type_id'];
        return $this->getFieldsByType($typeId);
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function destroy($id)
    {
        $field = $this->find($id);
        if (!empty($field)) {
            $typeId = $field->type_id;
            $field->delete();
            return $this->getFieldsByType($typeId);
        } else {
            return [];
        }
    }

    /**
     * @param $typeId
     * @return mixed
     */
    public function getFieldsByType($typeId)
    {
        $fields = Fields::where('type_id', '=', $typeId)
            ->whereNull('deleted_at')
            ->get();

        foreach($fields as &$field){
            if($field->type == 'icons'){
                $field->icons = config('fields.'.$field->name);
            }
        }
        return $fields;
    }
}