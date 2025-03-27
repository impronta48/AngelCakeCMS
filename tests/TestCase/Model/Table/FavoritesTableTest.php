<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FavoritesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FavoritesTable Test Case
 */
class FavoritesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FavoritesTable
     */
    protected $Favorites;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Favorites') ? [] : ['className' => FavoritesTable::class];
        $this->Favorites = $this->getTableLocator()->get('Favorites', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Favorites);

        parent::tearDown();
    }
}
