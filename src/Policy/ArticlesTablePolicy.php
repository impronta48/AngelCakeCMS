<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\ArticlesTable;
use Authorization\IdentityInterface;

/**
 * Articles policy
 */
class ArticlesTablePolicy
{
    public function scopeIndex(IdentityInterface $user, $query)
    {
        if ($user->group_id == 1 || $user->group_id == 9) return $query; // Admin and Editor can see everything
        if ($user->group_id == 2) return $query->where(['Articles.destination_id' => $user->destination_id]); // BAM can see its destination
        return $query->where(['Articles.user_id' => $user->id]); // by default, you can see only your stuff
    }
}
