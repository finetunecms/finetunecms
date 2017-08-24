<?php

namespace Finetune\Finetune\Repositories\Node;

use Finetune\Finetune\Entities\Node;
use Finetune\Finetune\Entities\NodeBlocks;
use Finetune\Finetune\Repositories\Helper\HelperInterface;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Cache\Repository as Cache;

class NodeRepository implements NodeInterface
{
    protected $url = [];
    protected $node = [];
    protected $helper;
    protected $auth;
    protected $cache;

    public function __construct(HelperInterface $helper, Auth $auth, Cache $cache)
    {
        $this->helper = $helper;
        $this->auth = $auth;
        $this->cache = $cache;
    }

    // Entity Accesses

    public function all($site, $parent = 0, $area = 0, $frontend = false, $noEager = false)
    {
        $nodeAll = Node::with($this->getWithArray())
            ->where('site_id', '=', $site->id)
            ->orderBy('order');
        if (!empty($parent)) {
            $nodeAll->where('parent', '=', $parent);
        }
        if (!empty($area)) {
            $nodeAll->where('area', '=', 1);
        }
        if ($frontend) {
            $nodeAll->where('publish', '=', 1);
        }

        if (!$noEager) {
            $all = $nodeAll->get();
            $nodes = $this->eagerLoad($all, $frontend, $all->first());
        } else {
            $nodes = $nodeAll->get();
        }
        if (!$frontend) {
            if (!$this->auth->user()->ability('Superadmin', 'can_manage_allcontent')) {
                if (config('finetune.nodeRoles')) {
                    $user = $this->auth->user();
                    $filtered = $nodes->filter(function ($value, $key) use ($user) {
                        return $this->checkIfUserCanView($user, $value);
                    });
                    $nodes = $filtered->each(function ($node, $key) use ($user) {
                        $checkEdit = $this->checkIfUserCanEdit($user, $node);
                        if ($checkEdit) {
                            $node->canEdit = true;
                        } else {
                            $node->canEdit = false;
                        }
                    });
                }
            } else {
                $nodes->each(function ($node, $key) {
                    $node->canEdit = true;
                });
            }
        }
        return $nodes;
    }

    public function find($id, $frontend = false)
    {
        $find = Node::with($this->getWithArray())->find($id);
        return $this->eagerLoad($find, $frontend);
    }

    public function findByTag($site, $tag, $area, $frontend = false)
    {
        $finder = Node::with($this->getWithArray());
        $finder->where('site_id', '=', $site->id);
        if ($area != 0) {
            $finder->where('area_fk', $area);
        } else {
            $finder->where('area_fk', 0);
        }
        $find = $finder->where('tag', '=', $tag)->first();
        return $this->eagerLoad($find, $frontend);
    }

    public function create($site, $request)
    {
        $node = new Node();
        $node->site_id = $site->id;
        $node->type_id = $request['type']['id'];
        if (isset($request['area_fk'])) {
            $node->area_fk = $this->findArea($request['area_fk']);
        } else {
            $node->area_fk = 0;
        }
        $node->area = ($node->area_fk == 0) ? 1 : 0;
        if (empty($request['tag'])) {
            $request['tag'] = $request['title'];
        }
        if ($node->area == 1) {
            $node->tag = $this->tagMaker($site, [], $node, $request['tag']);
        } else {
            $area = $this->find($node->area_fk);
            $node->tag = $this->tagMaker($site, $area, $node, $request['tag']);
        }
        $user = $this->auth->user();
        if (!empty($user)) {
            $node->author_id = $this->auth->user()->id;
        } else {
            $node->author_id = 1;
        }
        $node->publish = isset($request['publish']) ? 1 : 0;
        $node->soft_publish = isset($request['soft_publish']) ? (($request['soft_publish'] == true) ? 1 : 0) : 0;
        $node->exclude = isset($request['exclude']) ? (($request['exclude'] == true) ? 1 : 0) : 0;
        $node->title = strip_tags($request['title']);
        $node->dscpn = isset($request['dscpn']) ? strip_tags($request['dscpn']) : $node->title;
        $node->keywords = strip_tags($request['keywords']);
        $node->body = $this->filterContent($request['body']);
        if (isset($request['media'])) {
            $node->image = $request['media']['id'];
        }

        $node->parent = ($node->area == 1) ? 0 : $request['parent'];
        $node->redirect = $request['redirect'];
        $node->meta_title = strip_tags($request['meta_title']);
        $node->url_slug = $this->slugBuilder($node);
        $node->publish_on = $this->parseDate($request['publish_on']);
        $node->save();
        if (isset($request['tags'])) {
            foreach ($request['tags'] as $tag) {
                $node->tags()->attach($tag['id']);
            }
        }
        $this->blocks($node, $request['blocks']);
        if (isset($request['homepage'])) {
            if ($request['homepage'] == 1) {
                $this->setHomePage($site, $node);
            }
        }

        $this->customFields($node, $request);
        if (isset($request['packages'])) {
            $packages = $request['packages'];
            $this->savePackages($site, $node, $packages);
        }
        if (config('finetune.cache')) {
            $this->clearCache($node, $site);
        }
        return $this->find($node->id);
    }

