<?php

declare(strict_types=1);

namespace App\Lib;

use Cake\Utility\Text;
use Cake\Filesystem\Folder;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cyclomap\Lib\kmlCleaner;
use \Error;

class AttachmentManager
{
	static function replaceExtension($fname) // Will change any image extension with .jpg
	{
		$split = explode('.', $fname);
		$ext = array_pop($split);
		if (in_array($ext, ['.png', '.gif', '.jpeg', '.bmp'])) {
			return Text::slug(implode('.', $split)) . '.jpg';
		}
		return Text::slug(implode('.', $split)) . '.' . $ext;
	}

	static function splitPath(string $path): array
	{
		if (str_starts_with($path, '/')) {
			$path = substr($path, 1);
		}
		$pieces = explode('/', $path);
		if (count($pieces) < 5) {
			return [];
		}
		return [
			'sitedir' => $pieces[0],
			'model' => $pieces[1],
			'destination' => $pieces[2],
			'id' => $pieces[3],
			'field' => $pieces[4],
			'fname' => count($pieces) > 5 ? $pieces[5] : '',
		];
	}

	static function buildPath($model, $destination, $id, $field = '', $fname = '')
	{
		$fullDirTemplate = Configure::read('copertina-pattern', ':sitedir/:model/:destination/:id/:field/');

		$save_dir = str_replace(
			[
				':sitedir',
				':model',
				':destination',
				':id',
				':field',
			],
			[
				Configure::read('sitedir'),
				empty($model) ? 'attachments' : strtolower($model),
				empty($destination)  || ($destination == "null") ? 'none' : strtolower($destination),
				empty($id) ? '' : $id,
				empty($field) ? '' : $field,
			],
			$fullDirTemplate
		);

		// TODO do this in a nicer way!
		$save_dir = str_replace("//", "/", $save_dir);
		$save_dir = str_replace("cyclomap.", "", $save_dir);

		if (!empty($fname)) {
			$save_dir = $save_dir . DS . $fname;
		}

		return $save_dir;
	}

	static function makeFolder($save_dir, $deleteBefore = false, $temporary = false)
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

	static function getFile($model, $destination, $id, $field, $allowed_extensions, $firstonly = false, $default = false)
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
			'destination' => (empty($destination)  || ($destination == "null")) ? 'none' : strtolower($destination),
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
			if ($default) {
				return Router::url(Configure::read('default-image', null));
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

	static function moveAllFiles($files, $model, $destination, $id, $field)
	{
		if (empty($files)) return true;
		foreach ($files as $f) {
			if (empty($f)) continue;
			$old_path = self::splitPath($f);
			$res = self::renameFile(
				self::buildPath(
					$old_path['model'],
					$old_path['destination'],
					$old_path['id'],
					$old_path['field'],
				),
				self::buildPath(
					$model,
					$destination,
					$id,
					$field,
				),
				$old_path['fname']
			);
			if (!$res) return false;
		}
		return true;
	}

	static function moveChangeDestination($model, $id, $from_slug, $to_slug)
	{
		$from_path = WWW_ROOT . self::buildPath($model, $from_slug, $id, null, null);
		$dest_folder = WWW_ROOT . self::buildPath($model, $to_slug, null, null, null);
		$dest_folder = rtrim($dest_folder, "/");

		//Devo cambiare la umask per consetire a mkdir di avere i permessi di scrittura
		//https://stackoverflow.com/questions/7878784/php-mkdir-permissions
		//Prima controllo se la cartella esiste, se non esiste la creo
		if (!file_exists($dest_folder)) {
			$old = umask(0);
			mkdir($dest_folder, 0777, true);
			umask($old);
		}

		$to_path = WWW_ROOT . self::buildPath($model, $to_slug, $id, null, null);
		$from_path = rtrim($from_path, "/");
		$to_path = rtrim($to_path, "/");

		$cmd = "mv $from_path $dest_folder";

		exec($cmd, $output, $return_val);

		return $return_val;
	}

	static function renameFile($from, $to, $fname, $new_fname = null)
	{
		if (is_null($new_fname)) $new_fname = $fname;
		self::makeFolder($to);
		return rename($from . DS . $fname, $to . DS . $new_fname);
	}

	static function putFile($files, $model, $destination, $id, $field, $temporary = false, $deleteBefore = false)
	{
		$results = [];

		$errors = Configure::read('phpFileUploadErrors');

		$save_dir = self::buildPath($model, $destination, $id, $field);

		self::makeFolder($save_dir, $deleteBefore, $temporary);

		if (array_key_exists($field, $files)) {
			if (is_array($files[$field])) { // multi-file upload
				foreach ($files[$field] as $n => $file) {
					$err = $file->getError();
					if ($err == 0) {
						$fname = self::replaceExtension($file->getClientFileName());
						$file->moveTo(($temporary ? TMP : WWW_ROOT) . $save_dir . $fname); // Will raise an exc if something goes wrong


						$results["upload$n"] = 'OK';
					} else {
						$results['error'] = $errors[$err];
						break;
					}
				}
			} else {
				$err = $files[$field]->getError();
				if ($err == 0) {
					$fname = self::replaceExtension($files[$field]->getClientFileName());
					$files[$field]->moveTo(($temporary ? TMP : WWW_ROOT) . $save_dir . $fname); // Will raise an exc if something goes wrong
					//se il file è un KML devo chiamare il KML cleaner
					if (strtolower(pathinfo($fname, PATHINFO_EXTENSION)) == 'kml') {
						$kml_cleaner = new kmlCleaner(($temporary ? TMP : WWW_ROOT) . $save_dir . $fname);
						$kml_cleaner->clean();
					}
					$results['upload'] = 'OK';
				} else {
					$results['error'] = $errors[$err];
				}
			}
		} else {
			$results['error'] = 'no file uploaded';
		}

		return $results;
	}

	static function popFile($model, $destination, $id, $field, $name, $temporary = false)
	{
		$results = [];

		$save_dir = self::buildPath($model, $destination, $id, $field);
		if (!empty($save_dir)) {
			$fname = rtrim(($temporary ? TMP : WWW_ROOT) . $save_dir . $name);
			if (file_exists($fname)) {
				$ip = $_SERVER['REMOTE_ADDR'];
				//TODO: devo cancellare lo stesso nome file anche in tutte le altre cartelle figlie

				unlink($fname);

				//devo cancellare anche la cache di glide
				$d = CACHE . Configure::read('sitedir') . DS . "glide" . DS . $save_dir;
				$cmd = sprintf("rm -rf %s", escapeshellarg($d));
				exec($cmd, $output, $return_val);

				$results['log'] = "eliminato il file $fname da $ip";
				$results['removed'] = 'OK';
			} else {
				$results['warning'] = "file doesn't exist on server";
			}
		} else {
			$results['error'] = "no path to file given";
		}

		return $results;
	}
}
