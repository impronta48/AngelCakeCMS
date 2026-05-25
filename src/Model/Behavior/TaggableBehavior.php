namespace App\Model\Behavior;

use Muffin\Tags\Model\Behavior\TagBehavior as BaseTagBehavior;

class TaggableBehavior extends BaseTagBehavior
{
    public function beforeFind(EventInterface $event, SelectQuery $query, ArrayObject $options): void
    {
        // chiama il beforeFind originale del plugin
        parent::beforeFind($event, $query, $options);

        // estendi il contain dei tag con l'enhancement
        $query->contain([
            'Tags' => [
                'TagsEnhancements'
            ]
        ]);
    }
}