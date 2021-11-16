<?php

namespace PN\Bundle\ServiceBundle\Twig\Extension;
use Symfony\Bridge\Twig\Extension\AssetExtension as AssetsExtension;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AssetExtension extends AssetsExtension {

    private $container;

    public function __construct(ContainerInterface $container, Packages $packages) {
        parent::__construct($packages);
        $this->container = $container;
    }

    public function getAssetUrl($path, $packageName = null, $absolute = false, $version = null) {
        if (strpos($path, 'uploads/') !== FALSE) {
            return $this->container->get('assets')->assets($path);
        } else {
            return parent::getAssetUrl($path, $packageName, $absolute = false, $version = null);
        }
    }

}
