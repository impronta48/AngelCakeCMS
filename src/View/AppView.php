<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\View;

use BootstrapUI\View\UIView;
use Cake\I18n\I18n;
use Cake\Core\Configure;

/**
 * Application View
 *
 * Your application's default view class
 *
 * @link https://book.cakephp.org/4/en/views.html#the-app-view
 */
class AppView extends UIView
{
	/**
	 * Initialization hook method.
	 *
	 * Use this method to add common initialization code like loading helpers.
	 *
	 * e.g. `$this->loadHelper('Html');`
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		//Don't forget to call the parent::initialize()
		parent::initialize();
		$this->loadHelper('Authentication.Identity');
		$this->loadHelper('AssetMix.AssetMix');

		$lang = I18n::getLocale();
		$base = ROOT;
		if (!is_null($this->theme)) {
			$base = $base . "/plugins/{$this->theme}";
		}
		$base = $base . DS . 'templates' . DS . $this->getTemplatePath();
		$localized_directory = $base . DS . $lang . DS . $this->getTemplate() . '.php';
		if (file_exists($localized_directory)) {
			$this->setTemplatePath($this->getTemplatePath() . DS . $lang);
		}
	}
}