    public function update($site, $id, $request)
    {
        $node = $this->find($id);
        $node->site_id = $site->id;
        $node->type_id = $request['type']['id'];
        if (isset($request['area_fk'])) {
            $node->area_fk = $this->findArea($request['area_fk']);
        }
        $node->area = ($node->area_fk == 0) ? 1 : 0;
        if (empty($request['tag'])) {
            $request['tag'] = $request['title'];
        }
        if ($node->area == 1) {
            $node->tag = $this->tagMaker($site, [], $node, $request['tag']);
        } else {
            $area = $this->find($node->area_fk);
            $node->tag = $this->tagMaker($site, $area, $node, $request['tag']);
        }
        $node->author_id = $this->auth->user()->id;
        $node->publish = isset($request['publish']) ? $request['publish'] : 0;
        $node->soft_publish = isset($request['soft_publish']) ? (($request['soft_publish'] == true) ? 1 : 0) : 0;
        $node->exclude = isset($request['exclude']) ? (($request['exclude'] == true) ? 1 : 0) : 0;
        $node->title = strip_tags($request['title']);
        $node->dscpn = isset($request['dscpn']) ? strip_tags($request['dscpn']) : $node->title;
        $node->keywords = strip_tags($request['keywords']);
        $node->body = $this->filterContent($request['body']);
        $node->image = $request['media']['id'];
        if (isset($request['parent'])) {
            $node->parent = ($node->area == 1) ? 0 : $request['parent'];
        }
        $node->redirect = $request['redirect'];
        $node->meta_title = strip_tags($request['meta_title']);
        $node->publish_on = $this->parseDate($request['publish_on']);
        $node->url_slug = $this->slugBuilder($node);
        $node->save();
        $node->tags()->detach();
        foreach ($request['tags'] as $tag) {
            $node->tags()->attach($tag['id']);
        }

        $this->blocks($node, $request['blocks']);
        if ($request['homepage'] == 1) {
            $this->setHomePage($site, $node);
        }
        $this->customFields($node, $request);

        if (isset($request['packages'])) {
            $packages = $request['packages'];
            $this->savePackages($site, $node, $packages);
        }
        if (config('finetune.cache')) {
            $this->clearCache($node, $site);
        }
        return $this->find($id);
    }

    public function delete($site, $id)
    {
        $node = $this->find($id);
        if (isset($node)) {
            if (config('finetune.cache')) {
                $site = $site->getSite();
                $this->cache->forget($site->id . '-' . $node->url_slug);
            }
            $node->delete();
        }
    }

    public function slugBuilder($node)
    {
        $this->url = [];
        $this->url[] = $node;
        $this->slugRecursive($node);
        $type = $node->type()->first();
        $outputs = explode(':', $type->outputs);
        $currentIndex = 0;
        $urlString = '';

        foreach ($outputs as $output) {
            if (isset($this->url[$currentIndex])) {
                switch ($output) {
                    case 'list':
                        $urlString = $urlString . '/' . $this->url[$currentIndex]->tag;
                        $currentIndex = $currentIndex + 1;
                        break;
                    case 'list_date':
                        $date = $this->url[$currentIndex]->publish_on;
                        $slug = \Carbon\Carbon::parse($date)->format('Y-m-d');
                        $urlString = $urlString . '/' . $slug;
                        break;
                    case 'display':
                        $urlString = $urlString . '/' . $this->url[$currentIndex]->tag;
                        $currentIndex = $currentIndex + 1;
                        break;
                }
            }
        }
        foreach ($this->url as $key => $url) {
            if ($key >= $currentIndex) {
                $urlString = $urlString . '/' . $this->url[$currentIndex]->tag;
                $currentIndex = $currentIndex + 1;
            }
        }
        return $urlString;
    }

