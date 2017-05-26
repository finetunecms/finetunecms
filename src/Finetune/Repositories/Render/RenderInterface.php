<?php
namespace Finetune\Finetune\Repositories\Render;

interface RenderInterface
{
    public function buildFinetune($site, $request);

    public function objToArray($obj, &$arr);

    public function findRedirect($catchAll, $request);

    public function buildPublic($site, $request, $path);

    public function renderError($site);

    public function renderPage($site, $node, $request);

    public function pathSplit($path);

    public function sitemap($site);
}