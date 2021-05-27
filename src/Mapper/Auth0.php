<?php
declare(strict_types=1);

/**
 * Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace App\Mapper;

use CakeDC\Auth\Social\Mapper\AbstractMapper;

class Auth0 extends AbstractMapper
{

	/**
	 * Map for provider fields
	 *
	 * @var array
	 */
	protected $_mapFields = [
	'id' => 'sub',
	'avatar' => 'picture',
	'username' => 'nickname',
	'modified' => 'updated_at',
	'first_name' => 'name',
	'email' => 'email',
	];

	/**
	 * Get link property value
	 *
	 * @param mixed $rawData raw data
	 *
	 * @return string
	 */
	protected function _link($rawData) {
		return $rawData['link'] ?? null ?: '#';
	}
}
