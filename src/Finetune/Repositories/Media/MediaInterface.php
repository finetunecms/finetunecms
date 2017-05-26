<?php namespace Finetune\Finetune\Repositories\Media;

/**
 * Interface MediaInterface
 * @package Repositories\Media
 */
interface MediaInterface
{
    public function all($site);

    public function find($id);

    public function findByFileName($site, $filename);

    public function create($site, $request);

    public function update($site, $id, $request);

    public function destroy($id);

    // Image Manipulations And Rendering

    public function renderImage($site, $image, $img = true);

    public function saveRender($site, $name, $img, $quality);

    public function crop($img, $width, $height, $x = 0, $y = 0);

    public function resize($img, $width, $height = null);

    public function resizeCanvas($img, $width, $height = null, $location = 'center', $relative = false, $background = '000000');

    public function fit($img, $x, $y);

    public function flip($img, $flip = 'v');

    public function rotate($img, $degree = '90');

    public function brightness($img, $amount = '0');

    public function contrast($img, $amount = 0);

    public function blur($img, $amount = 0);

    public function greyscale($img);

    public function sharpen($img, $amount = 0);

    public function gamma($img, $amount = 0);
}