<?php
namespace Finetune\Finetune\Repositories\Media;

use Finetune\Finetune\Entities\Media;
use Finetune\Finetune\Repositories\Helper\HelperInterface;
use Intervention\Image\ImageManager;

class MediaRepository implements MediaInterface
{
    protected $helper;
    protected $image;

    public function __construct(HelperInterface $helper, ImageManager $imageManager)
    {
        $this->helper = $helper;
        $this->image = $imageManager;
    }

    public function all($site)
    {
        return Media::where('site_id', '=', $site->id)
            ->with('folders', 'nodes')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function find($id)
    {
        return Media::with('folders', 'nodes')->find($id);
    }

    public function findByFileName($site, $filename)
    {
        return Media::where('filename', '=', $filename)
            ->where('site_id', '=', $site->id)
            ->with('folders')
            ->first();
    }

    public function create($site, $request)
    {
        if ($request->hasFile('file')) {
            if ($request->file('file')->isValid()) {
                $file = $request->file('file');
                $destinationPath = storage_path() . '/uploads/' . $site->tag; // upload path
                $extension = $file->getClientOriginalExtension(); // getting image extension
                $fileNameExplode = explode('.', $file->getClientOriginalName());
                if (!empty($fileNameExplode)) {
                    $fileNameMain = strtolower($fileNameExplode[0]);
                    $fileNameMain = $this->helper->buildTag($fileNameMain);
                } else {
                    $fileNameMain = 'no-file-name';
                }
                $fileName = $fileNameMain . '-' . rand(11111, 99999) . '.' . $extension; // renameing image
                $allowedImageMemeTypes = config('upload.imageMime');
                $allowedFileMemeTypes = config('upload.fileMime');
                $version = 1;
                $parent = 0;
                $mime = $file->getClientMimeType();
                if (in_array($mime, $allowedImageMemeTypes)) {
                    $img = $this->image->make($file);
                    if ($img->width() >= $img->height()) {
                        $img->resize(2000, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                    if ($img->height() > $img->width()) {
                        $img->resize(null, 2000, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                    $width = $img->width();
                    $height = $img->height();
                    $img->save($destinationPath . '/' . $fileName);
                    $media = new Media();
                    $media->site_id = $site->id;
                    $media->width = $width;
                    $media->height = $height;
                    $media->filename = $fileName;
                    $media->extension = $extension;
                    $media->original = $fileNameMain;
                    $media->version = $version;
                    $media->parent = $parent;
                    $media->external = '/image/' . $site->tag . '/' . $fileName;
                    $media->path = $destinationPath . '/' . $fileName;
                    $media->thumb = '/image/' . $site->tag . '/' . $fileName . '/200x200';
                    $media->mime = $mime;
                    $media->type = 'image';
                    $media->save();
                    return $media;
                } elseif (in_array($mime, $allowedFileMemeTypes)) {
                    $newDestinationPath = storage_path() . '/uploads/' . $site->tag; // upload path
                    $file->storeAs($newDestinationPath, $fileName);
                    $media = new Media();
                    $media->site_id = $site->id;
                    $media->filename = $fileName;
                    $media->extension = $extension;
                    $media->original = $fileNameMain;
                    $media->version = $version;
                    $media->parent = $parent;
                    $media->external = '/file/' . $site->tag . '/' . $fileName;
                    $media->path = $destinationPath . '/' . $fileName;
                    $media->mime = $mime;
                    $media->type = 'file';
                    $media->save();
                    return $media;
                }
            }
        }

    }

    public function update($site, $id, $request)
    {
        $file = $this->find($id);
        if (isset($request['title'])) {
            $file->title = $request['title'];
        }
        if ($file->filename != $request['filename']) {
            $fileNameExplode = explode('.', $request['filename']);
            if (!empty($fileNameExplode)) {
                $fileNameMain = strtolower($fileNameExplode[0]);
                $fileNameMain = Helper::buildTag($fileNameMain);
            } else {
                $fileNameMain = 'no-file-name';
            }
            $extensionExploder = explode('.', $file->filename);
            $extension = $extensionExploder[1];
            $fileNameMain = $this->checkNewFileName($site, $fileNameMain, $extension);
            $fileLink = '/image/' . $site->tag . '/' . $fileNameMain . '.' . $extension;
            $destinationPath = storage_path() . '/uploads/' . $site->tag . '/' . $fileNameMain . '.' . $extension;
            rename($file->path, $destinationPath);
            $file->filename = $fileNameMain . '.' . $extension;
            $file->external = $fileLink;
            $file->thumb = $fileLink . '/200x200?fit=true&bg=FAFAFA';
            $file->path = $destinationPath;
        }
        $file->save();
        $this->move($file, $request['folders']);
        return $this->all($site);
    }

    public function destroy($media)
    {
        $file = $this->find($media['id']);
        if (!empty($file)) {
            $file->delete();
        }
    }

    public function move($media, $folders)
    {
        $media->folders()->detach();
        foreach ($folders as $folder) {
            $media->folders()->attach($folder['id']);
        }
    }

    // Image Rending and Manipulation
    public function renderImage($site, $image, $img = true)
    {
        if (is_string($image)) {
            $imgObj = $this->findByFileName($site, $image);

        } else {
            $imgObj = $this->find(intval($image));
        }
        if (!empty($imgObj)) {
            if ($img) {
                $img = $this->image->make($imgObj->path);
                return $img;
            } else {
                return $imgObj;
            }
        } else {
            return 'Image Not Found';
        }
    }

    public function saveRender($site, $name, $img, $quality)
    {
        $path = storage_path('uploads/' . $site->tag . '/resized/' . $name);
        $img->save($path, intval($quality));
        return $img;
    }


    public function crop($img, $width, $height, $x = 0, $y = 0)
    {
        $img->crop($width, $height, $x, $y);
        return $img;
    }

    public function doCrop($site, $id, $request)
    {
        $media = $this->find($id);
        $crop = json_decode($request['crop']);
        $img = $this->image->make($media->path);

        if ($crop->scaleX == -1) {
            $img->flip('h');
        }
        if ($crop->scaleY == -1) {
            $img->flip('v');
        }

        if ($crop->rotate != 0) {
            $img->rotate(-$crop->rotate);
        }
        $img->crop($crop->width, $crop->height, $crop->x, $crop->y);

        $destinationPath = storage_path() . '/uploads/' . $site->tag; // upload path
        $width = $img->width();
        $height = $img->height();
        $fileName = 'a_' . $media->filename;
        $img->save($destinationPath . '/' . $fileName);
        if (!empty($media->title)) {
            $title = 'cropped-' . $media->title;
        } else {
            $title = 'cropped-' . $media->filename;
        }
        $newMedia = new Media();
        $newMedia->site_id = $site->id;
        $newMedia->width = $width;
        $newMedia->height = $height;
        $newMedia->filename = $fileName;
        $newMedia->extension = $media->extension;
        $newMedia->original = $media->filename;
        $newMedia->version = 2;
        $newMedia->title = $title;
        $newMedia->parent = $media->id;
        $newMedia->external = '/image/' . $site->tag . '/' . $fileName;
        $newMedia->path = $destinationPath . '/' . $fileName;
        $newMedia->thumb = '/image/' . $site->tag . '/' . $fileName . '/200x200';
        $newMedia->mime = $media->mime;
        $newMedia->type = 'image';
        $newMedia->save();
    }

    public function resize($img, $width, $height = null)
    {
        $img->resize($width, $height, function ($constraint) use ($height) {
            if (empty($height)) {
                $constraint->aspectRatio();
            }
        });
        return $img;
    }

    public function resizeCanvas($img, $width, $height = null, $location = 'center', $relative = false, $background = '000000')
    {
        $img->resizeCanvas($width, $height, $location, $relative, $background);
        return $img;
    }

    public function fit($img, $x, $y)
    {
        $img->fit($x, $y, null, 'center');
        return $img;
    }

    public function flip($img, $flip = 'v')
    {
        $img->flip($flip);
        return $img;
    }

    public function rotate($img, $degree = '90')
    {
        $img->rotate($degree);
        return $img;
    }

    public function brightness($img, $amount = '0')
    {
        $img->brightness($amount);
        return $img;
    }

    public function contrast($img, $amount = 0)
    {
        $img->contrast($amount);
        return $img;
    }

    public function blur($img, $amount = 0)
    {
        $img->blur($amount);
        return $img;
    }

    public function greyscale($img)
    {
        $img->greyscale();
        return $img;
    }

    public function sharpen($img, $amount = 0)
    {
        $img->sharpen($amount);
        return $img;
    }

    public function gamma($img, $amount = 0)
    {
        $img->gamma($amount);
        return $img;
    }

    public function saveOrder($media, $folder){
        $order = 0;
        foreach($media as $item){
            $mediaItem = $this->find($item['id']);
            $mediaItem->order = $order;
            $mediaItem->save();
            $order = $order + 1;
        }
    }

    private function checkNewFileName($site, $fileNameMain, $extension, $level = 0)
    {
        if ($level == 0) {
            $destinationPath = storage_path() . '/uploads/' . $site->tag . '/' . $fileNameMain . '.' . $extension;
        } else {
            $destinationPath = storage_path() . '/uploads/' . $site->tag . '/' . $fileNameMain . '-' . $level . '.' . $extension;
        }
        if (file_exists($destinationPath)) {
            $level = $level + 1;
            return $this->checkNewFileName($site, $fileNameMain, $extension, $level);
        } else {
            if ($level != 0) {
                return $fileNameMain . '-' . $level;
            } else {
                return $fileNameMain;
            }
        }
    }
}