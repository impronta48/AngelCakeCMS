<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Lib\AttachmentManager;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Text;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use \Error;

/**
 * Attachments Controller
 *
 * @method \App\Model\Entity\Attachment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AttachmentsController extends AppController
{

  private function authorize($model, $id) {
		$entity_table = TableRegistry::getTableLocator()->get(ucfirst($model));
		$entity = $entity_table->findById($id)->firstOrFail();
    $this->Authorization->authorize($entity, 'edit');
  }

  function move($model, $destination, $id, $field, $fname) {
    $this->authorize($model, $id);
    $old_path = AttachmentManager::buildPath($model, $destination, $id, $field);

    $new_model = $this->request->getQuery('model') ?: $model;
    $new_destination = $this->request->getQuery('destination') ?: $destination;
    $new_id = $this->request->getQuery('id') ?: $id;
    $new_field = $this->request->getQuery('field') ?: $field;
    $new_fname = $this->request->getQuery('fname') ?: $fname;
    $this->authorize($new_model, $new_id);
    $new_path = AttachmentManager::buildPath($new_model, $new_destination, $new_id, $new_field);

    AttachmentManager::renameFile($old_path . DS . $fname, $new_path . DS . $new_fname);
  }

  function upload($model, $destination, $id, $field, $temporary = false, $deleteBefore = false)
  {
    $this->viewBuilder()->setOption('serialize', true);
    $this->RequestHandler->renderAs($this, 'json');

    if (!$this->request->is(['patch', 'post', 'put'])) {
      $this->response = $this->response->withStatus(500);
      $this->set(['error' => 'no files']);
      return;
    }

    $this->authorize($model, $id);

    $files = $this->request->getUploadedFiles();

    $res = AttachmentManager::putFile($files, $model, $destination, $id, $field, $temporary, $deleteBefore);

    $this->set($res);

    if (isset($res['error'])) {
      $this->response = $this->response->withStatus(500);
      return;
    } 
  }

  function remove($model, $destination, $id, $field, $name, $temporary = false)
  {
    $this->authorize($model, $id);

    $this->viewBuilder()->setOption('serialize', true);
    $this->RequestHandler->renderAs($this, 'json');

    $res = AttachmentManager::popFile($model, $destination, $id, $field, $name, $temporary);

    $this->set($res);

    if (isset($res['error'])) {
      $this->response = $this->response->withStatus(500);
      return;
    } 
  }
}
