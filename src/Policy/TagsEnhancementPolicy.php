<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\TagsEnhancement;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;

/**
 * TagsEnhancement policy
 */
class TagsEnhancementPolicy implements BeforePolicyInterface
{
    public function before($user, $resource, $action)
    {
        if ($user->group_id == ROLE_ADMIN) // is an admin, can do whatever
            return true;
        if (!in_array($user->group_id, [ROLE_ADMIN])) // is not allowed to edit
            return false;
    }

    public function canAdd(IdentityInterface $user, TagsEnhancement $resource)
    {
        return false;
    }

    public function canEdit(IdentityInterface $user, TagsEnhancement $resource)
    {
        return false;
    }

    public function canDelete(IdentityInterface $user, TagsEnhancement $resource)
    {
        return false;
    }

    public function canView(IdentityInterface $user, TagsEnhancement $resource)
    {
        return false;
    }
}
