<?php

declare(strict_types=1);

use ProductTrap\Contracts\Factory;
use ProductTrap\DTOs\Product;
use ProductTrap\Enums\Currency;
use ProductTrap\Enums\Status;
use ProductTrap\Facades\ProductTrap as FacadesProductTrap;
use ProductTrap\ProductTrap;
use ProductTrap\Spider;
use ProductTrap\tesco\Tesco;

function getMockTesco($app, string $response): void
{
    Spider::fake([
        '*' => $response,
    ]);
}

it('can add the Tesco driver to ProductTrap', function () {
    /** @var ProductTrap $client */
    $client = $this->app->make(Factory::class);

    $client->extend('tesco_other', fn () => new Tesco(
        cache: $this->app->make('cache.store'),
    ));

    expect($client)->driver(Tesco::IDENTIFIER)->toBeInstanceOf(Tesco::class)
        ->and($client)->driver('tesco_other')->toBeInstanceOf(Tesco::class);
});

it('can call the ProductTrap facade', function () {
    expect(FacadesProductTrap::driver(Tesco::IDENTIFIER)->getName())->toBe(Tesco::IDENTIFIER);
});

it('can retrieve the Tesco driver from ProductTrap', function () {
    expect($this->app->make(Factory::class)->driver(Tesco::IDENTIFIER))->toBeInstanceOf(Tesco::class);
});

it('can call `find` on the tesco driver and handle a successful response', function () {
    $html = file_get_contents(__DIR__.'/../fixtures/successful_response.html');
    getMockTesco($this->app, $html);

    $data = $this->app->make(Factory::class)->driver(Tesco::IDENTIFIER)->find('301511619');
    unset($data->raw);

    expect($data)
        ->toBeInstanceOf(Product::class)
        ->identifier->toBe('301511619')
        ->status->toEqual(Status::Available)
        ->name->toBe('John West No Drain Tuna Steak In Oil Fridge Pot 110G')
        ->description->toBe('Tuna Steak with a little Sunflower Oil.; 100% Traceable; Track Your Can john-west.co.uk')
        ->ingredients->toBe('<strong>Tuna</strong> (93%); Sunflower Oil; Salt')
        ->price->amount->toBe(2.0)
        ->unitAmount->unit->value->toBe('g')
        ->unitAmount->amount->toBe(100.0)
        ->unitPrice->unitAmount->unit->value->toBe('kg')
        ->unitPrice->unitAmount->amount->toBe(1.0)
        ->price->currency->toBe(Currency::GBP)
        ->unitPrice->price->amount->toBe(20.0)
        ->brand->name->toBe('JOHN WEST')
        ->images->toBe([
            'https://digitalcontent.api.tesco.com/v2/media/ghs/80791ac3-a2ab-4db9-88da-01631a6eaef9/d7fcce24-d9fc-4747-923c-9bddb8b3b94a_1749225173.jpeg?h=225&amp;w=225',
        ]);
});
