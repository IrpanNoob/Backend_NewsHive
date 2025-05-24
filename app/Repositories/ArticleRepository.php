<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\Repository;

class ArticleRepository extends Repository
{
    protected $model = Article::class;

}
