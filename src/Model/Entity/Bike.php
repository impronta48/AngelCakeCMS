<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\AttachmentManager;
use Cake\ORM\Entity;
use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\Utility\Text;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;

/**
 * Destination Entity
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 *
 * @property \App\Model\Entity\Event[] $events
 */
class Bike extends Entity
{

	
}
