<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{

    const websiteTitle = "Bam";
    const fromEmail = 'no-reply@bamdigital.co';
    const adminEmail = 'info@bamdigital.co';
    const CDN_URL = '';
    const CDN_HOST = '50.87.52.41';
    const CDN_USERNAME = '';
    const CDN_PASSWORD = '';

    public static $webRoot = 'web/';

    public function __construct($environment, $debug) {
        if (file_exists(realpath(parent::getRootDir() . '/../public_html/'))) {
            self::$webRoot = "public_html/";
        }

        date_default_timezone_set('Africa/Cairo');
        parent::__construct($environment, $debug);
    }

    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \FOS\UserBundle\FOSUserBundle(),
            new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),
            new PN\Bundle\CMSBundle\CMSBundle(),
            new \PN\Bundle\MediaBundle\MediaBundle(),
            new \PN\Bundle\ServiceBundle\ServiceBundle(),
            new \PN\Bundle\SeoBundle\SeoBundle(),
            new \PN\Bundle\UserBundle\UserBundle(),
            new PN\Bundle\WorkBundle\WorkBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();

            if ('dev' === $this->getEnvironment()) {
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            }
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->setParameter('container.autowiring.strict_mode', true);
            $container->setParameter('container.dumper.inline_class_loader', true);

            $container->addObjectResource($this);
        });
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
