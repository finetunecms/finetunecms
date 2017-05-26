<?php
namespace Finetune\Finetune\Controllers\Admin;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Repositories\SnippetGroup\SnippetGroupInterface;
use \Illuminate\Http\Request;

class SnippetGroupController extends BaseController
{

    protected $groups;
    protected $node;

    public function __construct(SiteInterface $site, Request $request, SnippetGroupInterface $groups)
    {
        parent::__construct($site, $request);
        $this->groups = $groups;
    }

    // Groups List
    public function index()
    {
        $route = $this->route;
        $site = $this->site;
        $packages = config('packages.group-list');
        $snippetGroups = $this->groups->all($this->site);
        return view('finetune::snippets.list', compact('route','site','adminTag', 'snippetGroups', 'packages'));
    }

    // Group snippet list
    public function show($id)
    {
        $route = $this->route;
        $site = $this->site;
        $packages = config('packages.snippet-list');
        $group = $this->groups->find($id);
        if (empty($group)) {
            abort('404');
        }
        return view('finetune::snippets.show', compact('route','site','group', 'packages'));
    }
}