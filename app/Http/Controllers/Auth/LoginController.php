<?php

namespace App\Http\Controllers\Auth;

use App\Business\AuthBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use App\Http\Resources\AuthJsonResource;
use App\Http\Resources\CustomJsonResource;

class LoginController extends Controller
{


    public function __construct(private AuthBusiness $authBusiness)
    {
    }
    public function login(LoginFormRequest $request): CustomJsonResource
    {
        return new AuthJsonResource(
            $this->authBusiness->loginAttempt($request),
            'Login Success',
        );
    }

    public function logout()
    {

    }
}
