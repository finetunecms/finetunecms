<?php
namespace Finetune\Finetune\Middleware;

use Closure;

class Restrict{

    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        $ips = config('ips');
        if(!empty($ips)){
            $userIp = $request->ip();
            if(in_array($userIp, $ips)){
                return $next($request);
            }else{
                return redirect('/');
            }
        }else{
            return $next($request);
        }
    }
}