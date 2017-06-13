<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Requests\Media\MediaRequest;
use Finetune\Finetune\Repositories\Folders\FoldersInterface;
use Finetune\Finetune\Repositories\Media\MediaInterface;
use \Illuminate\Http\Request as NormalRequest;
use \Illuminate\Translation\Translator;
use \Entrust;


class MediaController extends BaseController
{
    protected $media;
    protected $folders;
    protected $lang;

    public function __construct(SiteInterface $site, NormalRequest $request, MediaInterface $media, FoldersInterface $folders, Translator $lang)
    {
        parent::__construct($site, $request);
        $this->media = $media;
        $this->folders = $folders;
        $this->lang = $lang;
    }

    public function index()
    {
        return response()->json($this->media->all($this->site));
    }

    public function show($id)
    {
        $folder = $this->folders->find($id);
        return response()->json($folder->media()->get());
    }

    public function store(MediaRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_media'])) {
            $medias = $this->media->create($this->site, $request);
            $array = [
                'media' => $medias->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::media.notifications.updated')
            ];
            return response()->json($array);
        } else {
            return response()->json(['No Permissions for managing media'], 403);
        }
    }

    public function update($id, MediaRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_media'])) {
            $media = $request->get('media');
            if (!empty($media['filename'])) {
                $this->media->update($this->site, $id, $media);
                $array = [
                    'alertType' => 'success',
                    'alertMessage' => $this->lang->trans('finetune::media.notifications.updated')
                ];
                return response()->json($array);
            } else {
                $array = [
                    'alertType' => 'danger',
                    'alertMessage' => 'Filename is required'
                ];
                return response()->json($array);
            }
        } else {
            return response()->json(['No Permissions for managing media'], 403);
        }

    }

    public function destroy(MediaRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_media'])) {
            $medias = $request->get('media');
            foreach ($medias as $media) {
                $this->media->destroy($media);
            }
            $array = [
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::media.notifications.deleted')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing media'], 403);
        }
    }

    public function move(NormalRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_media'])) {
            $this->validate($request, [
                'media' => 'required',
                'folders' => 'required',

            ]);
            $medias = $request->get('media');
            $folders = $request->get('folders');

            foreach ($medias as $media) {
                $obj = $this->media->find($media['id']);
                $this->media->move($obj, $folders);
            }

            $array = [
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::media.notifications.updated')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing media'], 403);
        }
    }

    public function getMediaOptions()
    {
        $mediaArray = config('media');
        return Response()->json($mediaArray, 200);
    }

    public function order(NormalRequest $request){
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_media'])) {
            $this->validate($request, [
                'media' => 'required',
                'folder' => 'required',
            ]);
            $medias = $request->get('media');
            $folder = $request->get('folder');

            $this->media->saveOrder($medias, $folder);

            $array = [
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::media.notifications.updated')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing media'], 403);
        }
    }
}