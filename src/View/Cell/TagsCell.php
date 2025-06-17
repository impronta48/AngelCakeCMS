<?php

declare(strict_types=1);

namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Destinations cell
 */
class TagsCell extends Cell
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
    public function initialize(): void
    {
        $this->loadModel('Tags');
    }

    /**
     * Default display method.
     *
     * @return void
     */
    public function display()
    {
        $t = $this->Tags->find()
            ->order(['Tags.title' => 'ASC'])
            ->limit(1000);

        $this->set('tags', $t);
    }
}
