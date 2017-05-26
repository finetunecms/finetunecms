<?php

namespace Finetune\Finetune\Repositories\Tagging;

use Finetune\Finetune\Entities\Tagging;
use Finetune\Finetune\Repositories\Helper\HelperInterface;

class TaggingRepository implements TaggingInterface
{
    protected $helper;

    public function __construct(HelperInterface $helper)
    {
        $this->helper = $helper;
    }

    public function getAll($site)
    {
        return Tagging::where('site_id', '=', $site->id)->whereNull('deleted_at')->get();
    }

    public function find($id)
    {
        return Tagging::whereNull('deleted_at')->find($id);
    }

    public function getTagFromTag($tag)
    {
        return Tagging::whereNull('deleted_at')->where('tag', '=', $tag)->first();
    }

    public function getTagList($site)
    {
        return Tagging::where('site_id', '=', $site->id)->whereNull('deleted_at')->pluck('title', 'id');
    }

    public function getTagged($site, $tags = [], $areaId = null, $limit = null)
    {
        if (!is_array($tags)) {
            $category = $tags;
            $tags = [0 => $category];
        }
        $allTags = $this->getAll($site);
        $nodes = collect([]);
        foreach ($allTags as $singleTag) {
            if (in_array($singleTag->tag, $tags)) {
                $nodesObj = $singleTag->nodes()->get();
                foreach ($nodesObj as $object) {

                    if (!empty($areaId)) {
                        if ($object->area_fk == $areaId) {
                            $nodes->push($object);
                        }
                    } else {
                        $nodes->push($object);
                    }
                }
            }
        }
        if (!empty($limit)) {
            $nodes = $nodes->take($limit);
        }
        return $nodes;
    }

    public function addTag($site, $input)
    {
        $tag = new Tagging();
        if (isset($input['tag']) && !empty($input['tag'])) {
            $input['tag'] = $this->helper->buildTag($input['tag']);
        } else {
            $input['tag'] = $this->helper->buildTag($input['title']);
        }
        $input['site_id'] = $site->id;
        $tag->fill($input);
        $tag->save();
        return $tag->id;
    }

    public function deleteTagNode($tagId, $nodes)
    {
        $tag = $this->find($tagId);
        if (!empty($tag)) {
            $tag->nodes()->detach($nodes);
        }
    }

    public function updateTag($site, $id, $input)
    {
        $tag = $this->find($id);
        if (isset($input['tag']) && !empty($input['tag'])) {
            $input['tag'] = $this->helper->buildTag($input['tag']);
        } else {
            $input['tag'] = $this->helper->buildTag($input['title']);
        }
        $input['site_id'] = $site->id;
        $tag->fill($input);
        $tag->save();
    }

    public function deleteTag($id)
    {
        return Tagging::destroy($id);
    }
}