<?php

namespace Finetune\Finetune\Repositories\Folders;

use Finetune\Finetune\Entities\Folders;
use Finetune\Finetune\Repositories\Media\MediaRepository;
use Finetune\Finetune\Repositories\Helper\HelperInterface;

class FoldersRepository implements FoldersInterface
{

    protected $helper;

    public function __construct(HelperInterface $helper)
    {
        $this->helper = $helper;
    }


    public function all($site)
    {
        return Folders::with('media', 'media.folders')->where('site_id', '=', $site->id)->get();
    }

    public function find($id)
    {
        if ($id == 'main') {
            $media = new MediaRepository();
            $mediaItems = $media->all()->filter(function ($value, $key) {
                if ($value->folder == null) {
                    return true;
                } else {
                    return false;
                }
            });
            $folder = [
                'title' => 'Main Folder',
                'tag' => 'main-folder',
                'media' => $mediaItems
            ];
            return collect($folder);
        } else {
            return Folders::find($id);
        }

    }

    public function create($site, $request)
    {
        $request['tag'] = $this->buildTag($request['title'], $request['tag']);
        $folder = new Folders();
        $folder->fill($request);
        $folder->site_id = $site->id;
        $folder->save();
        return $this->all($site);
    }

    public function update($site, $id, $request)
    {
        $request['tag'] = $this->buildTag($request['title'], $request['tag']);
        $folder = $this->find($id);
        $folder->fill($request);
        $folder->site_id = $site->id;
        $folder->save();
        return $this->all($site);
    }

    public function destroy($site, $id)
    {
        $folder = $this->find($id);
        if (!empty($folder)) {
            $folder->delete();
        }
        return $this->all($site);
    }

    private function buildTag($title, $tag)
    {
        if (!empty($tag)) {
            return $this->helper->buildTag($tag);
        } else {
            return $this->helper->buildTag($title);
        }
    }
}