<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\AttachmentManager;
use Cake\ORM\Entity;

/**
 * TagsEnhancement Entity
 *
 * @property int $id
 * @property int $tag_id
 * @property string|null $alt_name
 * @property string|null $color
 * @property string|null $image
 *
 * @property \App\Model\Entity\Tag $tag
 */
class TagsEnhancement extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'tag_id' => true,
        'alt_name' => true,
        'color' => true,
        'image' => true,
        'tag' => true,
    ];


    protected $_virtual = ['image'];


	public function _getImage()
	{
		$img = $this->getFieldFiles('tag', 'jpg|jpeg|gif|png|webp', $firstonly = true);
	
		return $img;
	}

	private function getFieldFiles($fieldDir, $allowed_extensions, $firstonly = false, $default = false)
	{
		return AttachmentManager::getFile(
			$this->getSource(),
			null,
			$this->id,
			$fieldDir,
			$allowed_extensions,
			$firstonly,
			$default
		);
	}
}
