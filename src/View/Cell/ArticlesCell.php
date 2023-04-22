<?php
declare(strict_types=1);

namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Destinations cell
 */
class ArticlesCell extends Cell
{

	/**
	 * List of valid options that can be passed into this
	 * cell's constructor.
	 *
	 * @var array
	 */
	protected $_validCellOptions = [];

	/**
	 * Initialization logic run at the end of object construction.
	 *
	 * @return void
	 */
	public function initialize(): void {
        $this->loadModel('Articles');
	}

	/**
	 * Default display method.
	 *
	 * @return void
	 */
	public function display($articles = []) {
		$a = $this->Articles->find()
			->where(['slug IN' => $articles])
			->toArray();
		
		$this->set('articles',$a); 
	}
}
