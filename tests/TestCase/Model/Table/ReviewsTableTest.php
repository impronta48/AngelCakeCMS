<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ReviewsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ReviewsTable Test Case
 */
class ReviewsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ReviewsTable
     */
    protected $Reviews;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Reviews') ? [] : ['className' => ReviewsTable::class];
        $this->Reviews = $this->getTableLocator()->get('Reviews', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Reviews);

        parent::tearDown();
    }
}
