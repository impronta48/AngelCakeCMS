<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Destination;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;

/**
 * Destination policy
 */
class DestinationPolicy implements BeforePolicyInterface
{
    public function before(?IdentityInterface $user, $resource, $action)
    {
        if ($user->group_id == ROLE_ADMIN) // is an admin, can do whatever
            return true;
        if ($user->group_id == ROLE_EDITOR && 
                ($user->destination_id == $resource->id || $user->destination_id == null)) // is an admin, can do whatever
            return true;
            
        return false;
    }

    /**
     * Check if $user can add Destination
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Destination $destination
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Destination $destination)
    {
        return false;
    }

    /**
     * Check if $user can edit Destination
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Destination $destination
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Destination $destination)
    {
        return false;
    }

    /**
     * Check if $user can delete Destination
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Destination $destination
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Destination $destination)
    {
        return false;
    }

    /**
     * Check if $user can view Destination
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Destination $destination
     * @return bool
     */
    public function canView(IdentityInterface $user, Destination $destination)
    {
        return true; // everybody can see a Destination
    }

    public function canPrenota(IdentityInterface $user, Destination $destination)
    {
        return true;
    }
}
