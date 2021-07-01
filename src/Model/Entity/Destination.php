<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Controller\AttachmentsController;
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

	protected $_virtual = ['safeCopertina', 'copertina', 'url'];

	public function _getUrl() {
		$locale = I18n::getLocale();
		$lang = Configure::read('LocaleMappings')[Configure::read('App.defaultLocale')];
		if ($locale === $lang) {
			return "/{$this->slug}";
		}
		return "/{$locale}/{$this->slug}";
	}

	public function _getSafeCopertina() {
		$img = $this->_getCopertina();
		if (empty($img)) {
			$img = Router::url(Configure::read('default-image', null));
		}
		return $img;
	}

	public function _getCopertina() {
		$fullDir = AttachmentsController::getPath('Destinations', $this->slug, $this->id, 'copertina');

		$dir = new Folder(WWW_ROOT . $fullDir);
		$files = $dir->find(".*\.(jpg|jpeg|gif|png|webp)", true);

		/*Controllo se è vuoto*/
		if (!$files) {
			return '';
		}

		if (is_array($files)) {
			if (empty($files)) {
				return '';
			}
			$files = $files[0];
		}

		$asd = preg_filter('/^/', Router::Url(str_replace(' ', '%20', $fullDir)), $files);
		return $asd;
	}
}
