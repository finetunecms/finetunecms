<?php
namespace Finetune\Finetune\Repositories\Site;

use Finetune\Finetune\Entities\Site;

use Illuminate\Contracts\Session\Session;

class SiteRepository implements SiteInterface
{
    protected $session;
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function all()
    {
            return Site::all();
    }

    public function find($id)
    {
        return Site::whereNull('deleted_at')->find($id);
    }

    public function findSiteByDomain($domain)
    {
        $parsedDomain = parse_url($domain);
        $url = preg_replace('#^www\.(.+\.)#i', '$1', $parsedDomain['host']);
        $site = Site::where('domain', '=', $url)->whereNull('deleted_at')->first();
        if(!empty($site)){
            return $site;
        }else{
            return $this->first();
        }
    }

    public function first()
    {
        return Site::whereNull('deleted_at')->first();
    }

    public function getSite($request)
    {
        if ($this->session->has('site')) {
            $site = $this->session->get('site');
            if ($site->domain != $request->root()) {
                if ($request->segment(1) == 'admin') {
                    return $this->session->get('site');
                } else {
                    return $this->findSiteByDomain($request->url());
                }
            } else {
                return $this->session->get('site');
            }
        } else {

            return $this->findSiteByDomain($request->url());
        }
    }

    public function update($id, $input)
    {
        $site = $this->find($id);
        $site->fill($input);
        $site->save();
        return $this->all();
    }

    public function create($input)
    {
        $site = new Site();
        $site->fill($input);
        $site->save();
        return $this->all();
    }

    public function destroy($id)
    {
        $site = $this->find($id);
        if (!empty($site)) {
            $site->delete();
        }
        return $this->all();
    }
}