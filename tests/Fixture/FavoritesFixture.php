<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FavoritesFixture
 */
class FavoritesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'user_id' => 1,
                'created' => '2025-03-26 17:30:20',
                'modified' => '2025-03-26 17:30:20',
                'model' => 'Lorem ipsum dolor sit amet',
                'foreign_key' => 1,
            ],
        ];
        parent::init();
    }
}
