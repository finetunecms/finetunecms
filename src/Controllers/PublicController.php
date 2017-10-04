<?php
namespace Finetune\Finetune\Controllers;

use Finetune\Finetune\Repositories\Node\NodeInterface;
use Finetune\Finetune\Repositories\Render\RenderInterface;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use \App\Http\Controllers\Controller;
use Finetune\Finetune\Repositories\Tagging\TaggingInterface;
use \Illuminate\Http\Request;
use \Illuminate\Contracts\View\Factory as View;
use \Illuminate\Contracts\Mail\Mailer as Mail;

class PublicController extends BaseController
{
    protected $node;
    protected $render;
    protected $siteInterface;
    protected $view;
    protected $mail;
    protected $validation;
    protected $tagging;

    public function __construct(NodeInterface $node, RenderInterface $render, SiteInterface $siteInterface, View $view, Mail $mail, Request $request, TaggingInterface $tagging)
    {
        parent::__construct($siteInterface, $request);
        $this->node = $node;
        $this->render = $render;
        $this->siteInterface = $siteInterface;
        $this->view = $view;
        $this->mail = $mail;
        $this->tagging = $tagging;
    }

    public function index(Request $request)
    {
        return $this->render->buildFinetune($this->site, $request);
    }

    public function search(Request $request)
    {
        return $this->render->search($this->site, $request);
    }

    public function email($form, Request $request)
    {
        $this->view->addNamespace($this->site->theme, public_path() . '/themes/' . $this->site->theme);
        $validation = config('forms.' . $form . '.validation');
        $layout = config('forms.' . $form . '.layout');

        if (config('forms.' . $form . '.email')) {
            $contactDefault = config('forms.' . $form . '.email');
        } else {
            $contactDefault = $this->site->email;
        }

        $contact = $request->input('contact', $contactDefault);

        $success = config('forms.' . $form . '.success');

        $this->validate($request, $validation);

        if (!empty($contact)) {
            $this->mail->send($this->site->theme . '::emails.' . $layout, ['request' => $request->all(), 'contact' => $contact, 'site' => $this->site], function ($message) use ($contact) {
                $message->to($contact, 'Website Email')->subject('Website Message');
            });
        }
        return redirect()->back()->with(['message' => $success, 'class' => 'success']);

    }

    public function jsonNodes()
    {
        $nodes = $this->node->getAll($this->site->id);
        $nodeArray = [
            [
                'name' => 'select an internal link....',
                'url' => false
            ]
        ];
        foreach ($nodes as $node) {
            if ($node->publish == 1) {
                $nodeArray[] = [
                    'name' => $node->title,
                    'url' => $node->url_slug
                ];
            }
        }
        return response()->json($nodeArray);
    }

    public function sitemap()
    {
        return $this->render->sitemap($this->site);
    }

    public function testStyles()
    {
        $this->view->addNamespace($this->site->theme, public_path() . '/themes/' . $this->site->theme);
        $site = $this->site;
        return view($this->site->theme . '::style-test', compact('site'));
    }

    public function catagory($cat){
        $nodes = $this->tagging->getTagged($this->site, $cat, null, true);
        $packages = config('packages.tagged');
        $items = [];
        if (!empty($packages)) {
            foreach ($packages as $package) {
                $class = resolve($package['class']);
                $items[$package['name']] = $class->{$package['function']}($this->site, $cat);
            }
        }
        if(!empty($items)){
            $array = [];
            $packageItems = [];
            foreach($items as $item){
                foreach($item as $object){
                    $packageItems[] = $object;
                }
            }
            $packageItems = collect($packageItems);
            foreach($nodes as $node){
                $array[] = $node;
                if(!empty($packageItems)){
                    $array[] = $packageItems->shift();
                }
            }
            if(!empty($packageItems)){
                foreach($packageItems as $packageItem){
                    $array[] = $packageItem;
                }
            }
            $nodes = collect($array);
        }
        return view($this->site->theme . '::category.list', compact('site', 'nodes'));
    }
}
