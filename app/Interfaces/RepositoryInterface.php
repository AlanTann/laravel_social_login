<?php

namespace App\Interfaces;

interface RepositoryInterface
{
    /**
     * Initialize eloquent model.
     *
     * @return void
     */
    public function initializeModel();
}