    public function updateOrder($nodes)
    {
        foreach ($nodes as $order => $node) {
            Node::where('id', '=', $node['id'])->update(
                [
                    'order' => $order,
                ]
            );
        }
    }

    public function moveNodes($nodes, $parent)
    {
        foreach ($nodes as $nodeObj) {
            $node = $this->find($nodeObj['id']);
            $node->parent = $parent['id'];
            $this->moveNode($node, $parent);
        }
    }

    public function publish($id)
    {
        $node = Node::with('media')->whereNull('deleted_at')->find($id);
        $node->publish = ($node->publish == 1 ? 0 : 1);
        $node->save();
        return $node;
    }

    public function links($site)
    {
        $nodes = $this->all($site, 0, 0, false, true);

        $links = [];
        foreach ($nodes as $node) {
            $links[] = [
                'title' => $node->title,
                'id' => $node->id,
                'url_slug' => $node->url_slug
            ];
        }
        return $links;
    }

    public function eagerLoad($collection, $frontend = false, $itemWithType = null)
    {
        if (empty($itemWithType)) {
            $itemWithType = $collection;
        }

        if (!empty($collection)) {
            $collection = $collection->load(['children' => function ($query) use ($collection, $frontend, $itemWithType) {
                $item = $itemWithType;

                if (!empty($item->type->order_by)) {

                    $order = explode(':', $item->type->order_by);
                    $query->orderBy($order[0], $order[1]);
                } else {
                    $query->orderBy('order');
                }
                if ($frontend) {
                    $query->where('publish', '=', '1');
                }

            },
                'area_node.children' => function ($query) use ($collection, $frontend, $itemWithType) {
                    $item = $itemWithType;
                    if (!empty($item->type->order_by)) {
                        $order = explode(':', $item->type->order_by);
                        $query->orderBy($order[0], $order[1]);
                    } else {
                        $query->orderBy('order');
                    }
                    if ($frontend) {
                        $query->where('publish', '=', '1');
                    }

                },
                'parent_node.children' => function ($query) use ($collection, $frontend, $itemWithType) {
                    $item = $itemWithType;

                    if (!empty($item->type->order_by)) {
                        $order = explode(':', $item->type->order_by);
                        $query->orderBy($order[0], $order[1]);
                    } else {
                        $query->orderBy('order');
                    }
                    if ($frontend) {
                        $query->where('publish', '=', '1');
                    }

                },
                'children.tags',
                'children.type',
                'children.area_node',
                'children.blocks',
                'children.values',
                'children.values.field',
                'children.site',
                'children.tags',
                'children.media',
                'children.roles']);

            if (isset($collection->children)) {
                foreach ($collection->children as $key => $child) {
                    $child = $child->load(['children' => function ($query) use ($collection, $frontend, $itemWithType) {
                        $item = $itemWithType;
                        if (!empty($item->type->order_by)) {
                            $order = explode(':', $item->type->order_by);
                            $query->orderBy($order[0], $order[1]);
                        } else {
                            $query->orderBy('order');
                        }
                        if ($frontend) {
                            $query->where('publish', '=', '1');
                        }
                    }]);
                    $collection->children[$key] = $child;
                }
            }
            return $collection;
        } else {
            return null;
        }
    }

    public function makeBread($node)
    {
        $bread = $this->breadRecursive($node, ['admin' => 'Admin', 'admin/content' => 'Content'], 'admin/content');
        return $bread;
    }

