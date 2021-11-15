<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Participant;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;

/**
 * Participant policy
 */
class ParticipantPolicy implements BeforePolicyInterface
{
    public function before(?IdentityInterface $user, $resource, $action)
    {
        if ($user->group_id == 1) // is an admin, can do whatever
            return true;
        if (!in_array($user->group_id, [1,2,3,6,9])) // is not allowed to edit
            return false;
    }

    /**
     * Check if $user can add Participant
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Participant $participant
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Participant $participant)
    {
        return false;
    }

    /**
     * Check if $user can edit Participant
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Participant $participant
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Participant $participant)
    {
        return false;
    }

    /**
     * Check if $user can delete Participant
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Participant $participant
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Participant $participant)
    {
        return false;
    }

    /**
     * Check if $user can view Participant
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Participant $participant
     * @return bool
     */
    public function canView(IdentityInterface $user, Participant $participant)
    {
        return false;
    }
}
