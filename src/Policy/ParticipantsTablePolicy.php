<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\ParticipantsTable;
use Authorization\IdentityInterface;

/**
 * Participant policy
 */
class ParticipantsTablePolicy
{
    public function scopeIndex(IdentityInterface $user, $query)
    {
        if ($user->group_id == ROLE_ADMIN || $user->group_id == ROLE_EDITOR) return $query; // Admin and Editor can see everything
        return $query->where(['Block.id IS' => null]); // cheap trick
    }
}
