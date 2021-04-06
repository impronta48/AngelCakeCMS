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

    private function makeFolder($model, $destination, $id, $field, $deleteBefore = false)
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

        return $save_dir;
    }

    function uploadTemp($model, $field, $deleteBefore = false)
    {
        if (!$this->request->is(['patch', 'post', 'put'])) {
            throw new Error('Nessun file allegato');
        }

        $files = $this->request->getUploadedFiles();

        $save_dir = $this->makeFolder($model, 'TEMP', session_id(), $field, $deleteBefore);

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

    /**
     * Delete method
     *
     * @param string|null $id Attachment id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $attachment = $this->Attachments->get($id);
        if ($this->Attachments->delete($attachment)) {
            $this->Flash->success(__('The attachment has been deleted.'));
        } else {
            $this->Flash->error(__('The attachment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
