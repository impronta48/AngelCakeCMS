<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\Utility\Text;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;

/**
 * Destination Entity
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 *
 * @property \App\Model\Entity\Event[] $events
 */
class Destination extends Entity
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
		'*' => true,
	];

	protected $_virtual = ['copertina', 'url'];

	public function _getUrl() {
		$locale = I18n::getLocale();
		$lang = Configure::read('LocaleMappings')[Configure::read('App.defaultLocale')];
		if ($locale === $lang) {
			return "/{$this->slug}";
		}
		return "/{$locale}/{$this->slug}";
	}

	public function _getCopertina() {
		$fullDirTemplate = ':sitedir/:model/';
		$fullDir = Text::insert($fullDirTemplate, [
			'sitedir' => Configure::read('sitedir'),
			'model' => 'destinations',
		]);

		// TODO: do this in a nicer way!
		$fullDir = str_replace("//", "/", $fullDir);
		$fullDir = str_replace("cyclomap.", "", $fullDir);

		$dir = new Folder(WWW_ROOT . $fullDir);
		$files = $dir->find("{$this->slug}\.(jpg|jpeg|gif|png|webp)", true);

		if (is_array($files)) {
			$files = $files[0];
		}

		/*Controllo se è vuoto*/
		if (!$files) {
			return Router::url(Configure::read('default-image', null));
		}

		$asd = preg_filter('/^/', Router::Url(str_replace(' ', '%20', $fullDir)), $files);
		return $asd;
	}
}
