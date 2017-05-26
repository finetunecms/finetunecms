<?php
namespace Finetune\Finetune\Controllers\Admin;

use Finetune\Finetune\Repositories\Node\NodeInterface;
use Finetune\Finetune\Repositories\Packages\PackageInterface;
use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use \Illuminate\Http\Request;
use \Illuminate\Translation\Translator;

class ContentController extends BaseController
{
    private $node;
    private $package;
    private $lang;


    public function __construct(SiteInterface $site, Request $request, NodeInterface $node, PackageInterface $package, Translator $lang)
    {
        parent::__construct($site, $request);
        $this->node = $node;
        $this->package = $package;
        $this->lang = $lang;
    }

    public function index()
    {
        $route = $this->route;
        $site = $this->site;
        return view('finetune::content.list', compact('route', 'site'));
    }

    public function show($id)
    {
        $node = $this->node->find($id, true);
        if(empty($node)){
            abort('404');
        }
        if($node->type->nesting != 1){
            if($node->area != 1){
                abort('404');
            }else{
                if($node->type->date != 1){
                    abort('404');
                }
            }
        }
        $breadcrumbs = $this->node->makeBread($node);
        $route = $this->route;
        $site = $this->site;
        return view('finetune::content.show', compact('route','site', 'node', 'breadcrumbs'));
    }

    public function create()
    {
        $node = [];
        $url = '/admin/content/';
        $method = 'post';
        $breadcrumb =  [
            'admin' => $this->lang->trans('finetune::main.admin'),
            'admin/content' => $this->lang->trans('finetune::content.breadcrumb.content'),
            'create' => $this->lang->trans('finetune::content.breadcrumb.create')
        ];
        $route = $this->route;
        $site = $this->site;
        return view('finetune::content.update', compact('route','site','node', 'url', 'method','breadcrumb' ));
    }

    public function createChild($id)
    {
        $node = [];
        $url = '/admin/content';
        $method = 'POST';
        $parentNode = $this->node->find($id);
        $breadcrumb = $this->node->makeBread($parentNode);
        $breadcrumb['create'] = $this->lang->trans('finetune::content.breadcrumb.create');
        $route = $this->route;
        $site = $this->site;
        return view('finetune::content.update', compact('route','site', 'node','parentNode', 'url', 'method','breadcrumb'));
    }


    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $site = $this->site;
        $route = $this->route;
        $user = auth()->user();
        $node = $this->node->find($id);
        if(!$this->node->checkIfUserCanEdit($user, $node)){
            return redirect()->to('/admin/content');
        }
        $url = '/admin/content/'.$id;
        $method = 'PUT';
        $breadcrumb = $this->node->makeBread($node);
        array_pop($breadcrumb);
        $breadcrumb['update'] = $this->lang->trans('finetune::content.breadcrumb.update').' '. $node->title;
        return view('finetune::content.update', compact('route','site', 'node', 'url', 'method','breadcrumb'));
    }
}
