<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

abstract class ApiBaseController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public ?User $authUser;

    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            /** @var User|null $user */
            $user = $request->user('sanctum');

            $this->authUser = $user;

            return $next($request);
        });
    }
}
