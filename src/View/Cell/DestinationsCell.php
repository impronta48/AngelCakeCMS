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
        $this->loadModel('Destinations');
	}

	private function make_destination_list()
	{
        $destionations = $this->Destinations
            ->find()
            ->select(['name', 'slug', 'id'])
            ->order(['name']);
		return $destionations;
	}

	public function fromId($destination_id) {
		return $this->Destinations->findById($destination_id)->firstOrFail();
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
