<?php

namespace App\Repositories;

use App\Abstracts\AbstractRepository;
use App\Models\PasswordReset;

class PasswordResetRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function initializeModel()
    {
        $this->model = new PasswordReset();
    }
}
