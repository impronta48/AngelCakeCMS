<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\UsersTable;
use Authorization\IdentityInterface;

/**
 * Users policy
 */
class UsersTablePolicy
{
    public function scopeIndex(IdentityInterface $user, $query)
    {
        if ($user->group_id == 1) return $query; // Admin can see everything
        return $query->where(['Users.id' => $user->id]);
    }
}
