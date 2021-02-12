<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Collection\Collection;
use Cake\Utility\Text;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility;

class Article extends Entity
{
  protected $_accessible = [
    '*' => true,
    'id' => false
  ];

  protected $_virtual = ['copertina', 'gallery', 'allegati'];

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
    return  Router::url('/') . $sitedir . '/articles/';
  }

  private function getDestinationSlug()
  {
    if (!empty($this->destination_id)) {
      $destinations = TableRegistry::getTableLocator()->get('Destinations');
      return $destinations->findById($this->destination_id)->first()->slug . DS;
    } else {
      return null;
    }
  }

  public function _getCopertina()
  {
    //$files = Cache::read("percorsi_first_image_$id", 'img');
    //if ($files)
    //{
    //	return $files;
    //}				
    $fullDirTemplate = Configure::read('copertina-pattern', ':sitedir/:model/:destination/:id/:field/');
    $fullDir = Text::insert($fullDirTemplate, [
      'sitedir' => Configure::read('sitedir'),
      'model' => strtolower($this->getSource()),
      'destination' => $this->getDestinationSlug(),
      'id' => $this->id,
      'field' => 'copertina',
    ]);
    $fullDir = str_replace('//', '/', $fullDir);
    $dir = new Folder(WWW_ROOT . $fullDir);
    $files = $dir->find(".*\.(jpg|jpeg|png|gif|webp)", true);
    /*Controllo*/
    if (!$files) {
      return Router::url(Configure::read('default-image', null));
    }

    $result = Router::url(str_replace(' ', '%20', $fullDir . $files[0]));
    //Cache::write("percorsi_first_image_$id", $result, 'img');
    return $result;
  }

  public function _getAllegati()
  {
    return $this->getFieldFiles('files', 'pdf|doc|xls|ppt|odt|docx|odp');
  }

  public function _getGallery()
  {
    return $this->getFieldFiles('galleria', 'jpg|jpeg|gif|png|webp');
  }

  private function getFieldFiles($fieldDir, $allowed_extensions)
  {
    //uso una cache per non leggere sul disco ogni volta
    //$files = Cache::read("percorsi_gallery_$id", 'img');
    // if ($files)
    // 	{
    // 	return $files;
    // }		
    $fullDirTemplate = Configure::read('copertina-pattern', ':sitedir/:model/:destination/:id/:field/');
    $fullDir = Text::insert($fullDirTemplate, [
      'sitedir' => Configure::read('sitedir'),
      'model' => strtolower($this->getSource()),
      'destination' => $this->getDestinationSlug(),
      'id' => $this->id,
      'field' => $fieldDir,
    ]);
    $dir = new Folder(WWW_ROOT . $fullDir);
    $files = $dir->find(".*\.($allowed_extensions)", true);
    /*Controllo se è vuoto*/
    if (!$files) {
      //Cache::write("percorsi_gallery_$id", [], 'img');
      return [];
    }

    //Aggiungo a tutti gli elementi il path assoluto		
    $files = preg_filter('/^/', Router::Url(str_replace(' ', '%20', $fullDir)), $files);
    //Cache::write("percorsi_gallery_$id", $files, 'img');
    return $files;
  }
}
