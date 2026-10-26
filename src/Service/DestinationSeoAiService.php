<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Destination;

/**
 * Genera seo_description e seo_keywords per una Destination tramite AI (OpenRouter).
 *
 * Le linee guida SEO (globali + specifiche per destinazione) vivono in
 * config/airouter.php del plugin Cyclomap (Configure key "AiRouter"), non
 * qui: per modificarle basta editare quel file, senza toccare questa classe.
 */
class DestinationSeoAiService extends AbstractSeoAiService
{
    protected function promptSection(mixed $entity): string
    {
        return 'destinazione';
    }

    protected function buildContext(mixed $entity): array
    {
        /** @var Destination $entity */
        return [
            'nome'          => $entity->name,
            'regione'       => $entity->regione,
            'nomi_seo_alt'  => $entity->nomiseo,
            'descrizione'   => trim(strip_tags((string)$entity->descrizione)),
            'tipo_pagina'   => 'destinazione cicloturistica',
        ];
    }
}
