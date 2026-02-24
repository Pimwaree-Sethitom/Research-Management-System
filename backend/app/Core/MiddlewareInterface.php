<?php

namespace RMS\Backend\Core;

interface MiddlewareInterface
{
    public function handle(callable $next);
}