<?php
declare(strict_types=1);

namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Destinations cell
 */
class ArticlesCell extends Cell
{
    protected $_validCellOptions = [];

    public function initialize(): void {
        $this->loadModel('Articles');
    }

    public function display($slugs = []) {
        $articles = $this->Articles->find()
            ->where(['slug IN' => $slugs])
            ->toArray();

        // Riordina rispettando l'ordine dell'array $slugs
        $slugOrder = array_flip($slugs);
        usort($articles, function($a, $b) use ($slugOrder) {
            return $slugOrder[$a->slug] <=> $slugOrder[$b->slug];
        });

        $this->set('articles', $articles);
    }
}