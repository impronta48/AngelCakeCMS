<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\AttachmentManager;
use Cake\Collection\Collection;
use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use isobutil\GoogleTranslate;

class Article extends Entity
{

	protected $_accessible = [
		'*' => true,
	];

	protected $_virtual = ['image', 'copertina', 'galleria', 'allegati'];

	protected function _getTagString()
	{
		if (isset($this->_fields['tag_string'])) {
			return $this->_fields['tag_string'];
		}
		//debug($this);
		if (empty($this->tags)) {
			return '';
		}
		$tags = new Collection($this->tags);
		$str = $tags->reduce(function ($string, $tag) {
			return $string . $tag->title . ', ';
		}, '');

		return trim($str, ', ');
	}

	//TODO: CACHE
	//Restituisce la cartella dove si trovano i percorsi per questo sito
	//Finisce con /

	public function getPath()
	{
		$sitedir = Configure::read('sitedir');

		return WWW_ROOT .  $sitedir . '/articles/';
	}

	public function getUrl()
	{
		$sitedir = Configure::read('sitedir');

		return Router::url('/') . $sitedir . '/articles/';
	}

	function getDestinationSlug()
	{
		if (!empty($this->destination_id)) {
			$destinations = TableRegistry::getTableLocator()->get('Destinations');

			return $destinations->findById($this->destination_id)->first()->slug . DS;
		} else {
			return '';
		}
	}

	public function _getImage()
	{
		$img = $this->_getCopertina();
		if (!empty($img)) return $img;
		$img = $this->_getGalleria();
		if (!empty($img)) return $img[0];
		return Router::url(Configure::read('sitedir')   . Configure::read('default-image', null));
	}

	public function _getCopertina()
	{
		$r =  $this->getFieldFiles('copertina', 'jpg|jpeg|gif|png|webp', true);
		if (empty($r)) {
			return Router::url(Configure::read('sitedir')   . Configure::read('default-image', null));
		}
		return $r;
	}

	public function _getAllegati()
	{
		return $this->getFieldFiles('allegati', 'pdf|doc|xls|ppt|odt|docx|odp|kml');
	}

	public function _getGalleria()
	{
		return $this->getFieldFiles('galleria', 'jpg|jpeg|gif|png|webp');
	}

	private function getFieldFiles($fieldDir, $allowed_extensions, $firstonly = false, $default = false)
	{
		return AttachmentManager::getFile(
			$this->getSource(),
			$this->getDestinationSlug(),
			$this->id,
			$fieldDir,
			$allowed_extensions,
			$firstonly,
			$default
		);
	}

	public function autoTranslate($field, $author_id)
	{
		$apiKey = Configure::read('GoogleTranslate.LANGUAGE_TRANSLATOR_APIKEY');
	
		$w = new GoogleTranslate($apiKey);

		$history = TableRegistry::getTableLocator()->get('AutoTranslationHistory');

		if (!empty($this->{$field})) {
			$translateInput = html_entity_decode($this->{$field});
			$res = $w->translateSentence($translateInput, 'it', 'en');
			$this->{$field} = $res;
			// traduzione ottenuta, salva nella history
			$historyLog = $history->newEmptyEntity();
			$historyLog->context = 'Article->' . $field;
			$historyLog->source = $translateInput;
			$historyLog->translation = $this->{$field};
			$historyLog->user_id = $author_id;
			$history->save($historyLog);
		}
	}


	public function autoTranslateAll($author_id)
	{
		$fields = ['title', 'body'];
		foreach ($fields as $field) {
			$this->autoTranslate($field, $author_id);
		}
		return $this;
	}
}
