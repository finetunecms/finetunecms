<?php
namespace Finetune\Finetune\Controllers\Admin;

use Finetune\Finetune\Repositories\Node\NodeInterface;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Repositories\SnippetGroup\SnippetGroupInterface;
use Finetune\Finetune\Repositories\Snippet\SnippetInterface;
use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Requests\Snippets\SnippetRequest;
use \Illuminate\Http\Request;


class SnippetController extends BaseController
{
    protected $snippet;
    protected $group;
    protected $node;

    public function __construct(SiteInterface $site, Request $request, SnippetInterface $snippet, SnippetGroupInterface $group,  NodeInterface $node)
    {
        parent::__construct($site, $request);
        $this->snippet = $snippet;
        $this->group = $group;
        $this->node = $node;
    }

    public function create($groupId)
    {
        $route = $this->route;
        $site = $this->site;
        $packages = config('packages.snippet-update');
        $group = $this->group->find($groupId);
        if (empty($group)) {
            abort('404');
        }
        $snippet = [];
        $url = '/admin/snippets/store';
        $method = 'post';
        return view('finetune::snippets.update', compact('route','site','group', 'snippet', 'method', 'url', 'packages'));
    }

    public function store(SnippetRequest $request)
    {
        $snippet = $this->snippet->create($this->site, $request->except('_token', '_method'));
        $group = $snippet->snippet_groups;
        return redirect('/admin/snippets/' . $group->id . '/snippet/' . $snippet->id . '/edit')->with(array('message' => trans('finetune::snippets.notifications.created'), 'class' => 'success'));
    }


    public function edit($groupId, $id)
    {
        $route = $this->route;
        $site = $this->site;
        $packages = config('packages.snippet-update');
        $snippet = $this->snippet->find($id);
        if (empty($snippet)) {
            abort('404');
        }
        $group = $snippet->snippet_groups;
        $url = '/admin/snippets/' . $group->id . '/snippet/' . $id;
        $method = 'PUT';
        return view('finetune::snippets.update', compact('route','site','snippet', 'group', 'method', 'url', 'packages'));
    }

    public function update($id, SnippetRequest $request)
    {
        $snippet = $this->snippet->update($this->site,$id, $request->except('_token', '_method'));
        $group = $snippet->snippet_groups;
        return redirect('/admin/snippets/' . $group->id . '/snippet/' . $snippet->id . '/edit')->with(array('message' => trans('finetune::snippets.notifications.updated'), 'class' => 'success'));
    }
}