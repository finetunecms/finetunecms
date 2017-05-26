<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Repositories\Type\TypeInterface;
use Finetune\Finetune\Requests\Type\TypeRequest;
use \Illuminate\Http\Request;
use \Illuminate\Translation\Translator;
use \Entrust;

class TypeController extends BaseController
{
    protected $type;
    protected $lang;

    public function __construct(SiteInterface $site, Request $request, TypeInterface $type, Translator $lang)
    {
        parent::__construct($site, $request);
        $this->type = $type;
        $this->lang = $lang;
    }

    public function index()
    {
        return Response()->json($this->type->all(), 200);
    }

    public function show($id)
    {
        return Response()->json($this->type->find($id)->fields()->get(), 200);
    }

    public function store(TypeRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_types'])) {
            $types = $this->type->create($request->except('_token'));
            $array = [
                'types' => $types->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::types.notifications.created')
            ];
            return response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing types'], 403);
        }
    }

    public function update($id, TypeRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_types'])) {

            $types = $this->type->update($id, $request->except('_token'));
            $array = [
                'types' => $types->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::types.notifications.updated')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing types'], 403);
        }
    }

    public function destroy(TypeRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_types'])) {

            $types = $request->get('types');
            foreach ($types as $type) {
                $this->type->destroy($type['id']);
            }
            $types = $this->type->all();
            $array = [
                'types' => $types->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::types.notifications.deleted')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing types'], 403);
        }
    }
}
