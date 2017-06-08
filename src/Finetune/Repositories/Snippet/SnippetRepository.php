<?php

namespace Finetune\Finetune\Repositories\Snippet;

use Finetune\Finetune\Entities\Snippet;
use Finetune\Finetune\Repositories\Helper\HelperInterface;
use \Illuminate\Contracts\Auth\Guard as Auth;

class SnippetRepository implements SnippetInterface
{

    protected $helper;
    protected $auth;

    public function __construct(HelperInterface $helper, Auth $auth)
    {
        $this->helper = $helper;
        $this->auth = $auth;
    }


    public function all($site)
    {
        return Snippet::with('media', 'snippet_groups', 'node')
            ->whereNull('deleted_at')
            ->where('site_id', '=', $site->id)
            ->orderBy('order', 'asc')
            ->get();
    }

    public function find($id)
    {
        return Snippet::with('media', 'snippet_groups', 'node')->where('id', '=', $id)->first();
    }

    public function create($site, $input)
    {
        $snippet = new Snippet();
        if (empty($input['tag'])) {
            $input['tag'] = $input['title'];
        }
        $input['body'] = str_replace('@snippet(' . $input['tag'] . ')', 'Error - Snippet Referenced it self', $input['body']);
        $input['tag'] = $this->helper->buildTag($input['tag']);
        $input['tag'] = $this->_buildTag($site, $input);
        $input['site_id'] = $site->id;
        $input['author_id'] = $this->auth->user()->id;
        $input['image'] = (isset($input['media']['id']) ? $input['media']['id'] : null);
        if ($input['link_type'] == 1) {
            $input['link_internal'] = $input['link_internal']['id'];
            $input['link_external'] = null;
        }
        if ($input['link_type'] == 2) {
            $input['link_internal'] = null;

        }
        $snippet->fill($input);
        $snippet->save();
        return $this->find($snippet->id);
    }

    public function update($site, $id, $input)
    {
        $snippet = $this->find($id);
        unset($snippet->linkid);
        if (empty($input['tag'])) {
            $input['tag'] = $input['title'];
        }
        $input['tag'] = $this->helper->buildTag($input['tag']);
        $input['tag'] = $this->_buildTag($site, $input, $id);
        $input['body'] = str_replace('@snippet(' . $input['tag'] . ')', 'Error - Snippet Referenced it self', $input['body']);
        $input['site_id'] = $site->id;
        $input['image'] = (isset($input['media']['id']) ? $input['media']['id'] : null);
        if ($input['link_type'] == 1) {
            $input['link_internal'] = $input['link_internal']['id'];
            $input['link_external'] = null;
        }
        if ($input['link_type'] == 2) {
            $input['link_internal'] = null;
        }
        $snippet->fill($input);
        $snippet->save();

        return $this->find($id);
    }

    public function delete($id)
    {
        $snippet = $this->find($id);
        if (!empty($snippet)) {
            Snippet::destroy($id);
            return $snippet->group;
        } else {
            return [];
        }
    }

    public function findSnippetByTag($site, $tag)
    {
        $snippets = $this->all($site);
        foreach ($snippets as $snippet) {
            if ($snippet->tag == $tag) {
                return $snippet;
            }
        }
        return [];
    }

    public function updateOrder($snippets)
    {
        foreach ($snippets as $order => $snippet) {
            Snippet::where('id', '=', $snippet['id'])->update(
                [
                    'order' => $order,
                ]
            );
        }
    }

    public function publish($id)
    {
        $snippet = Snippet::with('media', 'snippet_groups')->whereNull('deleted_at')->find($id);
        $snippet->publish = ($snippet->publish == 1 ? 0 : 1);
        $snippet->save();
        return $snippet;
    }

    private function _buildTag($site, $input, $id = null, $i = 1)
    {
        if (!empty($id)) {
            $tagExists = Snippet::with('media', 'snippet_groups')->where('group_id', '=', $input['group_id'])
                ->where('tag', '=', $input['tag'])
                ->where('id', '<>', $id)
                ->where('site_id', '=', $site->id)
                ->whereNull('deleted_at')
                ->get();
        } else {
            $tagExists = Snippet::with('media', 'snippet_groups')->where('group_id', '=', $input['group_id'])
                ->where('tag', '=', $input['tag'])
                ->where('site_id', '=', $site->id)
                ->whereNull('deleted_at')
                ->get();
        }
        if (!$tagExists->isEmpty()) {
            $i++;
            $input['tag'] = $input['tag'] . '-' . $i;
            $this->_buildTag($site, $input, $i);
        }
        return $input['tag'];
    }
}