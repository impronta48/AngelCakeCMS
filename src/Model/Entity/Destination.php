<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Destination Entity
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 *
 * @property \App\Model\Entity\Event[] $events
 */
class Destination extends Entity
{

    /**
     * Fields that can be mass assigned using newEmptyEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];


}
