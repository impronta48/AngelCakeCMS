<?php
declare(strict_types=1);

namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Block cell
 */
class BlockCell extends Cell
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
	}

	/**
	 * Default display method.
	 *
	 * @return void
	 */
	public function display($title) {
		$this->loadModel('Blocks');
		$block = $this->Blocks->findByTitle($title)->first();
		if (isset($block->body)) {
			$this->set('block', $block->body);
			$this->set('id', $block->id);
		} else {
			$this->set('block', '');
			$this->set('id', '#');
		}
	}
}
