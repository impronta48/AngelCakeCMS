<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Tag;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;

/**
 * User policy
 */
class TagPolicy implements BeforePolicyInterface
{
    public function before($user, $resource, $action)
    {
        if ($user->group_id == ROLE_ADMIN) // is an admin, can do whatever
            return true;
        if (!in_array($user->group_id, [ROLE_ADMIN])) // is not allowed to edit
            return false;
    }

    /**
     * Check if $user can add User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Tag $resource)
    {
        return false;
    }

    /**
     * Check if $user can edit User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Tag $resource)
    {
        return false;
    }

    /**
     * Check if $user can delete User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Tag $resource)
    {
        return false;
    }

    /**
     * Check if $user can view User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canView(IdentityInterface $user, Tag $resource)
    {
        return true; // all users can view tags
    }
}
