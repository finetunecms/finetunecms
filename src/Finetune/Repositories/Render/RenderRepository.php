<?php
/**
 * Finetune CMS
 *
 * Render Repository
 *
 * Render Repository renders the pages for public viewing, depending on the url.
 */

namespace Finetune\Finetune\Repositories\Render;

use Finetune\Finetune\Repositories\Node\NodeInterface;
use Finetune\Finetune\Entities\Redirect as RedirectOBj;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginator;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Support\Collection as Collection;

class RenderRepository implements RenderInterface
{
    protected $node;
    protected $contentArray;
    protected $output;
    protected $removeFromPath;
    protected $view;
    protected $cache;
    protected $url;
    protected $app;

    public function __construct(NodeInterface $node, View $view, Cache $cache, URL $url, App $app)
    {
        $this->node = $node;
        $this->contentArray = [];
        $this->output = '';
        $this->removeFromPath = ['.json', '.rss', '.pdf', '.render'];
        $this->view = $view;
        $this->cache = $cache;
        $this->url = $url;
        $this->app = $app;
    }

    public function buildFinetune($site, $request)
    {
        $catchAll = $this->_catchAll($site, $request);

        if ($catchAll['redirect']) {
            return redirect($catchAll['url']);
        }
        if ($catchAll['exists']) {
            if ($catchAll['render']) {
                return response($catchAll['node'], 200);
            }
            if ($catchAll['json']) {
                return response()->json($catchAll['node']);
            } elseif ($catchAll['pdf']) {
                //$pdf = \PDF::loadView($catchAll['node']['site']->theme . '::pdf', $this->contentArray);
                //return $pdf->stream($catchAll['node']['node']->title . '.pdf');
            } elseif ($catchAll['rss']) {
                /*
                if (isset($catchAll['node']['list'])) {
                    $feed = App::make("feed");
                    $feed->setCache(60, $catchAll['node']['node']->tag . 'FeedKey');
                    if (!$feed->isCached()) {
                        $node = $catchAll['node']['node'];
                        $feed->title = $node->title;
                        $feed->description = $node->title;
                        $feed->logo = '';
                        $feed->link = \URL::to($node->url_slug);
                        $feed->setDateFormat('datetime');
                        $feed->pubdate = $node->publish_on;
                        $feed->lang = 'en';
                        $feed->setShortening(true);
                        $feed->setTextLimit(100);
                        foreach ($catchAll['node']['list'] as $item) {
                            $feed->add($item->title, $catchAll['node']['site']->title, \URL::to($item->url_slug), $item->publish_on, $item->dscpn, $item->body);
                        }
                    }
                    return $feed->render('atom');

                } else {
                    return response('Sorry but this area cant be made into a rss feed as it dosn\'t contain a list of data', '404');
                }
                  */
            } else {
                if (is_array($catchAll['node'])) {
                    if (config('finetune.redirects')) {
                        return $this->findRedirect($catchAll, $request);
                    }
                    return response($catchAll['node'][0], $catchAll['node'][1]);
                }
                return response($catchAll['node'], 200);
            }
        } else {
            return response($catchAll['node'], 301);
        }
        return abort('404');
    }

    public function objToArray($obj, &$arr)
    {
        if (!is_object($obj) && !is_array($obj)) {
            $arr = $obj;
            return $arr;
        }
        foreach ($obj as $key => $value) {
            if (!empty($value)) {
                $isEloquent = is_subclass_of($value, 'Illuminate\Database\Eloquent\Model');
                if ($isEloquent) {
                    $arr[$key] = $value->toArray();
                } else {
                    $arr[$key] = array();
                    $this->objToArray($value, $arr[$key]);
                }

            } else {
                $arr[$key] = $value;
            }
        }
        return $arr;
    }

    public function findRedirect($catchAll, $request)
    {
        $url = $request->path();
        $newUrl = RedirectOBj::where('old', '=', $url)->first();
        if (!empty($newUrl)) {
            return redirect($newUrl->new);
        } else {
            return response($catchAll['node'][0], $catchAll['node'][1]);
        }
    }

