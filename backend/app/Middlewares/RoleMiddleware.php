<?php

namespace RMS\Backend\Middleware;

use RMS\Backend\Utils\Response;

class RoleMiddleware
{
    private array $requiredRoles;

    public function __construct(array $requiredRoles)
    {
        $this->requiredRoles = $requiredRoles;
    }

    public function handle(): void
    {
        $user = $_REQUEST['user'] ?? null;

        if (!$user) {
            Response::error("Unauthorized", 401);
            exit;
        }

        $userRoles = $user['roles'] ?? [];

        $hasAccess = false;

        foreach ($this->requiredRoles as $role) {
            if (in_array($role, $userRoles)) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            Response::error("Forbidden - insufficient role", 403);
            exit;
        }
    }
}