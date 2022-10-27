<?php

namespace Kenjiefx\Forte\Services;

use Kenjiefx\Forte\Helpers\Configuration;

class PackageManager
{

    private array $configuration;

    public function __construct()
    {
        $this->configuration = Configuration::init();
    }

    public function getPackages()
    {
        return $this->configuration['requires']['require'];
    }

    public function getComposer()
    {
        return $this->configuration['composer'];
    }
}
