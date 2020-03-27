<?php
namespace App\View\Cell;

use Cake\View\Cell;
use Cake\Database\Query;

/**
 * News cell
 */
class NewsCell extends Cell
{
    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];
    private $limit;

    /**
     * Initialization logic run at the end of object construction.
     *
     * @return void
     */
    public function initialize() : void
    {
    }

    /**
     * Default display method.
     *
     * @return void
     */
    public function display($destination_id = 1, $limit = 6)
    {
        
        $this->loadModel('Articles');
        

        $articles = $this->Articles->find()
            ->where([   
                     'OR' =>
                     [
                        ['destination_id'=>$destination_id, 'archived' => 0],
                        'promoted' => 1
                        ]
                    ])
            ->order(['created' =>'DESC'])
            ->limit($limit);
        
        $this->set('articles', $articles);
    }

}
