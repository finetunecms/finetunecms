<?php

namespace Finetune\Finetune\Services\Node;

use Finetune\Finetune\Repositories\Node\NodeInterface;


class NodeService
{
    protected $nodeRepo;

    public function __construct(NodeInterface $node)
    {
        $this->nodeRepo = $node;
    }

    public function all($site, $parent = 0, $area = 0)
    {
        return $this->nodeRepo->all($site, $parent, $area, true);
    }

    public function find($id)
    {
        return $this->nodeRepo->find($id);
    }

    public function findByTag($site, $tag, $area = 0)
    {
        return $this->nodeRepo->findByTag($site, $tag, $area, true);
    }

    public function childTags($tag, $area = 0, $tags = null)
    {
        $node = $this->nodeRepo->findByTag($tag, $area, true);
        $children = [];
        if (is_array($tags)) {
            foreach ($node->children as $child) {
                $added = false;
                foreach ($tags as $tag) {
                    $childTags = $child->tags()->get();

                    foreach ($childTags as $childTag) {
                        if ($childTag->tag == $tag) {
                            if (!$added) {
                                $children[] = $child;
                                $added = true;
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($node->children as $child) {
                $childTags = $child->tags()->get();
                foreach ($childTags as $childTag) {
                    if ($childTag->tag == $tags) {

                        $children[] = $child;
                    }
                }
            }
        }
        return collect($children);
    }

    public function findValue($node, $fieldTag)
    {
        return $this->nodeRepo->findValue($node, $fieldTag);
    }

    public function breadcrumbs($site, $node)
    {
        if (!isset($this->bread[$node->id])) {
            $this->bread[$node->id] = $this->nodeRepo->makeBreadFrontend($site, $node);
        }
        return $this->bread[$node->id];
    }

    public function areas($site, $ignore = null)
    {
        $areas = $this->all($site, 0, 1);
        if (is_array($ignore)) {
            foreach ($ignore as $ignoreItem) {
                foreach ($areas as $index => $area) {
                    if ($area->tag == $ignoreItem) {
                        $areas->forget($index);
                    }
                }
            }
        }
        return $areas;
    }

    public function frontEndSearch($site, $searchTerm)
    {
        return $this->nodeRepo->frontEndSearch($site, $searchTerm);
    }

    public function filterPublish($nodes, $year = '', $month = '', $day = '')
    {
        return $nodes->filter(function ($value, $key) use ($year, $month, $day) {
            $nodeDate = \Carbon\Carbon::parse($value->publish_on);
            if (!empty($year)) {
                if ($nodeDate->year == $year) {
                    if (!empty($month)) {
                        if ($nodeDate->month == $month) {
                            if (!empty($day)) {
                                if ($nodeDate->day == $day) {
                                    return true;
                                } else {
                                    return false;
                                }
                            } else {
                                return true;
                            }
                        } else {
                            return false;
                        }
                    } else {
                        return true;
                    }
                } else {
                    return false;
                }
            } else {
                return true;
            }
        });
    }

    public function canEditNode($node){
        $user = auth()->user();
        return $this->nodeRepo->checkIfUserCanEdit($user,$node);
    }

    public function canViewNode($node){
        $user = auth()->user();
        return $this->nodeRepo->checkIfUserCanView($user,$node);
    }

}