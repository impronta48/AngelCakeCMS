<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Utility\Text;
use Cake\Filesystem\Folder;
use \Error;

/**
 * Attachments Controller
 *
 * @method \App\Model\Entity\Attachment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AttachmentsController extends AppController
{

  static function getPath($model, $destination, $id, $field = '')
  {
    $fullDirTemplate = Configure::read('copertina-pattern', ':sitedir/:model/:destination/:id/:field/');

    $save_dir = Text::insert($fullDirTemplate, [
      'sitedir' => Configure::read('sitedir'),
      'model' => strtolower($model),
      'destination' => strtolower($destination),
      'id' => $id,
      'field' => $field,
    ]);

    // TODO do this in a nicer way!
    $save_dir = str_replace("//", "/", $save_dir);
    $save_dir = str_replace("cyclomap.", "", $save_dir);

    return $save_dir;
  }

  private function makeFolder($save_dir, $deleteBefore = false, $temporary = false)
  {
    //check if $save_dir exists, if not create it
    $folder = new Folder(($temporary ? TMP : WWW_ROOT) . $save_dir, true, 0777);
    if ($deleteBefore) {
      if ($folder->delete()) {
        // Successfully deleted foo and its nested folders
        $folder = new Folder(($temporary ? TMP : WWW_ROOT) . $save_dir, true, 0777);
      }
    }

    $e  = $folder->errors();
    if (!empty($e)) { //$save_dir is a relative path so it is checked relatively to current working directory
      throw new Error('Si è verificato un errore nella creazione della directory. Ripetere l\'operazione - ' . $e);
    }
  }


  function upload($model, $destination, $id, $field, $deleteBefore = false, $temporary = false)
  {
    $this->autoRender = false;
    if (!$this->request->is(['patch', 'post', 'put'])) {
      throw new Error('Nessun file allegato');
    }

    $files = $this->request->getUploadedFiles();

    $save_dir = AttachmentsController::getPath($model, $destination, $id, $field);

    $this->makeFolder($save_dir, $deleteBefore, $temporary);

    if (is_array($files[$field])) { // multi-file upload
      foreach ($files[$field] as $file) {
        $file->moveTo(($temporary ? TMP : WWW_ROOT) . $save_dir . DS . $file->getClientFileName()); // Will raise an exc if something goes wrong
      }
    } else {
      $files[$field]->moveTo(($temporary ? TMP : WWW_ROOT) . $save_dir . DS . $files[$field]->getClientFileName()); // Will raise an exc if something goes wrong
    }

    $msg = "OK";
    $this->set(compact('msg'));
    $this->viewBuilder()->serialize('msg');
  }

  function uploadTemp($model, $field, $deleteBefore = false)
  {
    $this->upload($model, 'TEMP', session_id(), $field, $deleteBefore, true);
  }


  public function remove($model, $destination, $id, $field, $name, $temporary = false)
  {
    $this->autoRender = false;
    $save_dir = AttachmentsController::getPath($model, $destination, $id, $field);
    if (!empty($save_dir)) {
      $fname = rtrim(($temporary ? TMP : WWW_ROOT) . $save_dir . $name);
      if (file_exists($fname)) {
        $ip = $_SERVER['REMOTE_ADDR'];
        //TODO: devo cancellare lo stesso nome file anche in tutte le altre cartelle figlie

        unlink($fname);
        $this->log("eliminato il file $fname da $ip");
      } else {
        throw new Error('Nessun file da cancellare');
      }
    } else {
      throw new Error('Nessun path fornito');
    }

    $msg = "OK";
    $this->set(compact('msg'));
    $this->viewBuilder()->serialize('msg');
  }

  public function removeTemp($model, $field, $name)
  {
    $this->remove($model, 'TEMP', session_id(), $field, $name, true);
  }
}
