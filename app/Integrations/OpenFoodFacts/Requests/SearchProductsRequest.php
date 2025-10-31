<?php

namespace App\Integrations\OpenFoodFacts\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class SearchProductsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $searchTerm,
        protected int $page = 1,
        protected int $pageSize = 20,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/cgi/search.pl';
    }

    protected function defaultQuery(): array
    {
        return [
            'search_terms' => $this->searchTerm,
            'search_simple' => 1,
            'action' => 'process',
            'json' => 1,
            'page' => $this->page,
            'page_size' => $this->pageSize,
            'fields' => 'code,product_name,brands,image_url,categories,nutriments,allergens,labels',
        ];
    }
}
