<?php

namespace App\Services;

/**
 * Base Service Class
 * All service classes should extend this class
 */
abstract class BaseService
{
    /**
     * Handle service operations
     */
    abstract public function handle();
}

