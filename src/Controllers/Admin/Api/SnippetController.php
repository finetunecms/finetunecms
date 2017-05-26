<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Requests\Snippets\SnippetRequest;
use Finetune\Finetune\Repositories\Snippet\SnippetInterface;
use Finetune\Finetune\Repositories\SnippetGroup\SnippetGroupInterface;
use \Illuminate\Http\Request as NormalRequest;
use \Illuminate\Translation\Translator;
use \Entrust;

class SnippetController extends BaseController
{

    protected $snippet;
    protected $group;
    protected $lang;

    public function __construct(SiteInterface $site, NormalRequest $request, SnippetInterface $snippet, SnippetGroupInterface $snippetGroup, Translator $lang)
    {
        parent::__construct($site, $request);
        $this->snippet = $snippet;
        $this->group = $snippetGroup;
        $this->lang = $lang;
    }

    public function index()
    {
        return Response()->json($this->snippet->all($this->site), 200);
    }

    public function show($id)
    {
        return Response()->json($this->snippet->find($id), 200);
    }

    public function store(SnippetRequest $snippetRequest)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_snippets'])) {
            $snippet = $this->snippet->create($this->site,$snippetRequest->except('_token', '_method'));
            $array = [
                'snippet' => $snippet->toArray(),
                'alertType' => 'success',
            ];
            if ($snippet->publish == 1) {
                $array['alertMessage'] = $this->lang->trans('finetune::snippets.notifications.createdPublished');
            } else {
                $array['alertMessage'] = $this->lang->trans('finetune::snippets.notifications.createdDraft');
            }
            return response()->json($array);
        } else {
            return response()->json(['No Permissions for managing snippets'], 403);
        }
    }

    public function update($id, SnippetRequest $snippetRequest)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_snippets'])) {
            $snippet = $this->snippet->update($this->site,$id, $snippetRequest->except('_token', '_method'));
            $array = [
                'snippet' => $snippet->toArray(),
                'alertType' => 'success',
            ];

            if ($snippet->publish == 1) {
                $array['alertMessage'] = $this->lang->trans('finetune::snippets.notifications.createdPublished');
            } else {
                $array['alertMessage'] = $this->lang->trans('finetune::snippets.notifications.createdDraft');
            }

            return response()->json($array);
        } else {
            return response()->json(['No Permissions for managing snippets'], 403);
        }
    }

    public function destroy(NormalRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_snippets'])) {
            $this->validate($request, [
                'snippets' => 'required',
                'group' => 'required',
            ]);
            $snippets = $request->get('snippets');
            foreach ($snippets as $snippet) {
                $this->snippet->delete($snippet['id']);
            }
            $group = $this->group->find($request->get('group'));
            $array = [
                'snippets' => $group->snippet->toArray(),
                'alertType' => 'warning',
                'alertMessage' => $this->lang->trans('finetune::snippets.notifications.deleted')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing snippets'], 403);
        }
    }

    public function saveOrder(NormalRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_snippets'])) {
            $this->validate($request, [
                'snippets' => 'required',
                'group' => 'required',
            ]);
            $snippets = $request->get('snippets');
            $group = $this->group->find($request->get('group'));
            $this->snippet->updateOrder($snippets);
            $array = [
                'snippets' => $group->snippet->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::snippets.notifications.order')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing snippets'], 403);
        }

    }

    public function publish(NormalRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_snippets'])) {
            $this->validate($request, [
                'snippet' => 'required',
                'group' => 'required',
            ]);
            $group = $this->group->find($request->get('group'));
            $snippet = $this->snippet->publish($request->get('snippet')['id']);
            $array = [
                'snippets' => $group->snippet->toArray(),
                'alertType' => 'success',
                'alertMessage' => (($snippet->publish == 1) ? $this->lang->trans('finetune::snippets.notifications.published') : $this->lang->trans('finetune::snippets.notifications.draft'))];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing snippets'], 403);
        }
    }
}