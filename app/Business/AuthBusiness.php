<?php

namespace App\Business;

use App\Business\Business;
use App\Http\Requests\LoginFormRequest;
use App\Repositories\UsersRepository;
use InvalidArgumentException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\UnauthorizedException;
use League\Config\Exception\ValidationException;

class AuthBusiness extends Business
{

    public function __construct(private readonly UsersRepository $usersRepository)
    {

    }
    public function loginAttempt(LoginFormRequest $request): array
    {
        $result = [
          'token' => null,
        ];

        $user = $this->usersRepository->findBy($request->get('email'),'email')->firstOrFail();
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw new UnauthorizedException('Wrong email or password.');
        }
        $result['token'] = $user->createToken('auth')->plainTextToken;
        return $result;
    }
}
