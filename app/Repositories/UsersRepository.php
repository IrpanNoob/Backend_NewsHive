<?php

namespace App\Repositories;

use App\Repositories\Repository;

class UsersRepository extends Repository
{
    protected $model = \App\Models\User::class;
}
