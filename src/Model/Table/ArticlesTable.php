<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\Utility\Text;
use Cake\Validation\Validator;

class ArticlesTable extends Table
{
  public function initialize(array $config): void
  {
    $this->addBehavior('Timestamp');
    $this->belongsTo('Users');
    $this->belongsTo('Destinations');
    $this->hasOne('Projects');
    $this->belongsToMany('Tags');
    //$this->addBehavior('Tags.Tag', ['taggedCounter' => false]);
  }

  public function beforeSave(\Cake\Event\EventInterface $event, $entity, $options)
  {
    if ($entity->tag_string) {
      $entity->tags = $this->_buildTags($entity->tag_string);
    }

    if (($entity->isNew() && !$entity->slug) || $entity->renewSlug && !$entity->slug) {
      $sluggedTitle = Text::slug(strtolower($entity->title));
      $entity->slug = substr($sluggedTitle, 0, 191);
    } elseif (!$entity->renewSlug) {
      $entity->slug = $entity->getOriginal('slug');
    }
  }

  public function validationDefault(Validator $validator): \Cake\Validation\Validator
  {
    $max_upload = (int)(ini_get('upload_max_filesize'));
    $max_post = (int)(ini_get('post_max_size'));
    $memory_limit = (int)(ini_get('memory_limit'));
    $upload_mb = min($max_upload, $max_post, $memory_limit);
    $validator
      ->notEmptyString('title')
      ->minLength('title', 5)
      ->maxLength('title', 255)

      //->notEmpty('body')
      ->minLength('body', 10);
    /*       ->add(
        'document',
        [
          [
            'rule' => ['extension', ['pdf', 'doc', 'docx', 'xlsx']], // default  ['gif', 'jpeg', 'png', 'jpg']
            'message' => __('Only pdf files are allowed.')
          ],
          [
            'rule' => ['fileSize', '<=', $upload_mb],
            'message' => __('Image must be less than 1MB')
          ]
        ]
      ); */


    return $validator;
  }

  public function findTagged(Query $query, array $options)
  {
    $columns = [
      'Articles.id', 'Articles.user_id', 'Articles.title',
      'Articles.body', 'Articles.published', 'Articles.created',
      'Articles.slug',
    ];

    $query = $query
      ->select($columns)
      ->distinct($columns);

    if (empty($options['tags'])) {
      $query->leftJoinWith('Tags')
        ->where(['Tags.title IS' => null]);
    } else {
      $query->innerJoinWith('Tags')
        ->where(['Tags.title IN' => $options['tags']]);
    }

    return $query->group(['Articles.id']);
  }

  protected function _buildTags($tagString)
  {
    //Tagstring = lavoro, personale, cultura
    //Explode --> array(' lavoro ', 'personale', ' cultura')
    //array_map applica la funzione trim a tutti gli elementi di explode

    //Trim tags
    $newTags = array_map('trim', explode(',', $tagString));

    //Toglie i tag vuoti
    $newTags = array_filter($newTags);

    //Toglie i duplicati
    $newTags = array_unique($newTags);

    $out = [];
    $query = $this->Tags->find()
      ->where(['Tags.title IN' => $newTags]);

    //Toglie i tag che ci sono già nel db prima di inserirli
    foreach ($query->extract('title') as $existing) {
      $index = array_search($existing, $newTags);
      if ($index !== false) {
        unset($newTags[$index]);
      }
    }

    //Aggiunge i tag rimasti dopo tutte ste pulizie

    //Già esistenti
    foreach ($query as $tag) {
      $out[] = $tag;
    }

    //Nuovi e superstiti
    foreach ($newTags as $tag) {
      $out[] = $this->Tags->newEmptyEntity(['title' => $tag]);
    }

    return $out;
  }
}
