<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\AttachmentManager;
use Cake\ORM\Entity;

/**
 * Block Entity
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $body
 */
class Block extends Entity
{

	/**
	 * Fields that can be mass assigned using newEntity() or patchEntity().
	 *
	 * Note that when '*' is set to true, this allows all unspecified fields to
	 * be mass assigned. For security purposes, it is advised to set '*' to false
	 * (or remove it), and explicitly make individual fields accessible as needed.
	 *
	 * @var array
	 */
	protected $_accessible = [
		'title' => true,
		'body' => true,
	];

	protected $_virtual = ['allegati'];

	public function _getAllegati()
	{
		return AttachmentManager::getFile(
			$this->getSource(),
			null,
			$this->id,
			'allegati',
			'jpg|jpeg|png|gif|webp'
		);
	}
}
