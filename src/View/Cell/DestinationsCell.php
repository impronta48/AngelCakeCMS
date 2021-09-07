<?php
declare(strict_types=1);

namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Destinations cell
 */
class DestinationsCell extends Cell
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

	private function make_destination_list()
	{
        $this->loadModel('Destinations');
        $destionations = $this->Destinations
            ->find()
            ->select(['name', 'slug', 'id'])
            ->order(['name']);
		return $destionations;
	}

	/**
	 * Default display method.
	 *
	 * @return void
	 */
	public function display() {
        $this->set('destinations', $this->make_destination_list());
	}

	/**
	 * Options display method.
	 *
	 * @return void
	 */
	public function options() {
        $this->set('destinations', $this->make_destination_list());
	}
}
