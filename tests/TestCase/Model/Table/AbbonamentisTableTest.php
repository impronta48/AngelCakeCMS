<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AbbonamentisTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AbbonamentisTable Test Case
 */
class AbbonamentisTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AbbonamentisTable
     */
    protected $Abbonamentis;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Abbonamentis',
        'app.Users',
        'app.Companies',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Abbonamentis') ? [] : ['className' => AbbonamentisTable::class];
        $this->Abbonamentis = $this->getTableLocator()->get('Abbonamentis', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Abbonamentis);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
