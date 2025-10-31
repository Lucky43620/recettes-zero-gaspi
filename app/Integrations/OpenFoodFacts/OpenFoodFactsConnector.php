<?php

namespace App\Integrations\OpenFoodFacts;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class OpenFoodFactsConnector extends Connector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://world.openfoodfacts.org';
    }

    protected function defaultHeaders(): array
    {
        return [
            'User-Agent' => 'RecettesZeroGaspi/1.0',
        ];
    }

    protected function defaultConfig(): array
    {
        return [
            'timeout' => 30,
            'verify' => false,
        ];
    }
}
