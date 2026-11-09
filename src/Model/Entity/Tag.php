<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\AttachmentManager;
use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Tag Entity
 *
 * @property int $id
 * @property string $label
 * @property string|null $slug
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Article[] $articles
 * @property \App\Model\Entity\TagsEnhancement|null $tags_enhancement
 */
class Tag extends Entity
{

	/**
	 * Fields that can be mass assigned using newEmptyEntity() or patchEntity().
	 *
	 * Note that when '*' is set to true, this allows all unspecified fields to
	 * be mass assigned. For security purposes, it is advised to set '*' to false
	 * (or remove it), and explicitly make individual fields accessible as needed.
	 *
	 * @var array
	 */
	protected $_accessible = [
		'label' => true,
		'slug' => false,
		'created' => true,
		'modified' => true,
		'articles' => true,
		'tags_enhancement' => true,
	];


	

}