    public function buildPublic($site, $request, $path)
    {
        if (empty($path)) {
            $path =  $request->path();
        }

        $this->view->addNamespace($site->theme, public_path() . '/themes/' . $site->theme);
        $this->contentArray['actualPath'] = $path;
        $this->contentArray['path'] = $path;
        foreach ($this->removeFromPath as $item) {
            $this->contentArray['path'] = str_replace($item, '', $this->contentArray['path']);
        }
        $this->contentArray['pathSplit'] = explode('/', $this->contentArray['path']);
        if ($path == '/') {
            $node = $this->node->findHomepage($site);
            if (config('finetune.cache')) {
                if ($this->cache->has($site->id . '-' . $node->url_slug)) {
                    return $this->cache->get($site->id . '-' . $node->url_slug);
                }
            }
            if (!empty($node)) {
                $page = $this->renderPage($site, $node, $request);
                return $page;
            } else {
                return $this->renderError($site);
            }
        } else {
            $this->contentArray['path'] = '/' . $this->contentArray['path'];

            $node = $this->node->findBySlug($site, $this->contentArray['path']);
            if (config('finetune.cache')) {
                if ($this->cache->has($site->id . '-' . $node->url_slug)) {
                    return $this->cache->get($site->id . '-' . $node->url_slug);
                }
            }
            if (!empty($node->redirect)) {
                return $node;
            }
            if (!empty($node)) {
                $page = $this->renderPage($site, $node, $request);
                return $page;
            } else {
                if (in_array('category', $this->contentArray['pathSplit'])) {
                    $node = $this->node->findByTag($site,$this->contentArray['pathSplit'][0], 0);
                    return $this->renderPage($site, $node, $request);
                } else {
                    $date = end($this->contentArray['pathSplit']);
                    try {
                        $this->contentArray['date'] = \Carbon\Carbon::createFromFormat('Y', $date);
                        $this->contentArray['dateType'] = 'year';
                    } catch (\Exception $err) {
                        try {
                            $this->contentArray['date'] = \Carbon\Carbon::createFromFormat('Y-m', $date);
                            $this->contentArray['dateType'] = 'month';
                        } catch (\Exception $err) {
                            try {
                                $this->contentArray['date'] = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
                                $this->contentArray['dateType'] = 'day';
                            } catch (\Exception $err) {
                                return $this->renderError($site);
                            }
                        }
                    }
                    $nodeSlug = str_replace('/' . $date, '', $this->contentArray['path']);
                    $node = $this->node->findBySlug($site, $nodeSlug);
                    if (!empty($node->redirect)) {
                        return $node;
                    }
                    if (!empty($node)) {
                        $page = $this->renderPage($site, $node, $request);
                        return $page;
                    } else {
                        return $this->renderError($site);
                    }
                }
            }
        }
    }

    public function renderError($site)
    {
        $view = View($site->theme . '::errors.error', ['site' => $site]);
        if (!empty($view)) {
            return [$view, '404'];
        } else {
            return 'view error';
        }
    }

    public function renderPage($site, $node, $request, $path = null)
    {
        if (!empty($path)) {
            $this->pathSplit($path);
        }
        $this->contentArray['node'] = $node;
        $this->contentArray['area'] = $node->area_node;
        if (empty($this->contentArray['area'])) {
            $this->contentArray['area'] = $this->contentArray['node'];
        }
        $this->contentArray['parent'] = $node->parent_node;
        if (empty($this->contentArray['parent'])) {
            $this->contentArray['parent'] = $this->contentArray['node'];
        }
        $this->contentArray['tags'] = $node->tags;
        $this->contentArray['blocks'] = $node->blocks;
        $this->contentArray['type'] = $node->type;
        $this->contentArray['media'] = $node->media;
        $this->contentArray['site'] = $site;
        $this->contentArray['roles'] = $node->roles;
        $this->contentArray['values'] = $node->values;
        $this->contentArray['children'] = $node->children;
        $this->getOutput();
        $this->renderBlocks();
        $this->renderBodyBlock();
        if (!empty($this->contentArray['children'])) {
            $this->filterChildren();
        }
        if ($this->output == 'list') {
            $this->_list($request);
        }
        if ($this->output == 'list_date') {
            $this->_listDate($request);
            $this->output = 'list';
        }
        if ($this->output == 'error') {
            return $this->renderError($site);
        }
        foreach ($this->removeFromPath as $item) {
            if (strpos($this->contentArray['actualPath'], $item)) {
                if ($item == '.render') {
                    $this->getView();
                    return View($this->contentArray['site']->theme . '::' . $this->contentArray['type']->layout . '.' . $this->output, $this->contentArray);
                } else {
                    return $this->contentArray;
                }
            }
        }
        $this->getView();

        $view = View($this->contentArray['site']->theme . '::' . $this->contentArray['type']->layout . '.' . $this->output, $this->contentArray);

        if (config('finetune.cache')) {
            $viewRendered = $view->render();
            $this->cache->forever($this->contentArray['site']->id . '-' . $this->contentArray['node']->url_slug, $viewRendered);
        }
        return $view;
    }

    public function pathSplit($path)
    {
        $this->contentArray['path'] = $path;
        $this->contentArray['actualPath'] = $path;
        $this->contentArray['pathSplit'] = explode('/', $this->contentArray['path']);
    }

