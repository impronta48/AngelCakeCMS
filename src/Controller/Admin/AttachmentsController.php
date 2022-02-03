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

  private function authorize($comp_model, $id) {
		$entity_table = TableRegistry::getTableLocator()->get($comp_model);
    $entity = null;
    if (is_numeric($id)) {
		  $entity = $entity_table->findById($id)->first();
    }
    if (empty($entity)) {
      $entity = $entity_table->newEntity(['id' => $id]);
    }
    $this->Authorization->authorize($entity, 'edit');
  }

  function move($comp_model, $destination, $id, $field, $fname) {
    $this->authorize($comp_model, $id);
    $tmp = explode('.', $comp_model, 2);
    $model = end($tmp);
    $old_path = AttachmentManager::buildPath($model, $destination, $id, $field);

    $new_model = $this->request->getQuery('model') ?: $model;
    $new_destination = $this->request->getQuery('destination') ?: $destination;
    $new_id = $this->request->getQuery('id') ?: $id;
    $new_field = $this->request->getQuery('field') ?: $field;
    $new_fname = $this->request->getQuery('fname') ?: $fname;
    $this->authorize($new_model, $new_id);
    $new_path = AttachmentManager::buildPath($new_model, $new_destination, $new_id, $new_field);

    AttachmentManager::renameFile($old_path, $new_path, $fname, $new_fname);
  }

  function upload($comp_model, $destination, $id, $field, $temporary = false, $deleteBefore = false)
  {
    $this->viewBuilder()->setOption('serialize', true);
    $this->RequestHandler->renderAs($this, 'json');

    if (!$this->request->is(['patch', 'post', 'put'])) {
      $this->response = $this->response->withStatus(500);
      $this->set(['error' => 'no files']);
      return;
    }

    $this->authorize($comp_model, $id);
    $tmp = explode('.', $comp_model, 2);
    $model = end($tmp);

    $files = $this->request->getUploadedFiles();

    $res = AttachmentManager::putFile($files, $model, $destination, $id, $field, $temporary, $deleteBefore);

    $this->set($res);

    if (isset($res['error'])) {
      $this->response = $this->response->withStatus(500);
      return;
    } 
  }

  function remove($comp_model, $destination, $id, $field, $name, $temporary = false)
  {
    $this->authorize($comp_model, $id);
    $tmp = explode('.', $comp_model, 2);
    $model = end($tmp);

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
