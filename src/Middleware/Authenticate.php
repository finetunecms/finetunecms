<?php namespace Finetune\Finetune\Middleware;

use Closure;
use \Illuminate\Contracts\Auth\Guard;

/**
 * Class Authenticate
 * @package App\Http\Middleware
 */
class Authenticate
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;


    /**
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('auth/login');
            }
        } else {
            if ($request->user()) {
                if(!$request->user()->ability(['Superadmin'], ['can_administer_website'])){
                    return response()->view('finetune::errors.unauth');
                }else{
                    return $next($request);
                }
            }
        }
        return $next($request);
    }
}