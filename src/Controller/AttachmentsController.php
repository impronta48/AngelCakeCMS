<?php

declare(strict_types=1);

namespace App\Controller;

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
	// Returns a file size limit in bytes based on the PHP upload_max_filesize
	// 		and post_max_size
	// Code originally from Drupal
	static function file_upload_max_size() {
		static $max_size = -1;

		if ($max_size < 0) {
			// Start with post_max_size.
			$post_max_size = AttachmentsController::parse_size(ini_get('post_max_size'));
			if ($post_max_size > 0) {
				$max_size = $post_max_size;
			}

			// If upload_max_size is less, then reduce. Except if upload_max_size is
			// zero, which indicates no limit.
			$upload_max = AttachmentsController::parse_size(ini_get('upload_max_filesize'));
			if ($upload_max > 0 && $upload_max < $max_size) {
				$max_size = $upload_max;
			}
		}
		return $max_size;
	}

	static private function parse_size($size) {
		$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
		$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
		if ($unit) {
			// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
			return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
		}
		else {
			return round($size);
		}
	}
	// End of code from Drupal

  static private function replace_extension($fname)
  {
    $split = explode('.', $fname);
    $ext = array_pop($split);
    if (in_array($ext, ['.png', '.gif', '.jpeg', '.bmp'])) {
      return Text::slug(implode('.', $split)) . '.jpg';
    }
    return Text::slug(implode('.', $split)) . '.' . $ext;
  }

  
  static function getPath($model, $destination, $id, $field = '')
  {
    $fullDirTemplate = Configure::read('copertina-pattern', ':sitedir/:model/:destination/:id/:field/');

    $save_dir = Text::insert($fullDirTemplate, [
      'sitedir' => Configure::read('sitedir'),
      'model' => empty($model) ? 'attachments' : strtolower($model),
      'destination' => empty($destination) ? 'none' : strtolower($destination),
      'id' => empty($id) ? -1 : $id,
      'field' => empty($field) ? 'dropzone' : $field,
    ]);

    // TODO do this in a nicer way!
    $save_dir = str_replace("//", "/", $save_dir);
    $save_dir = str_replace("cyclomap.", "", $save_dir);

    return $save_dir;
  }

  static function getFieldFiles($model, $destination, $id, $field, $allowed_extensions, $firstonly = false, $default = null)
  {
    //uso una cache per non leggere sul disco ogni volta
    //$files = Cache::read("percorsi_gallery_$id", 'img');
    // if ($files)
    //  {
    //  return $files;
    // }
    $fullDirTemplate = Configure::read('copertina-pattern', ':sitedir/:model/:destination/:id/:field/');
    $fullDir = Text::insert($fullDirTemplate, [
      'sitedir' => Configure::read('sitedir'),
      'model' => strtolower($model),
      'destination' => $destination,
      'id' => $id,
      'field' => $field,
    ]);

    // TODO: do this in a nicer way!
    $fullDir = str_replace("//", "/", $fullDir);
    $fullDir = str_replace("cyclomap.", "", $fullDir);

    $dir = new Folder(WWW_ROOT . $fullDir);
    $files = $dir->find(".*\.($allowed_extensions)", true);

    /*Controllo se è vuoto*/
    if (!$files) {
      if (!empty($default)) {
        return Router::url($default);
      }
      if ($firstonly) {
        return null;
      }

      return [];
    }

    if ($firstonly) {
      $files = $files[0];
    }

    return preg_filter('/^/', Router::Url(str_replace(' ', '%20', $fullDir)), $files);
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


  function upload($model, $destination, $id, $field, $temporary = false, $deleteBefore = false)
  {
    $this->viewBuilder()->setOption('serialize', true);
    $this->RequestHandler->renderAs($this, 'json');

    if (!$this->request->is(['patch', 'post', 'put'])) {
      $this->response = $this->response->withStatus(500);
      $this->set(['error' => 'no files']);
      return;
    }

    $errors = Configure::read('phpFileUploadErrors');

    $files = $this->request->getUploadedFiles();

    $save_dir = self::getPath($model, $destination, $id, $field);

    $this->makeFolder($save_dir, $deleteBefore, $temporary);

    if (array_key_exists($field, $files)) {
      if (is_array($files[$field])) { // multi-file upload
        foreach ($files[$field] as $n => $file) {
          $err = $file->getError();
          if ($err == 0) {
            $fname = self::replace_extension($file->getClientFileName());
            $file->moveTo(($temporary ? TMP : WWW_ROOT) . $save_dir . DS . $fname); // Will raise an exc if something goes wrong
            $this->set(["upload$n" => 'OK']);
          } else {
            $this->response = $this->response->withStatus(500);
            $this->set(['error' => $errors[$err]]);
            return;
          }
        }
      } else {
        $err = $files[$field]->getError();
        if ($err == 0) {
          $fname = self::replace_extension($files[$field]->getClientFileName());
          $files[$field]->moveTo(($temporary ? TMP : WWW_ROOT) . $save_dir . DS . $fname); // Will raise an exc if something goes wrong
          $this->set(['upload' => 'OK']);
        } else {
          $this->response = $this->response->withStatus(500);
          $this->set(['error' => $errors[$err]]);
          return;
        }
      }
    } else {
      $this->response = $this->response->withStatus(500);
      $this->set(['error' => 'no file uploaded']);
      return;
    }
  }

  public function remove($model, $destination, $id, $field, $name, $temporary = false)
  {
    $this->viewBuilder()->setOption('serialize', true);
    $this->RequestHandler->renderAs($this, 'json');

    $name = self::replace_extension($name);

    $save_dir = self::getPath($model, $destination, $id, $field);
    if (!empty($save_dir)) {
      $fname = rtrim(($temporary ? TMP : WWW_ROOT) . $save_dir . $name);
      if (file_exists($fname)) {
        $ip = $_SERVER['REMOTE_ADDR'];
        //TODO: devo cancellare lo stesso nome file anche in tutte le altre cartelle figlie

        unlink($fname);
        $this->log("eliminato il file $fname da $ip");
        $this->set(['removed' => 'OK']);
      } else {
        $this->set(['warning' => "file doesn't exist on server"]);
        return;
      }
    } else {
      $this->response = $this->response->withStatus(500);
      $this->set(['error' => "no path to file given"]);
      return;
    }
  }
}
