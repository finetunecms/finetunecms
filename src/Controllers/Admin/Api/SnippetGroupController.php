<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Repositories\SnippetGroup\SnippetGroupInterface;
use Finetune\Finetune\Requests\Snippets\GroupRequest;
use \Illuminate\Http\Request as NormalRequest;
use \Illuminate\Translation\Translator;
use \Entrust;

class SnippetGroupController extends BaseController
{

    protected $groups;
    protected $lang;

    public function __construct(SiteInterface $site, NormalRequest $request, SnippetGroupInterface $groups, Translator $lang)
    {
        parent::__construct($site, $request);
        $this->groups = $groups;
        $this->lang = $lang;
    }

    public function index()
    {
        return response()->json($this->groups->all($this->site)->toArray());
    }

    public function show($id)
    {
        $snippet = $this->groups->find($id);
        return response()->json($snippet->snippet()->get()->toArray());
    }

    public function store(GroupRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_snippets'])) {
            $groups = $this->groups->create($this->site, $request->except('_token', '_method'));
            $array = [
                'groups' => $groups->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::snippets.notifications.group.created')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing snippets'], 403);
        }
    }

    public function update($id, GroupRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_snippets'])) {
            $groups = $this->groups->update($this->site, $id, $request->except('_token', '_method'));
            $array = [
                'groups' => $groups->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::snippets.notifications.group.updated')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing snippets'], 403);
        }
    }


    public function destroy(GroupRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_snippets'])) {
            $groups = $request->get('groups');
            foreach ($groups as $group) {
                $this->groups->delete($group['id']);
            }
            $array = [
                'groups' => $this->groups->all($this->site)->toArray(),
                'alertType' => 'warning',
                'alertMessage' => $this->lang->trans('finetune::snippets.notifications.deleted')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing snippets'], 403);
        }
    }
}