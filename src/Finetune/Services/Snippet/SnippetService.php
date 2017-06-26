<?php

namespace Finetune\Finetune\Services\Snippet;

use Finetune\Finetune\Repositories\Snippet\SnippetInterface;
use Finetune\Finetune\Repositories\SnippetGroup\SnippetGroupInterface;
use Illuminate\Contracts\View\Factory as View;

class SnippetService {

    protected $snippets;
    protected $group;
    protected $view;

    /**
     * @param SnippetInterface $snippetRepo
     */
    public function __construct(SnippetInterface $snippets, SnippetGroupInterface $group)
    {
        $this->snippets = $snippets;
        $this->group = $group;
    }

    /**
     * @return mixed
     */
    public function getSnippet($site, $tag)
    {
        return $this->snippets->findSnippetByTag($site,$tag);
    }

    /**
     * @param $tag
     * @return mixed
     */
    public function getGroup($site, $tag)
    {
        return $this->group->findGroupByTag($site, $tag);
    }
    public function getGroups($site)
    {
        return $this->group->all($site);
    }
    public function getGroupList($site)
    {
        $groupArray = [];
        $groupArray[0] = 'All Groups';
        $groups = $this->getGroups($site);
        foreach($groups as $group){
            $groupArray[$group->id] = $group->title;
        }
        return $groupArray;
    }

    public function hasSnippets($site, $groupTag)
    {
        $group = $this->getGroup($site, $groupTag);
        if(!empty($group->snippets)){
            return false;
        }else{
            return true;
        }
    }

    public function renderGroup($site, $group)
    {

        $snippets = [];
        if (!empty($group)) {
            $groupObj = $this->getGroup($site, $group);
            if (!empty($groupObj)) {
                $snippets = $groupObj->snippet()->whereNull('deleted_at')->where('publish', '=', 1)->get();
            }
        }
        $view = '';
        if (!empty($snippets)) {
            $this->view = app('view');
            if ($this->view->exists($site->theme . '::snippets.group-' . $group)) {
                $view = $this->view->make($site->theme . "::snippets.group-" . $group, ['snippets' => $snippets, 'group' => $groupObj])->render();
            } else {
                if ($this->view->exists($site->theme . '::snippets.defaultSnippet')) {
                    $view = $this->view->make($site->theme . "::snippets.defaultGroup", ['snippets' => $snippets, 'group' => $groupObj])->render();
                } else {
                    $view = 'Snippet View File Not found';
                }
            }

        }
        return $view;
    }

    public function renderSnippet($site,$snippet)
    {
        if (!empty($snippet)) {
            $snippetArray = $this->getSnippet($site, $snippet);
        } else {
            $snippetArray = '';
        }
        if (!empty($snippetArray)) {

            if ($snippetArray->publish == 1) {
                $this->view = app('view');
                if ($this->view->exists($site->theme . '::snippets.' . $snippet)) {
                    return $this->view->make($site->theme . "::snippets." . $snippet, ['snippet' => $snippetArray])->render();
                } else {
                    if ($this->view->exists($site->theme . '::snippets.defaultSnippet')) {
                        return $this->view->make($site->theme . "::snippets.defaultSnippet", ['snippet' => $snippetArray])->render();
                    } else {
                        return 'Snippet View File Not found';
                    }
                }
            }
        }
        return '';
    }
}