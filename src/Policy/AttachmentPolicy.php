<?php

declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Cake\ORM\TableRegistry;

/**
 * Attachment policy
 */
class AttachmentPolicy implements BeforePolicyInterface
{
	private static function split_path(string $path): array
	{
		$pieces = explode('/', $path);
		if (count($pieces) != 5) {
			return [];
		}
		return [
			'sitedir' => $pieces[0],
			'model' => $pieces[1],
			'destination' => $pieces[2],
			'id' => $pieces[3],
			'field' => $pieces[4],
		];
	}

	public function before(?IdentityInterface $user, $resource, $action)
	{
		if ($user->group_id == ROLE_ADMIN) // is an admin, can do whatever
			return true;
		if (!in_array($user->group_id, ADMIN_ROLES_LIST)) // is not allowed to edit
			return false;
	}

	private function canTakeAction(IdentityInterface $user, string $path)
	{
		$path = self::split_path($path);
		if (
			$user->group_id == ROLE_ADMIN || // ADMIN can edit anything
			$user->group_id == ROLE_EDITOR // EDITOR can edit anything
		) {
			return true;
		}

		if (in_array($user->group_id, ADMIN_ROLES_LIST)) {
			if ($user->group_id == ROLE_BAM) {
				$destinations_table = TableRegistry::getTableLocator()->get('Destination');
				$destination = $destinations_table->findBySlug($path['destination']);
				if ($user->destination_id == $destination->id) {
					return true;
				}
			}

			if ($user->group_id == ROLE_COMMERCIALE || $user->group_id == ROLE_RENTER) {
				$entity_table = TableRegistry::getTableLocator()->get(ucfirst($path['model']));
				$entity = $entity_table->findById($path['id']);
				if ($user->id == $entity->user_id) {
					return true;
				}
			}
		}

		return false;
	}

	public function canUpload(IdentityInterface $user, string $path)
	{
		return $this->canTakeAction($user, $path);
	}
	public function canRemove(IdentityInterface $user, string $path)
	{
		return $this->canTakeAction($user, $path);
	}
	public function canMove(IdentityInterface $user, string $path)
	{
		return $this->canTakeAction($user, $path);
	}
}