    public function sitemap($site)
    {
        $nodes = $this->node->all(0,0,$site->id, true, true);
        $nodes = $nodes->filter(function ($value, $key) {
            if ($value->exclude == 1) {
                return false;
            }
            return true;
        });
        $protocol = config('finetune.protocol');
        $sitemap = $this->app->make("sitemap");
        $sitemap->setCache('laravel.sitemap', 60);
        if (!$sitemap->isCached()) {
            foreach ($nodes as $node) {
                $images = $this->getSitemapImages($node);
                if ($node->homepage) {
                    $sitemap->add($protocol.$this->site->domain.$node->url_slug, $node->updated_at, '1.0', 'daily', $images);
                } else {
                    if ($node->area) {
                        $sitemap->add($protocol.$this->site->domain.$node->url_slug, $node->updated_at, '0.9', 'daily', $images);
                    } else {
                        $sitemap->add($protocol.$this->site->domain.$node->url_slug, $node->updated_at, '0.8', 'monthly', $images);
                    }
                }
            }
        }
        return $sitemap->render('xml');
    }

    private function _catchAll($site, $request)
    {
        $array = [];
        $url = $request->url();
        $forwarders = config('forwarders');
        $path = $request->path();
        if (!empty($forwarders)) {
            foreach ($forwarders as $urlFowarder => $slug) {
                if ($url == $urlFowarder) {
                    $path = $slug;
                }
            }
        }
        $array['node'] = $this->buildPublic($site,$request,$path);
        if (strpos($url, '.json')) {
            $array['exists'] = true;
            $array['json'] = true;
            $array['pdf'] = false;
            $array['render'] = false;
            $array['redirect'] = false;
            $array['rss'] = false;
        } elseif (strpos($url, '.pdf')) {
            $array['exists'] = true;
            $array['json'] = false;
            $array['render'] = false;
            $array['pdf'] = true;
            $array['redirect'] = false;
            $array['rss'] = false;
        } elseif (strpos($url, '.rss')) {
            $array['exists'] = true;
            $array['json'] = false;
            $array['pdf'] = false;
            $array['render'] = false;
            $array['rss'] = true;
            $array['redirect'] = false;
        } elseif (strpos($url, '.render')) {
            $array['exists'] = true;
            $array['json'] = false;
            $array['pdf'] = false;
            $array['render'] = true;
            $array['rss'] = false;
            $array['redirect'] = false;
        } else {
            $array['exists'] = true;
            $array['json'] = false;
            $array['pdf'] = false;
            $array['render'] = false;
            $array['rss'] = false;
            $array['redirect'] = false;
            if (!empty($array['node']->redirect)) {
                $array['redirect'] = true;
                $array['url'] = $array['node']->redirect;
            }
        }
        return $array;
    }

    private function renderBodyBlock()
    {
        $this->contentArray['body'] = new \stdClass();
        $this->contentArray['body']->content = $this->contentArray['node']->compile();
        $this->contentArray['body']->title = $this->contentArray['node']->title;
        if (!empty($this->contentArray['node']->image)) {
            if (!empty($this->contentArray['media'])) {
                $this->contentArray['body']->image = $this->contentArray['media']->external;
            }
        } else {
            $this->contentArray['body']->image = '';
        }
        $this->contentArray['body']->name = 'body';
    }

    private function renderBlocks()
    {
        $typeBlocks = explode(':', $this->contentArray['type']->blocks);
        $blocks = $this->contentArray['blocks'];
        foreach ($typeBlocks as $blockKey) {
            foreach ($blocks as $block) {
                if ($block->name == $blockKey) {
                    $this->contentArray[$blockKey] = new \stdClass();
                    if (!empty($blocksEdited)) {
                        foreach ($blocksEdited as $index => $editedBlock) {
                            if ($index == $blockKey) {
                                $this->contentArray[$blockKey]->content = $editedBlock->compile();
                                $this->contentArray[$blockKey]->title = $editedBlock->title;
                            }
                        }
                    } else {
                        $this->contentArray[$blockKey]->content = $block->compile();
                        $this->contentArray[$blockKey]->title = $block->title;
                    }
                    if (!empty($blocksEdited)) {
                        foreach ($blocksEdited as $index => $editedBlock) {
                            if ($index == $blockKey) {
                                if (!empty($editedBlock->media)) {
                                    $this->contentArray[$blockKey]->media = $editedBlock->media;
                                } else {
                                    $this->contentArray[$blockKey]->media = '';
                                }
                            }
                        }
                    } else {
                        if (!empty($block->image)) {
                            $this->contentArray[$blockKey]->media = $block->media;
                        } else {
                            $this->contentArray[$blockKey]->media = '';
                        }
                    }
                    $this->contentArray[$blockKey]->name = $block->name;
                }
            }
        }
    }

