<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Requests\Node\NodeRequest;
use Finetune\Finetune\Repositories\Node\NodeInterface;
use \Illuminate\Http\Request as NormalRequest;
use \Illuminate\Translation\Translator;
use \Illuminate\Contracts\Auth\Guard as Auth;
use \Entrust;


class NodeController extends BaseController
{
    protected $node;
    protected $lang;
    protected $auth;

    public function __construct(NodeInterface $node, SiteInterface $site, NormalRequest $request, Translator $lang, Auth $auth)
    {
        parent::__construct($site, $request);
        $this->node = $node;
        $this->lang = $lang;
        $this->auth = $auth;
    }

    public function index()
    {
        return response()->json($this->node->all($this->site));
    }

    public function show($id)
    {
        if ($id == 'movable') {
            return response()->json($this->node->movable($this->site));
        }
        if ($id == 'links') {
            return response()->json($this->node->links($this->site));
        }
        if ($id == 'areas') {
            return response()->json($this->node->all($this->site,0, 1));
        } else {
            $node = $this->node->find($id);
            if (Entrust::ability([config('auth.superadminRole')], ['can_manage_allcontent'])) {
                if (config('finetune.nodeRoles')) {
                    $user = $this->auth->user();
                    $node->children = $node->children->each(function ($node, $key) use ($user) {
                        $checkEdit = $this->node->checkIfUserCanEdit($user, $node);
                        if ($checkEdit) {
                            $node->canEdit = true;
                        } else {
                            $node->canEdit = false;
                        }
                    });
                }else {
                    $node->children = $node->children->each(function ($node, $key) {
                        $node->canEdit = true;
                    });
                }
            } else {
                $node->children = $node->children->each(function ($node, $key) {
                    $node->canEdit = true;
                });
            }
            return response()->json($node);
        }
    }

    public function store(NodeRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_content'])) {
            $node = $this->node->create($this->site, $request->except('_token', '_method'));
            $array = [
                'node' => $node->toArray(),
                'alertType' => 'success'
            ];

            if ($node->publish == 1) {
                $array['alertMessage'] = $this->lang->trans('finetune::content.notifications.createdPublished');
            } else {
                $array['alertMessage'] = $this->lang->trans('finetune::content.notifications.createdDraft');
            }

            return response()->json($array);
        } else {
            return response()->json(['No Permissions for managing content'], 403);
        }
    }

    public function update($id, NodeRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_content'])) {
            $user = $this->auth->user();
            $node = $this->node->find($id);
            if (!empty($node)) {
                if (!$this->node->checkIfUserCanEdit($user, $node)) {
                    return response()->json(['No Permissions for managing content'], 403);
                }
                $node = $this->node->update($this->site, $id, $request->except('_token', '_method'));
                $array = [
                    'node' => $node->toArray(),
                    'alertType' => 'success',
                ];
                if ($node->publish == 1) {
                    $array['alertMessage'] = $this->lang->trans('finetune::content.notifications.updatePublished');
                } else {
                    $array['alertMessage'] = $this->lang->trans('finetune::content.notifications.updateDraft');
                }
                return response()->json($array);
            } else {
                return response()->json(['No content found'], 404);
            }
        } else {
            return response()->json(['No Permissions for managing content'], 403);
        }
    }

    public function destroy(NodeRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_content'])) {
            $nodes = $request->get('nodes');
            foreach ($nodes as $node) {
                $this->node->delete($this->site,$node['id']);
            }
            $parent = $request->get('parent');
            if ($parent == 0) {
                $nodes = $this->node->all($this->site,0, 1);
            } else {
                $nodes = $this->node->all($this->site,$parent);
            }
            $array = [
                'nodes' => $nodes->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::content.notifications.updated')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing content'], 403);
        }
    }

    public function saveOrder(NormalRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_content'])) {
            $this->validate($request, [
                'nodes' => 'required',
                'parent' => 'required'
            ]);
            $nodes = $request->get('nodes');
            $this->node->updateOrder($nodes);
            $parent = $request->get('parent');
            if ($parent == 0) {
                $nodes = $this->node->all($this->site,0, 1);
            } else {
                $nodes = $this->node->all($this->site,$parent);
            }
            $array = [
                'nodes' => $nodes->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::content.notifications.order')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing content'], 403);
        }

    }

    public function publish(NormalRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_content'])) {
            $this->validate($request, [
                'node' => 'required',
                'parent' => 'required'
            ]);
            $node = $this->node->publish($request->get('node')['id']);
            $parent = $request->get('parent');
            if ($parent == 0) {
                $nodes = $this->node->all($this->site,0, 1);
            } else {
                $nodes = $this->node->all($this->site,$parent);
            }
            $array = [
                'nodes' => $nodes->toArray(),
                'alertType' => 'success',
                'alertMessage' => (($node->publish == 1) ? $this->lang->trans('finetune::content.notifications.published') : $this->lang->trans('finetune::content.notifications.draft'))];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing content'], 403);
        }
    }

    public function move(NormalRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_content'])) {
            $this->validate($request, [
                'nodes' => 'required',
                'newparent' => 'required',
                'parent' => 'required'
            ]);

            $nodes = $request->get('nodes');
            $newParent = $request->get('newparent');

            $this->node->moveNodes($nodes, $newParent);

            $parent = $request->get('parent');
            if ($parent == 0) {
                $nodes = $this->node->all($this->site, 1);
            } else {
                $nodes = $this->node->all($this->site, $parent);
            }

            $array = [
                'nodes' => $nodes->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::content.notifications.moved')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing content'], 403);
        }
    }

    public function destroyOrphan($nodeId, $id)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_content'])) {
            $node = $this->node->find($nodeId);
            foreach ($node->blocks as $block) {
                if ($block->id == $id) {
                    $block->delete();
                }
            }
            return Response()->json($this->node->find($nodeId), 200);
        } else {
            return response()->json(['No Permissions for managing content'], 403);
        }
    }

    public function search(NormalRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_content'])) {
            $this->validate($request, [
                'searchterm' => 'required'
            ]);

            $nodes = $this->node->search($this->site, $request->get('searchterm'), $request->get('area'));
            $user = auth()->user();
                if (!$user->ability([config('auth.superadminRole')], ['can_manage_allcontent'])) {
                    if (config('finetune.nodeRoles')) {
                        $nodes = $nodes->each(function ($node, $key) use ($user) {
                            $checkEdit = $this->node->checkIfUserCanEdit($user, $node);
                            if ($checkEdit) {
                                $node->canEdit = true;
                            } else {
                                $node->canEdit = false;
                            }
                        });
                    }
                } else {
                    $nodes = $nodes->each(function ($node, $key) {
                        $node->canEdit = true;
                    });
                }
            $array = [
                'nodes' => $nodes,
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::content.notifications.searched')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing content'], 403);
        }
    }
}