    public function search($site, $searchTerm, $area = null, $frontend = false)
    {
        $node = Node::with($this->getWithArray())->where('title', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('id', 'LIKE', '%' . $searchTerm . '%');
        if (isset($area)) {
            $node->where('area_fk', '=', $area);
        }
        $node->where('site_id', '=', $site->id);
        $nodeObj = $node->get();

        return $this->eagerLoad($nodeObj, $frontend, $nodeObj->first());
    }

    public function filterContent($content)
    {
        $active = config('purifier.active');
        if ($active) {
            $filters = ['em', 'strong', 'span', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
            foreach ($filters as $filter) {
                $content = preg_replace("/<" . $filter . ">@(.*?)<\/" . $filter . ">/", "@$1", $content);
                $content = preg_replace("/<" . $filter . ">@(.*?)/", "@$1", $content);
                $content = preg_replace("/@(.*?)<\/" . $filter . ">/", "@$1", $content);
                $content = preg_replace("/<" . $filter . "[^>]*>[\s|&nbsp;]*<\/" . $filter . ">/", '', $content);
                $content = preg_replace("/<(\w+)\b(?:\s+[\w\-.:]+(?:\s*=\s*(?:\"[^\"]*\"|\"[^\"]*\"|[\w\-.:]+))?)*\s*\/?>\s*<\/\1\s*>/", '', $content);
            }
            $content = preg_replace("/<span[^>]+\>/i", "", $content);
            $content = str_replace("<div class=\"embed\">&nbsp;</div>", "", $content);  // This removes the left behinds from tinymce when wrapping the iframe in the embed divs
            $content = str_replace("<div class=\"table-wrap\">&nbsp;</div>", "", $content);  // This removes the left behinds from tinymce when wrapping the table with divs
            $content = str_replace('?nosave=true', '', $content);
            return \Purifier::clean($content);
        } else {
            return $content;
        }

    }

    // Frontend Functions

    public function findBySlug($site, $slug)
    {
        $node = Node::with($this->getWithArray())
            ->where('url_slug', '=', $slug)
            ->where('site_id', '=', $site->id)
            ->first();

        if(isset($node)){
            if ($node->publish != 1) {
                if ($node->soft_publish != 1) {
                    $node = null;
                }
            }
        }
        return $this->eagerLoad($node, true);
    }

    public function findHomepage($site)
    {
        $node = Node::with($this->getWithArray())
            ->where('homepage', '=', 1)
            ->where('site_id', '=', $site->id)
            ->first();
        if ($node->publish != 1) {
            if ($node->soft_publish != 1) {
                $node = null;
            }
        }
        return $this->eagerLoad($node, true);
    }

    public function movable($site)
    {
        $all = $this->all($site, 0,0,false,true);
        $array = [];
        foreach ($all as $index => $node) {
            $type = $node->type()->first();
            if ($node->area != 1) {
                if ($type->date == 1) {
                    unset($all[$index]);
                }
            }
            if ($type->children != 1) {
                unset($all[$index]);
            }
        }
        foreach ($all as $item) {
            $array[] = $item;
        }
        return $array;
    }

    public function findValue($node, $fieldTag)
    {
        foreach ($node->values as $value) {
            if (!empty($value->field)) {
                if ($value->field->name == $fieldTag) {
                    return $value->value;
                }
            }
        }
        return null;
    }

    public function makeBreadFrontend($site, $node)
    {
        $this->url[] = $node;
        $this->slugRecursive($node);
        $type = $node->type()->first();
        $outputs = explode(':', $type->outputs);
        $currentIndex = 0;
        $bread = [];
        $urlString = '';
        foreach ($outputs as $index => $output) {
            if (isset($this->url[$currentIndex])) {
                $index = $currentIndex;
                switch ($output) {
                    case 'list':
                        $urlString = $urlString . '/' . $this->url[$currentIndex]->tag;
                        if ($this->url[$currentIndex]->tag == $node->tag) {
                            $bread['last'] = $this->url[$currentIndex]->title;
                        } else {
                            $bread[$urlString] = $this->url[$currentIndex]->title;
                        }
                        $currentIndex = $currentIndex + 1;
                        break;
                    case 'list_date':
                        $date = $this->url[$currentIndex]->publish_on;
                        $title = \Carbon\Carbon::parse($date)->format(config('finetune.date'));
                        $slug = \Carbon\Carbon::parse($date)->format('Y-m-d');
                        $urlString = $urlString . '/' . $slug;
                        $bread[$urlString] = $title;
                        break;
                    default:
                        $urlString = $urlString . '/' . $this->url[$currentIndex]->tag;
                        if ($this->url[$currentIndex]->tag == $node->tag) {
                            $bread['last'] = $this->url[$currentIndex]->title;
                        } else {
                            $bread[$urlString] = $this->url[$currentIndex]->title;
                        }
                        $currentIndex = $currentIndex + 1;
                        break;
                }
                unset($this->url[$index]);
            } else {
                if ($output == 'list_date') {
                    if ($this->url[$currentIndex]->tag == $node->tag) {

                    }
                } else {
                    $urlString = $urlString . '/' . $node->tag;
                    if ($this->url[$currentIndex]->tag == $node->tag) {
                        $bread['last'] = $node->title;
                    } else {
                        $bread[$urlString] = $node->title;
                    }
                    $currentIndex = $currentIndex + 1;
                }
            }
        }
        if (!empty($this->url)) {
            foreach ($this->url as $index => $url) {
                $urlString = $urlString . '/' . $url->tag;
                if ($url->tag == $node->tag) {
                    $bread['last'] = $url->title;
                } else {
                    $bread[$urlString] = $url->title;
                }
                $currentIndex = $currentIndex + 1;
            }
        }
        return $bread;
    }

    public function frontEndSearch($site, $searchTerm)
    {
        $nodes = Node::with($this->getWithArray())
            ->where('site_id', '=', $site->id)
            ->where(function ($q) use ($searchTerm) {
                $q->where('tag', 'like', '%' . str_replace(' ', '-', $searchTerm) . '%')
                    ->orWhere('title', 'like', "%{$searchTerm}%")
                    ->orWhere('body', 'like', "%{$searchTerm}%")
                    ->orWhereHas('blocks', function ($q) use ($searchTerm) {
                        $q->where(function ($q) use ($searchTerm) {
                            $q->where('title', 'like', "%{$searchTerm}%")
                                ->orWhere('content', 'like', "%{$searchTerm}%");
                        });
                    });
            })->get();
        foreach ($nodes as & $node) {

            if ($node->publish != 1) {
                if ($node->soft_publish != 1) {
                    $node = null;
                }
            }
        }
        return $this->eagerLoad($nodes, true, $nodes->first());
    }

    public function savePackages($site, $node, $packages)
    {
        foreach ($packages as $package) {
            $class = resolve($package['class']);
            $class->{$package['function']}($site, $node, $package['fields']);
        }
    }

    public function checkIfUserCanView($user, $node)
    {
        if ($user->ability(['Superadmin'], ['can_manage_allcontent'])) {
            return true;
        }
        if (!$node->roles->isEmpty()) {
            foreach ($node->roles as $role) {
                foreach ($user->roles as $userRoles) {
                    if ($role->id == $userRoles->id) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function checkIfUserCanEdit($user, $node)
    {
        if (!empty($node) && !empty($user)) {
            if ($user->ability(['Superadmin'], ['can_manage_allcontent'])) {
                return true;
            }
            if ($node->area != 1) {
                $node = $node->area_node()->first();
            }
            if (!$node->roles->isEmpty()) {
                foreach ($node->roles as $role) {
                    foreach ($user->roles as $userRoles) {
                        if ($role->id == $userRoles->id) {
                            if ($role->pivot->can_edit == 1) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    // Private Functions

    private function getWithArray()
    {
        return [
            'tags',
            'parent_node',
            'area_node',
            'blocks',
            'type',
            'media',
            'site',
            'children',
            'roles',
            'values',
            'values.field',
            'blocks.media'];
    }

    private function clearCache($node, $site)
    {
        $this->cache->forget($site->id . '-' . $node->url_slug);
        if (isset($node->parent)) {
            if (!empty($node->parent)) {
                $node = $node->parent_node()->first();
                $this->cache->forget($site->id . '-' . $node->url_slug);
                $this->clearCache($node, $site);
            }
        }
    }

    private function tagMaker($site, $area, $node, $tag, $i = 1)
    {
        $tag = $this->helper->buildTag($tag);

        if (!empty($area)) {
            $tagExists = $this->findByTag($site, $tag, $area->id);
        } else {
            $tagExists = $this->findByTag($site, $tag, 0);
        }

        if (!empty($tagExists)) {
            if (!empty($node)) {
                if ($tagExists->id == $node->id) {
                    return $tag;
                }
            }
            $tag = $tag . '-' . $i;
            return $this->tagMaker($site, $area, $node, $tag, $i);
        }
        return $tag;
    }

    private function findArea($areaId)
    {
        $area = $this->find($areaId);
        if (!empty($area)) {
            if (empty($area->parent)) {
                return $area->id;
            } else {
                return $this->findArea($area->parent);
            }
        } else {
            return $areaId;
        }

    }

    private function setHomePage($site, $nodeObj)
    {
        $nodes = $this->all($site);
        foreach ($nodes as $node) {
            unset($node->canEdit);
            if ($node->homepage == 1) {
                $node->homepage = 0;
                $node->save();
            }
            if ($node->id == $nodeObj->id) {
                $node->homepage = 1;
                $node->save();
            }
        }
    }

    private function slugRecursive($node, $level = 0)
    {
        $level = $level + 1;
        if ($level < 100) { // Stops never ending loops
            if (!empty($node->parent)) {
                $parent = $this->find($node->parent, true);
                if (!empty($parent)) {
                    array_unshift($this->url, $parent);
                    $this->slugRecursive($parent, ($level + 1));
                }
            }
        }
    }

    private function customFields($node, $request)
    {
        $values = $node->values()->with('field')->get();
        foreach ($values as $value) {
            $value->delete();
        }
        $type = $node->type()->first();
        $fields = $type->fields()->get();
        foreach ($fields as $field) {
            $value = '';
            foreach ($request['fields'] as $fieldItem) {
                if ($field->id == $fieldItem['id']) {
                    if (isset($fieldItem['value'])) {
                        $value = $fieldItem['value'];
                    } else {
                        $value = null;
                    }
                }
            }
            if (is_array($value)) {
                foreach ($value as $key => $val) {
                    if (isset($val['value'])) {
                        $data = [
                            'node_id' => $node->id,
                            'field_id' => $field->id,
                            'value' => $val['value'],
                        ];
                        $node->values()->create($data);
                    } else {
                        if ($key == 'value') {
                            $data = [
                                'node_id' => $node->id,
                                'field_id' => $field->id,
                                'value' => $val,
                            ];
                            $node->values()->create($data);
                        }
                    }
                }
            } else {
                $data = [
                    'node_id' => $node->id,
                    'field_id' => $field->id,
                    'value' => $value,
                ];
                $node->values()->create($data);

            }
        }
    }

    private function blocks($node, $blocks)
    {
        $node->blocks()->delete();

        foreach ($blocks as $blockItem) {
            $block = new NodeBlocks();
            $block->node_id = $node->id;
            $block->name = $blockItem['name'];
            $block->content = $this->filterContent($blockItem['content']);
            $block->title = strip_tags($blockItem['title']);
            if (isset($blockItem['media'])) {
                $block->image = $blockItem['media']['id'];
            }
            $block->save();
        }
    }

    private function moveNode($node, $parent)
    {

        if (isset($parent['area'])) {
            if ($parent['area'] == 1) {
                $node->area_fk = $parent['id'];
            } else {
                $node->area_fk = $parent['area_fk'];
            }
            $node->type_id = $parent['type_id'];
            $node->area = 0;
            $node->save();
            $this->slugBuilder($node);
            if (!empty($node->children)) {
                foreach ($node->children as $child) {
                    $this->moveNode($child, $parent);
                }
            }
        } else {
            if (isset($parent['id'])) {
                if ($parent['id'] == 0) {
                    $node->area = 1;
                    $node->area_fk = 0;
                    $node->save();
                    $this->slugBuilder($node);
                    if (!empty($node->children)) {
                        foreach ($node->children as $child) {
                            $this->moveNode($child, $node);
                        }
                    }
                }
            }
        }
    }

    private function breadRecursive($node, $bread, $url)
    {
        if ($node->parent != 0) {
            $parent = $this->find($node->parent);
            $bread = $this->breadRecursive($parent, $bread, $url);
        }
        $bread[$url . '/' . $node->id] = $node->title;
        return $bread;
    }

    private function parseDate($date)
    {
        if (isset($date)) {
            if ($date != 'Invalid date') {
                $newDate = \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
            } else {
                $newDate = \Carbon\Carbon::now()->format('Y-m-d  H:i:s');
            }
        } else {
            $newDate = \Carbon\Carbon::now()->format('Y-m-d  H:i:s');
        }
        return $newDate;
    }
}