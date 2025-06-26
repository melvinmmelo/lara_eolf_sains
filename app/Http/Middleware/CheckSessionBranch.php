<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionBranch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // if ($request->route()->getName() === 'branch-select' and session('branch_code') === null or $request->route()->getName() === 'branch.setBranchSession' or $request->route()->getName() === 'login') {
        //     return $next($request);
        // }

        if($request->route()->getName() === 'login' or $request->route()->getName() === 'branch.setBranchSession'){
            return $next($request);
        }

        if (auth()->check() and (session('branch_code') === null or session('branch_code') == '') and $request->route()->getName() !== 'branch-select'){
            return redirect()->route('branch-select');
        }

        return $next($request);

    }
}
