<?php

namespace PN\Bundle\ServiceBundle\Service;

use Symfony\Component\DependencyInjection\Container;

class AssetsService {

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function assets($path, $absolute = FALSE) {
        $file = __DIR__ . "/../../../../../" . \AppKernel::$webRoot . $path;
        if (file_exists($file)) {
            $baseUrl = "";
            if ($absolute == TRUE) {
                $baseUrl = $this->container->get('request')->getScheme() . "://" . $this->container->get('request')->getHost();
            }

            return $baseUrl . $this->container->get('request')->server->get('BASE') . "/" . $path;
        } else {
            return \AppKernel::CDN_URL . $path;
        }
    }

}
