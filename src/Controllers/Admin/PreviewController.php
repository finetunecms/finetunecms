<?php
namespace Finetune\Finetune\Controllers\Admin;

use Finetune\Finetune\Repositories\Render\RenderInterface;
use Finetune\Finetune\Repositories\Node\NodeInterface;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use \Illuminate\Http\Request;

class PreviewController extends Controller
{

    private $node;
    private $site;
    private $render;

    /**
     * @param NodeInterface $node
     * @param SiteInterface $site
     * @param RenderInterface $render
     */
    public function __construct(SiteInterface $site, Request $request, NodeInterface $node, RenderInterface $render)
    {
        parent::__construct($site, $request);
        $this->site = $site;
        $this->node = $node;
        $this->render = $render;
    }

    /**
     * @return string
     */
    public function index()
    {
        $site = $this->site->getSite();
        $node = $this->node->findHomepage($site);
        return $this->renderPreview($site, $node);
    }

    public function show($node)
    {
        $node = '/' . $node;
        $site = $this->site->getSite();
        $node = $this->node->findBySlug($site, $node);
        return $this->renderPreview($site, $node);
    }

    public function update($nodeId, Request $request)
    {
        if ($request->has('body-content')) {
            $node = $this->node->find($nodeId);
            $node->body = $request->get('body-content');
            $node->save();
        }
        $slug = ltrim('/', $node->url_slug);
        return Redirect('/admin/preview/' . $slug);
    }

    private function renderPreview($site, $node)
    {
        if (!empty($node)) {
            $uri = ltrim($node->url_slug, '/');
            $view = $this->render->buildPublic($uri, $site);
            $renderHtml = $view->render();

            $body = View('finetune::partials.adminBody', ["url" => '/admin/preview/' . $node->id, "method" => 'PUT', "node" => $node]);

            $renderHtml = str_replace('<body>', $body, $renderHtml);
            $renderHtml = str_replace($node->body, '<div id="body-content">' . $node->body . '</div>', $renderHtml);

            foreach ($node->blocks as $block) {
                $renderHtml = str_replace($block->content, '<div id="' . $block->tag . '-content">' . $block->content . '</div>', $renderHtml);
            }

            $script = View('finetune::partials.adminScript');

            $renderHtml = str_replace('</body>', $script, $renderHtml);

            return $renderHtml;
        } else {
            abort('404');
        }
    }

}