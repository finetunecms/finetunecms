<?php

namespace Finetune\Finetune\Repositories\SnippetGroup;

use Finetune\Finetune\Entities\SnippetGroups;
use Finetune\Finetune\Repositories\Helper\HelperInterface;

/**
 * Class SnippetRepository
 * @package Repositories\Snippet
 */
class SnippetGroupRepository implements SnippetGroupInterface
{
    protected $helper;

    public function __construct(HelperInterface $helper)
    {
        $this->helper = $helper;
    }


    public function all($site)
    {
        return SnippetGroups::with('snippet', 'snippet.media')
            ->whereNull('deleted_at')
            ->where('site_id', '=', $site->id)
            ->get();
    }

    public function find($id)
    {
        return SnippetGroups::whereNull('deleted_at')
            ->find($id);
    }


    public function create($site, $input)
    {
        $input['site_id'] = $site->id;
        if (empty($input['tag'])) {
            $input['tag'] = $this->helper->buildTag($input['title']);
        } else {
            $input['tag'] = $this->helper->buildTag($input['tag']);
        }
        if (empty($input['dscpn'])) {
            $input['dscpn'] = $this->helper->buildTag($input['title']);
        }
        $input['publish'] = (isset($input['publish']) ? 1 : 0);
        $group = new SnippetGroups();
        $group->fill($input);
        $group->save();
        return $this->all($site);
    }


    public function update($site, $id, $input)
    {
        $input['site_id'] = $site->id;
        $input['tag'] = $this->helper->buildTag($input['tag']);
        $input['publish'] = (isset($input['publish']) ? 1 : 0);
        $group = $this->find($id);
        $group->fill($input);
        $group->save();
        return $this->all($site);
    }

    public function delete($id)
    {
        $group = $this->find($id);
        if (!empty($group)) {
            $snippets = $group->snippet;
            SnippetGroups::destroy($id);
            if (!$snippets->isEmpty()) {
                foreach ($snippets as $snippet) {
                    $snippet->delete();
                }
            }
        }
    }

    public function findGroupByTag($site, $tag)
    {
        return SnippetGroups::with('snippet', 'snippet.media')
            ->whereNull('deleted_at')
            ->where('site_id', '=', $site->id)
            ->where('tag', '=', $tag)
            ->first();
    }
}