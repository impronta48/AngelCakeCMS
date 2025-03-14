<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PoiTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PoiTable Test Case
 */
class PoiTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PoiTable
     */
    protected $Poi;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Poi') ? [] : ['className' => PoiTable::class];
        $this->Poi = $this->getTableLocator()->get('Poi', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Poi);

        parent::tearDown();
    }
}
