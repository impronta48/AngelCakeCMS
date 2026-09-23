<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Destination;
use Cake\ORM\TableRegistry;

/**
 * Riscrive/genera il campo descrizione di una Destination tramite AI (OpenRouter).
 *
 * A differenza di Poi/Percorso, il prompt 'destinazione' in config/airouter.php è
 * completo e autonomo (non viene concatenato al preambolo condiviso "editoriale.global",
 * pensato per poi/percorso) e il campo `descrizione` è testo semplice, non HTML.
 */
class DestinationDescriptionAiService extends AbstractDescriptionAiService
{
    protected function includeGlobalPrompt(): bool
    {
        return false;
    }

    protected function outputFormat(): string
    {
        return self::OUTPUT_FORMAT_PLAIN;
    }

    protected function promptSection(mixed $entity): string
    {
        return 'destinazione';
    }

    protected function buildContext(mixed $entity): array
    {
        /** @var Destination $entity */
        $percorsiTable = TableRegistry::getTableLocator()->get('Cyclomap.Percorsi');
        $poiTable = TableRegistry::getTableLocator()->get('Cyclomap.Poi');

        $percorsiQuery = $percorsiTable->find()
            ->where(['destination_id' => $entity->id, 'published' => 1]);
        $percorsiTitoli = (clone $percorsiQuery)
            ->select(['title'])
            ->orderDesc('id')
            ->limit(15)
            ->all()
            ->extract('title')
            ->toArray();

        $poiQuery = $poiTable->find()
            ->where(['destination_id' => $entity->id, 'published' => 1]);
        $poiTitoli = (clone $poiQuery)
            ->select(['title'])
            ->orderDesc('promoted')
            ->orderDesc('id')
            ->limit(20)
            ->all()
            ->extract('title')
            ->toArray();

        return [
            'nome'                => $entity->name,
            'nomi_seo_alt'        => $entity->nomiseo,
            'regione'             => $entity->regione,
            'ciclovia'            => trim(strip_tags((string)$entity->ciclovia)),
            'descrizione_attuale' => trim(strip_tags((string)$entity->descrizione)),
            'percorsi_disponibili' => $percorsiTitoli,
            'percorsi_totali'      => $percorsiQuery->count(),
            'poi_disponibili'      => $poiTitoli,
            'poi_totali'           => $poiQuery->count(),
            'tipo_pagina'          => 'destinazione cicloturistica',
        ];
    }
}
