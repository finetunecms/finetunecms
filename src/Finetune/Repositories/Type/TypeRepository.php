<?php
namespace Finetune\Finetune\Repositories\Type;

use Finetune\Finetune\Entities\Type;
use Finetune\Finetune\Repositories\Node\NodeInterface;
use \Illuminate\Support\Facades\Response;

/**
 * Class TypeRepository
 * @package Repositories\Type
 */
class TypeRepository implements TypeInterface
{
    protected $node;

    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return Type::all();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return Type::find($id);
    }

    public function findByTitle($tag)
    {
        return Type::with('node')->where('title', '=', $tag)->first();
    }

    public function findDefault()
    {
        return Type::with('node')->where('default_type', '=', 1)->first();
    }

    /**
     * Soft Delete a type
     * @param $id
     * @return mixed|null
     */
    public function destroy($id)
    {
        $type = $this->find($id);
        if(!empty($type)) {
            $default = $this->findDefault();
            if ($type->id != $default->id) {
                foreach ($type->node as $node) {
                    $node->type_id = $default->id;
                }
                $this->updateNodes($type->node);
                $type->delete();
            }
        }
    }

    public function create($request)
    {
        $type = new Type();
        $request['children'] = (isset($request['children']) ? ($request['children'] ? 1 : 0) : 0);
        $request['nesting'] = (isset($request['nesting']) ? ($request['nesting'] ? 1 : 0) : 0);
        $request['ordering'] = (isset($request['ordering']) ? ($request['ordering'] ? 1 : 0) : 0);
        $request['date'] = (isset($request['date']) ? ($request['date'] ? 1 : 0) : 0);
        $request['today_future'] = (isset($request['today_future']) ? ($request['today_future'] ? 1 : 0) : 0);
        $request['today_past'] = (isset($request['today_past']) ? ($request['today_past'] ? 1 : 0) : 0);
        $request['pagination'] = (isset($request['pagination']) ? ($request['pagination'] ? 1 : 0) : 0);
        $request['rss'] = (isset($request['rss']) ? ($request['rss'] ? 1 : 0) : 0);
        $request['blocks'] = (isset($request['blocks']) ? preg_replace("/[\/_|+ -]+/ ", '-', strtolower(trim($request['blocks'], '-'))) : '');
        $request['default'] = (isset($request['default_type']) ? ($request['default_type'] ? 1 : 0) : 0);
        $request['outputs'] = $this->outputs($request['outputs']);
        $type->fill($request);
        $type->save();
        $this->updateNodes($type->node);
        return $this->all();
    }

    public function update($id, $request)
    {
        $type = $this->find($id);
        $oldOutput = $type->outputs;
        $request['children'] = (isset($request['children']) ? ($request['children'] ? 1 : 0) : 0);
        $request['nesting'] = (isset($request['nesting']) ? ($request['nesting'] ? 1 : 0) : 0);
        $request['ordering'] = (isset($request['ordering']) ? ($request['ordering'] ? 1 : 0) : 0);
        $request['default_type'] = (isset($request['default_type']) ? ($request['default_type'] ? 1 : 0) : 0);
        $request['date'] = (isset($request['date']) ? ($request['date'] ? 1 : 0) : 0);
        $request['today_future'] = (isset($request['today_future']) ? ($request['today_future'] ? 1 : 0) : 0);
        $request['today_past'] = (isset($request['today_past']) ? ($request['today_past'] ? 1 : 0) : 0);
        $request['pagination'] = (isset($request['pagination']) ? ($request['pagination'] ? 1 : 0) : 0);
        $request['rss'] = (isset($request['rss']) ? ($request['rss'] ? 1 : 0) : 0);
        $request['blocks'] = (isset($request['blocks']) ? preg_replace("/[\/_|+ -]+/ ", '-', strtolower(trim($request['blocks'], '-'))) : '');
        $request['outputs'] = $this->outputs($request['outputs']);
        $type->fill($request);
        $type->save();
        if($oldOutput != $type->outputs){
            $this->updateNodes($type->node);
        }
        return $this->all();
    }

    private function outputs($outputs)
    {
        $outputsArray = explode(':', $outputs);
        if (!in_array('display', $outputsArray)) {
            $outputs = $outputs . ':display';
        }
        unset($outputsArray);
        return $outputs;
    }

    /**
     * Type list array (id, title)
     * @return array
     */
    public function getTypeList()
    {
        $array = Type::whereNull('deleted_at')->lists('title', 'id');
        $array[0] = 'Please choose a type';
        return $array;
    }

    private function updateNodes($nodes)
    {
        foreach($nodes as $node){
            $node->url_slug = $this->node->slugBuilder($node);
            $node->save();
        }
    }
}