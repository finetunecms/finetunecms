<?php
namespace Finetune\Finetune\Middleware;

use Closure;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Illuminate\Contracts\Session\Session;

class SiteChecker{

    protected $site;
    protected $session;

    public function __construct(SiteInterface $site, Session $session)
    {
        $this->site = $site;
        $this->session = $session;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->session->has('site')) {
            $user = auth()->user();
            $sites = $this->site->all();
            if ($request->has('site')) {
                foreach ($sites as $site) {
                    if ($site->id == $request->get('site')) {
                        $this->session->put('site', $site);
                    }
                }
            }
            if(\Entrust::hasRole('Superadmin')){
                if (count($sites) > 1) {
                    return Redirect('/admin/sites')->with('select-site', 'true')->with('message', 'Please select a site before using this area')->with('class', 'info');
                } else {
                    $siteObj = $this->site->first();
                    $this->session->put('site', $siteObj);
                }
            }else{
                $sites = $user->sites()->count();
                if ($sites > 1) {
                    return Redirect('/admin/sites')->with('select-site', 'true')->with('message', 'Please select a site before using this area')->with('class', 'info');
                } else {
                    $siteObj = $user->sites()->first();
                    $this->session->put('site', $siteObj);
                }
            }
        }

        return $next($request);
    }

}