    private function getOutput()
    {
        if (in_array('category', $this->contentArray['pathSplit'])) {
            $this->output = 'list';
        } else {
            $outputs = explode(':', $this->contentArray['type']->outputs);
            $count = count($outputs);
            if ($count == 1) {
                $this->output = $outputs[0];
            } else {
                $urlArray = array_filter($this->contentArray['pathSplit']);
                $index = (count($urlArray) - 1);
                if (isset($outputs[$index])) {
                    $this->output = $outputs[$index];
                } else {
                    $this->output = 'display';
                }
            }
        }
    }

    private function getView()
    {
        if (!$this->view->exists($this->contentArray['site']->theme . '::' . $this->contentArray['type']->layout . '.' . $this->output)) {
            $this->contentArray['type']->layout = 'default';
            if (!$this->view->exists($this->contentArray['site']->theme . '::' . $this->contentArray['type']->layout . '.' . $this->output)) {
                $this->output = 'default';
                if (!$this->view->exists($this->contentArray['site']->theme . '::' . $this->contentArray['type']->layout . '.' . $this->output)) {
                    $this->output = 'error';
                }
            }
        }
    }

    private function _list($request)
    {
        if ($this->contentArray['type']->pagination == 1) {
            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage = $this->contentArray['type']->pagination_limit;
            $currentPageResults = $this->contentArray['children']->slice(($page - 1) * $perPage, $perPage)->all();
            $this->contentArray['list'] = new LengthAwarePaginator(
                $currentPageResults,
                count($this->contentArray['children']),
                $perPage,
                ['path' => $this->contentArray['path'], 'query' => $request->query()]);
        } else {
            $this->contentArray['list'] = $this->contentArray['children'];
        }
    }

    private function _listDate($request)
    {
        switch ($this->contentArray['dateType']) {
            case 'year':
                $this->contentArray['children'] = $this->contentArray['children']->filter(function ($value, $key) {
                    $publishOn = \Carbon\Carbon::parse($value->publish_on);
                    if ($publishOn->year == $this->contentArray['date']->year) {
                        return true;
                    }
                    return false;
                });
                break;
            case 'month':
                $this->contentArray['children'] = $this->contentArray['children']->filter(function ($value, $key) {
                    $publishOn = \Carbon\Carbon::parse($value->publish_on);
                    if ($publishOn->format('Y-m') == $this->contentArray['date']->format('Y-m')) {
                        return true;
                    }
                    return false;
                });
                break;
            case 'day':
                $this->contentArray['children'] = $this->contentArray['children']->filter(function ($value, $key) {
                    $publishOn = \Carbon\Carbon::parse($value->publish_on);
                    if ($publishOn->format('Y-m-d') == $this->contentArray['date']->format('Y-m-d')) {
                        return true;
                    }
                    return false;
                });
                break;
        }
        $this->_list($request);
    }

    private function filterChildren()
    {

        $filtered = $this->contentArray['children']->filter(function ($value, $key) {
            if ($value->publish != 1) {
                return false;
            }
            $now = \Carbon\Carbon::now();
            $publishOn = \Carbon\Carbon::parse($value->publish_on);
            if($this->contentArray['type']->today_future != 1){
                if ($now->lt($publishOn)) {
                    return false;
                }
            }
            return true;
        });

        if (!empty($this->contentArray['type']->order_by)) {
            $orderBy = explode(':', $this->contentArray['type']->order_by);
            if (isset($orderBy[0])) {
                if (isset($orderBy[1])) {
                    if ($orderBy[1] == 'desc') {
                        $filtered = $filtered->sortByDesc(function ($node, $key) use($orderBy) {
                            return $node->{$orderBy[0]};
                        });
                    } else {
                        $filtered = $filtered->sortBy(function ($node, $key) use($orderBy) {
                            return $node->{$orderBy[0]};
                        });
                    }
                }
            }
        }

        if (in_array('category', $this->contentArray['pathSplit'])) {
            $lastPartOfslug = end($this->contentArray['pathSplit']);
            $filtered = $filtered->filter(function ($value, $key) use ($lastPartOfslug) {
                if (!$value->tags->isEmpty()) {
                    foreach ($value->tags as $tag) {
                        if ($tag->tag == $lastPartOfslug) {
                            return true;
                        }
                    }
                    return false;
                } else {
                    return false;
                }
            });
        }
        $this->contentArray['children'] = $filtered;
    }

    private function getSitemapImages($node)
    {
        $images = [];
        if (!empty($node->image)) {
            if (!empty($node->media)) {
                $images[] = ['url' => $this->url->to($node->media->external), 'title' => $node->media->title];
            }
        }
        foreach ($node->blocks as $block) {
            if (!empty($block->image)) {
                if (isset($block->media)) {
                    $images[] = ['url' => $this->url->to($block->media->external), 'title' => $block->media->title];
                }
            }
        }
        return $images;
    }
}