<?php
namespace Finetune\Finetune\Controllers\Admin;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Repositories\Type\TypeInterface;
use \Illuminate\Http\Request;

class TypeController extends BaseController
{
    private $type;

    /**
     * @param TypeInterface $type
     */
    public function __construct(SiteInterface $site, Request $request, TypeInterface $type)
    {
        parent::__construct($site, $request);
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $site = $this->site;
        $route = $this->route;
        $packages = config('packages.type-list');
        return view('finetune::type.list', compact('site','route','packages'));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $site = $this->site;
        $route = $this->route;
        $type = $this->type->find($id);
        if (empty($type)) {
            abort('404');
        }
        return view('finetune::type.show', compact('site','route','type'));
    }


}
