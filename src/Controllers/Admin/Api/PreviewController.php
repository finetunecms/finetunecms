<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Node\NodeInterface;
use Finetune\Finetune\Repositories\Render\RenderRepository;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use \Illuminate\Http\Request as NormalRequest;
use \Illuminate\Contracts\View\Factory as View;

class PreviewController  extends BaseController
{
    protected $node;
    protected $render;
    protected $view;

    public function __construct(SiteInterface $site, NormalRequest $request,NodeInterface $node, RenderRepository $render, View $view)
    {
        parent::__construct($site, $request);
        $this->node = $node;
        $this->render = $render;
        $this->view = $view;
    }

    public function update($id, NormalRequest $request){
        $nodePost = $request->all();
        $node = $this->node->find($id);
        $node->body = $nodePost['body'];
        $node->title = $nodePost['title'];

        foreach($node->blocks as $key => $block){
            foreach($nodePost['blocks'] as $uKey => $uBlock)
            if($block->name == $uBlock['name']){
                $node->blocks[$key]->content = $uBlock['content'];
            }
        }


        $this->view->addNamespace($this->site->theme, public_path() . '/themes/' . $this->site->theme);

        return $this->render->renderPage($this->site, $node, $request, $node->url_slug);
    }
}