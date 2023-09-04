<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\StaticModel;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;

/**
 * Static policy
 */
class StaticPolicy implements BeforePolicyInterface
{
    public function before(?IdentityInterface $user, $resource, $action)
    {
        if ($user->group_id == ROLE_ADMIN) // is an admin, can do whatever
            return true;
        if (!in_array($user->group_id, ADMIN_ROLES_LIST)) // is not allowed to edit
            return false;
    }


    /**
     * Check if $user can add Article
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Article $article
     * @return bool
     */
    public function canGetWebdav(IdentityInterface $user, StaticModel $article)
    {
        if (!in_array($user->group_id, ADMIN_ROLES_LIST)) // is not allowed to edit
            return false;
        return true;
    }

}
