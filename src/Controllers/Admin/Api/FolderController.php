<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Folders\FoldersInterface;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Requests\Media\FolderRequest;
use \Illuminate\Http\Request;
use \Illuminate\Translation\Translator;
use \Entrust;

class FolderController extends BaseController
{
    protected $folders;
    protected $lang;

    public function __construct(SiteInterface $site, Request $request, FoldersInterface $folders, Translator $lang)
    {
        parent::__construct($site, $request);
        $this->folders = $folders;
        $this->lang = $lang;
    }

    public function index()
    {
        return response()->json($this->folders->all($this->site));
    }

    public function show($id)
    {
        return response()->json($this->folders->find($id));
    }

    public function store(FolderRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_media'])) {
            $folders = $this->folders->create($this->site,$request->except('_token'));
            $array = [
                'folders' => $folders->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::media.notifications.folderCreated')
            ];
            return response()->json($array);
        } else {
            return response()->json(['No Permissions for managing media'], 403);
        }
    }

    public function update($id, FolderRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_media'])) {
            $folders = $this->folders->update($this->site,$id, $request->except('_token'));
            $array = [
                'folders' => $folders->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::media.notifications.folderUpdated')
            ];
            return response()->json($array);
        } else {
            return response()->json(['No Permissions for managing media'], 403);
        }

    }

    public function destroy(FolderRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_media'])) {
            $folders = $request->get('folders');
            foreach ($folders as $folder) {
                $this->folders->delete($folder['id']);
            }
            $folders = $this->folders->all($this->site);
            $array = [
                'folders' => $folders->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::media.notifications.updated')
            ];
            return Response()->json($array, 200);
        } else {
            return response()->json(['No Permissions for managing media'], 403);
        }
    }
}