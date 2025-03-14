<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PoiFixture
 */
class PoiFixture extends TestFixture
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
                'title' => 'Lorem ipsum dolor sit amet',
                'slug' => 'Lorem ipsum dolor sit amet',
                'descr' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'localita' => 'Lorem ipsum dolor sit amet',
                'lon' => 1,
                'lat' => 1,
                'elevation' => 1,
                'created' => '2025-03-13 12:40:08',
                'modified' => '2025-03-13 12:40:08',
                'user_id' => 1,
                'url' => 'Lorem ipsum dolor sit amet',
                'formato' => 1,
                'published' => 1,
                'namespace' => 'Lorem ipsum dolor sit amet',
                'data' => '',
                'dataold' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'codice' => 'Lorem ipsum dolor sit amet',
                'indirizzo' => 'Lorem ipsum dolor sit amet',
                'url_source' => 'Lorem ipsum dolor sit amet',
                'subnamespace' => 'Lorem ipsum dolor sit amet',
                'destination_id' => 1,
                'promoted' => 1,
                'slider' => 1,
                'geohash' => 'Lorem ip',
                'copertina_bkg_pos' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
