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

    private function getPath($model, $destination, $id, $field)
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

    private function makeFolder($save_dir, $deleteBefore = false)
    {
        //check if $save_dir exists, if not create it
        $folder = new Folder(WWW_ROOT . $save_dir, true, 0777);
        if ($deleteBefore) {
            if ($folder->delete()) {
              // Successfully deleted foo and its nested folders
              $folder = new Folder(WWW_ROOT . $save_dir, true, 0777);
            }
        }

        $e  = $folder->errors();
        if (!empty($e)) { //$save_dir is a relative path so it is checked relatively to current working directory
            throw new Error('Si è verificato un errore nella creazione della directory. Ripetere l\'operazione - ' . $e);
        }
    }

    function uploadTemp($model, $field, $deleteBefore = false)
    {
        if (!$this->request->is(['patch', 'post', 'put'])) {
            throw new Error('Nessun file allegato');
        }

        $files = $this->request->getUploadedFiles();

        $save_dir = $this->getPath($model, 'TEMP', session_id(), $field);

        $this->makeFolder($save_dir, $deleteBefore);

        foreach ($files[$field] as $file) {
            $file->moveTo(TMP . $save_dir . DS . $file->getClientFileName()); // Will raise an exc if something goes wrong
        }

        $msg = "OK";
        $this->set(compact('msg'));
        $this->viewbuilder->serialize('msg');
    }

    function upload($model, $destination, $id, $field, $deleteBefore = false)
    {
        if (!$this->request->is(['patch', 'post', 'put'])) {
            throw new Error('Nessun file allegato');
        }

        $files = $this->request->getUploadedFiles();

        $save_dir = $this->makeFolder($model, $destination, $id, $field, $deleteBefore);

        foreach ($files[$field] as $file) {
            $file->moveTo(WWW_ROOT . $save_dir . DS . $file->getClientFileName()); // Will raise an exc if something goes wrong
        }

        $msg = "OK";
        $this->set(compact('msg'));
        $this->viewbuilder->serialize('msg');
    }


    public function removeFile($model, $destination, $id, $field, $name)
    {
        $save_dir = $this->getPath($model, $destination, $id, $field);
        if (!empty($save_dir)) {
            $fname = rtrim(WWW_ROOT . $save_dir . $name);
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
        $this->viewbuilder->serialize('msg');
    }
}
