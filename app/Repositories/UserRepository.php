<?php

namespace App\Repositories;

use App\Abstracts\AbstractRepository;
use App\User;

class UserRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function initializeModel()
    {
        $this->model = new User();
    }
}
