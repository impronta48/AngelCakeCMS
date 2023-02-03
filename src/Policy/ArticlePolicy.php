<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Article;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;

/**
 * Article policy
 */
class ArticlePolicy implements BeforePolicyInterface
{
    public function before(?IdentityInterface $user, $resource, $action)
    {
        if ($user->group_id == ROLE_ADMIN) // is an admin, can do whatever
            return true;
        if (!in_array($user->group_id, ADMIN_ROLES_LIST)) // is not allowed to edit
            return false;
    }

    private function canTakeAction(IdentityInterface $user, Article $article)
    {
        if (
            $user->group_id == ROLE_ADMIN || // ADMIN can edit anything
            $user->group_id == ROLE_EDITOR || // EDITOR can edit anything
            ( isset($article->destination_id) && $user->group_id == ROLE_BAM && $user->destination_id == $article->destination_id ) || // BAM can only edit in its destination
            ( isset($article->user_id) && in_array($user->group_id, [ROLE_RENTER, ROLE_COMMERCIALE]) && $user->id == $article->user_id ) // Renter/Commerciale can only edit his entries
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if $user can add Article
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Article $article
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Article $article)
    {
        return $this->canTakeAction($user, $article);
    }

    /**
     * Check if $user can edit Article
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Article $article
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Article $article)
    {
        return $this->canTakeAction($user, $article);
    }

    /**
     * Check if $user can delete Article
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Article $article
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Article $article)
    {
        return $this->canTakeAction($user, $article);
    }

    /**
     * Check if $user can view Article
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Article $article
     * @return bool
     */
    public function canView(IdentityInterface $user, Article $article)
    {
        if (
            $article->published ||
            (isset($article->user_id) && $article->user_id == $user->id) ||
            (isset($article->destination_id) && $article->destination_id == $user->destination_id)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if $user can manage attachments of Article
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Article $article
     * @return bool
     */
    public function canUpload(IdentityInterface $user, Article $article)
    {
        if (in_array($user->group_id, ADMIN_ROLES_LIST)) {
            if (!is_null($article)) {
                return $this->canEdit($user, $article);
            }
            return true;
        }
        return false;
    }

        /**
     * Check if $user can manage attachments of Article
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Article $article
     * @return bool
     */
    public function canMoveAttachment(IdentityInterface $user, Article $article)
    {
        if (in_array($user->group_id, ADMIN_ROLES_LIST)) {
            if (!is_null($article)) {
                return $this->canEdit($user, $article);
            }
            return true;
        }
        return false;
    }
}
