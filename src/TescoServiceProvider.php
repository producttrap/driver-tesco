<?php

declare(strict_types=1);

namespace ProductTrap\Tesco;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\ServiceProvider;
use ProductTrap\Contracts\Factory;
use ProductTrap\ProductTrap;

class TescoServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /** @var ProductTrap $factory */
        $factory = $this->app->make(Factory::class);

        $factory->extend(Tesco::IDENTIFIER, function () {
            /** @var CacheRepository $cache */
            $cache = $this->app->make(CacheRepository::class);

            return new Tesco(
                cache: $cache,
            );
        });
    }
}
