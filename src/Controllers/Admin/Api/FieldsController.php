<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Fields\FieldsInterface;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Requests\Type\FieldsRequest;
use \Illuminate\Http\Request as Request;
use \Illuminate\Translation\Translator;
use \Entrust;

class FieldsController extends BaseController
{
    protected $field;
    protected $lang;

    public function __construct(SiteInterface $site, Request $request, FieldsInterface $field, Translator $lang)
    {
        parent::__construct($site, $request);
        $this->field = $field;
        $this->lang = $lang;
    }

    public function index()
    {
        return Response()->json($this->field->all(), 200);
    }

    public function show($id)
    {
        return Response()->json($this->field->getFieldsByType($id), 200);
    }

    public function store(FieldsRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_types'])) {
            $fields = $this->field->create($request->except('_token'));
            $array = [
                'fields' => $fields->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::fields.notifications.created')
            ];
            return response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing types'], 403);
        }
    }

    public function update($id, FieldsRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_types'])) {
            $fields = $this->field->update($id, $request->except('_token'));
            $array = [
                'fields' => $fields->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::fields.notifications.updated')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing types'], 403);
        }
    }

    public function destroy(FieldsRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_types'])) {
            $fields = $request->get('fields');
            foreach ($fields as $field) {
                $fieldsObj = $this->field->destroy($field['id']);
            }
            $array = [
                'fields' => $fieldsObj->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::fields.notifications.deleted')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing types'], 403);
        }
    }
}
