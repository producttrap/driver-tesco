<?php

declare(strict_types=1);

namespace ProductTrap\Tesco;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Arr;
use ProductTrap\Contracts\Driver;
use ProductTrap\DTOs\Brand;
use ProductTrap\DTOs\Price;
use ProductTrap\DTOs\Product;
use ProductTrap\DTOs\Results;
use ProductTrap\DTOs\UnitAmount;
use ProductTrap\DTOs\UnitPrice;
use ProductTrap\Enums\Currency;
use ProductTrap\Enums\Status;
use ProductTrap\Exceptions\ProductTrapDriverException;
use ProductTrap\Tesco\Exceptions\InvalidResponseException;
use ProductTrap\Traits\DriverCache;
use ProductTrap\Traits\DriverCrawler;

class Tesco implements Driver
{
    use DriverCache;
    use DriverCrawler;

    public const IDENTIFIER = 'tesco';

    public const BASE_URI = 'https://tesco.com';

    public function __construct(CacheRepository $cache)
    {
        $this->cache = $cache;
    }

    public function getName(): string
    {
        return static::IDENTIFIER;
    }

    /**
     * @param  array<string, mixed>  $parameters
     *
     * @throws ProductTrapDriverException
     */
    public function find(string $identifier, array $parameters = []): Product
    {
        $html = $this->remember($identifier, now()->addDay(), fn () => $this->scrape($this->url($identifier)));
        $crawler = $this->crawl($html);

        // Extract product JSON as possible source of information
        preg_match_all(
            '/data-redux-state="(.*?)"/',
            $crawler->html(),
            $matches
        );
        $jsonld = null;
        foreach ($matches[1] as $json) {
            $json = str_replace('&quot;', '"', $json);
            $json = (array) json_decode($json, true);

            if (isset($json['productDetails'])) {
                $jsonld = $json['productDetails'];
            }
        }

        if ($jsonld === null) {
            throw new InvalidResponseException('The data could not be found for this product');
        }

        /** @var array{sku?: string, product?: array} $jsonld */

        // Title
        $title = $jsonld['product']['title'] ?? null;

        // Description
        $description = isset($jsonld['product']['description']) ? Arr::join($jsonld['product']['description'], '; ') : null;

        //SKU
        $sku = $jsonld['sku'] ?? $identifier;

        //GTIN-13
        $gtin = $jsonld['product']['gtin'] ?? null;

        // Brand
        $brand = isset($jsonld['product']['brandName']) ? new Brand(
            name: $brandName = $jsonld['product']['brandName'],
            identifier: $brandName,
        ) : null;

        // Currency
        $currency = Currency::GBP;

        // Price
        $price = $jsonld['product']['price'] ?? null;
        $price = ($price !== null)
            ? new Price(
                amount: $price,
                currency: $currency,
            )
            : null;

        // Images
        $images = array_values(array_unique($jsonld['product']['images'] ?? []));

        // Status
        $status = null;
        if (isset($jsonld['product']['status'])) {
            $status = match ($jsonld['product']['status']) {
                'AvailableForSale' => Status::Available,
                default => Status::Unknown,
            };
        }

        $status ??= Status::Unknown;

        // Ingredients
        $ingredients = isset($jsonld['product']['details']['ingredients']) ?
            Arr::join($jsonld['product']['details']['ingredients'], '; ') :
            null;

        // Unit Amount (e.g. 85g or 1kg)
        $unitAmount = UnitAmount::parse((string) ($jsonld['product']['unitOfMeasure'] ?? $title));

        // Unit Price (e.g. $2 per kg)
        $unitPrice = isset($jsonld['product']['unitPrice']) ? (string) $jsonld['product']['unitPrice'] : null;
        $unitPrice = UnitPrice::determine(
            price: $price,
            unitAmount: $unitAmount,
            unitPrice: $unitPrice,
            currency: $currency,
        );

        return new Product(
            identifier: $identifier,
            sku: $sku,
            gtin: $gtin,
            name: $title,
            description: $description,
            url: $this->url($identifier),
            price: $price,
            status: $status,
            brand: $brand,
            unitAmount: $unitAmount,
            unitPrice: $unitPrice,
            ingredients: $ingredients,
            images: $images,
            raw: [
                'html' => $html,
            ],
        );
    }

    public function url(string $identifier): string
    {
        return sprintf('%s/groceries/en-GB/products/%s', self::BASE_URI, $identifier);
    }

    /**
     * @param  array<string, mixed>  $parameters
     *
     * @throws ProductTrapDriverException
     */
    public function search(string $keywords, array $parameters = []): Results
    {
        return new Results();
    }
}
