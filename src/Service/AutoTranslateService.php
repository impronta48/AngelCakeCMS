<?php

declare(strict_types=1);

namespace App\Service;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\I18n\I18n;
use Cake\ORM\Table;

/**
 * Traduce in inglese i campi di un'entità con il modulo esistente (entity->autoTranslate,
 * GoogleTranslate + AutoTranslationHistory) e salva la versione 'eng' via Translate behavior.
 * Usato dopo le generazioni AI (admin e regenerate_ai_content) e da translate_content.
 */
class AutoTranslateService
{
    /**
     * @throws \Throwable se la traduzione o il salvataggio falliscono
     */
    public function toEnglish(Table $table, EntityInterface $entity, array $fields, ?int $userId = null): void
    {
        if (!self::isConfigured()) {
            throw new \RuntimeException('GoogleTranslate.LANGUAGE_TRANSLATOR_APIKEY non configurata per questo sito.');
        }
        // ponytail: isobutil\GoogleTranslate fa `require '../vendor/autoload.php'` relativo alla cwd,
        // valido solo da webroot: in CLI ci spostiamo lì. Da togliere quando si sistema la libreria.
        if (PHP_SAPI === 'cli') {
            chdir(WWW_ROOT);
        }
        $locale = I18n::getLocale();
        try {
            foreach ($fields as $field) {
                $entity->autoTranslate($field, $userId);
            }
            I18n::setLocale('eng');
            if (!$table->save($entity)) {
                throw new \RuntimeException('Salvataggio traduzione eng fallito: ' . json_encode($entity->getErrors()));
            }
        } finally {
            I18n::setLocale($locale);
        }
    }

    public static function isConfigured(): bool
    {
        return !empty(Configure::read('GoogleTranslate.LANGUAGE_TRANSLATOR_APIKEY'));
    }
}
