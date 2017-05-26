<?php
namespace Finetune\Finetune\Controllers;

use Finetune\Finetune\Repositories\Node\NodeInterface;
use Finetune\Finetune\Repositories\Render\RenderInterface;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use \App\Http\Controllers\Controller;
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

    public function __construct(NodeInterface $node, RenderInterface $render, SiteInterface $siteInterface, View $view, Mail $mail, Request $request)
    {
        parent::__construct($siteInterface, $request);
        $this->node = $node;
        $this->render = $render;
        $this->siteInterface = $siteInterface;
        $this->view = $view;
        $this->mail = $mail;
    }

    public function index(Request $request)
    {
        return $this->render->buildFinetune($this->site, $request);
    }

    public function search(Request $request)
    {
        $data = [];
        $data['site'] = $this->site;
        $data['searchTerm'] = $request->input('search');
        $data['areaTag'] = $request->input('area');
        if ($data['areaTag'] == 'search') {
            $data['areaTag'] = null;
        }
        $query = \Search::node($data['searchTerm']);
        $searchNodes = $query;
        $data['search'] = [];
        $searchNodeItems = $this->node->search($data['searchTerm'], $data['areaTag'], $frontend = true);
        foreach ($searchNodes as $search) {
            foreach ($searchNodeItems as $index => $searchItem) {
                if ($searchItem->id == $search->id) {
                    unset($searchNodeItems[$index]);
                }
            }
        }
        foreach ($searchNodeItems as $index => $searchItem) {
            $data['search'][] = $searchItem;
        }
        foreach ($searchNodes as $index => $item) {
            $data['search'][] = $item;
        }
        $data['hasResults'] = empty($data['search']) ? false : true;
        if (!empty($data['area'])) {
            $data['area'] = $this->node->findByTag($this->site,$data['areaTag'], 0);
        }
        View::addNamespace($data['site']->theme, public_path() . '/themes/' . $data['site']->theme);

        return View::make($data['site']->theme . '::search.display', $data);
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
}
