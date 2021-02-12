<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Event Entity
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property int|null $max_pax
 * @property string|null $place
 * @property int|null $destination_id
 * @property \Cake\I18n\FrozenTime|null $start_time
 * @property \Cake\I18n\FrozenTime|null $end_time
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $user_id
 *
 * @property \App\Model\Entity\Destination $destination
 * @property \App\Model\Entity\User $user
 */
class Event extends Entity
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
    'title' => true,
    'description' => true,
    'max_pax' => true,
    'place' => true,
    'destination_id' => true,
    'start_time' => true,
    'end_time' => true,
    'created' => true,
    'modified' => true,
    'user_id' => true,
    'destination' => true,
    'user' => true,
    'slug' => true,
    'cost' => true,
  ];
}
