<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Repositories\Tagging\TaggingInterface;
use Finetune\Finetune\Requests\Tagging\TaggingRequest;
use \Illuminate\Http\Request;
use \Illuminate\Translation\Translator;
use \Entrust;

class TaggingController extends BaseController
{
    protected $tagging;
    protected $lang;

    public function __construct(SiteInterface $site, Request $request, TaggingInterface $tagging, Translator $lang)
    {
        parent::__construct($site, $request);
        $this->tagging = $tagging;
        $this->lang = $lang;
    }

    public function index()
    {
        return response()->json($this->tagging->getAll($this->site)->toArray());
    }

    public function show($id)
    {
        $tag = $this->tagging->find($id);
        return response()->json($tag->nodes()->get()->toArray());
    }

    public function store(TaggingRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_tags'])) {
            $this->tagging->addTag($this->site,$request->except('_token', '_method'));
            $tags = $this->tagging->getAll($this->site);
            $array = [
                'tags' => $tags->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::tags.notifications.created')
            ];
            return response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing tagging'], 403);
        }
    }

    public function update($id, TaggingRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_tags'])) {
            $this->tagging->updateTag($this->site,$id, $request->except('_token', '_method'));
            $tags = $this->tagging->getAll($this->site);
            $array = [
                'tags' => $tags->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::tags.notifications.updated')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing tagging'], 403);
        }
    }

    public function destroy($id, Request $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_tags'])) {
            if ($id == 'node-delete') {
                $nodes = $request->get('nodes');
                $tagId = $request->get('tagId');
                $tag = $this->tagging->find($tagId);
                $this->tagging->deleteTagNode($tagId, $nodes);
                $array = [
                    'nodes' => $tag->nodes()->get()->toArray(),
                    'alertType' => 'warning',
                    'alertMessage' => $this->lang->trans('finetune::tags.notifications.tagged.deleted')
                ];
                return Response()->json($array, 200);
            } else {
                $tags = $request->get('tags');
                foreach ($tags as $tag) {
                    $this->tagging->deleteTag($tag['id']);
                }
                $array = [
                    'tags' => $this->tagging->getAll($this->site)->toArray(),
                    'alertType' => 'warning',
                    'alertMessage' => $this->lang->trans('finetune::tags.notifications.deleted')
                ];
                return Response()->json($array, 200);
            }
        } else {
            return response()->json(['No Permissions for managing tagging'], 403);
        }

    }
}
