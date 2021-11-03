<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\DestinationsTable;
use Authorization\IdentityInterface;

/**
 * Destinations policy
 */
class DestinationsTablePolicy
{
    public function scopeIndex(IdentityInterface $user, $query)
    {
        if ($user->group_id == 1 || $user->group_id == 9) return $query; // Admin and Editor can see everything
        if ($user->group_id == 2) return $query->where(['Destinations.id' => $user->destination_id]); // BAM can see its destination
        return $query->where(['Destination.id IS' => null]); // Jank way to filter them all out
    }
}
