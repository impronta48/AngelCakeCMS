<?php

declare(strict_types=1);

namespace App\Controller;

use App\Lib\AttachmentManager;

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
			return round((float) $size);
		}
	}
	// End of code from Drupal


  static function getFieldFiles($model, $destination, $id, $field, $allowed_extensions, $firstonly = false, $default = null)
  {
    return AttachmentManager::getFile(
      $model,
      $destination,
      $id,
      $field,
      $allowed_extensions,
      $firstonly,
      $default
    );
  }
}
