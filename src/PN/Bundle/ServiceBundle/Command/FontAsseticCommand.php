<?php

namespace PN\Bundle\ServiceBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FontAsseticCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('assetic:dump:font')
                ->setDescription('Assetic Dump Font')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $assets = $this->getContainer()->getParameter('assets');

        $webRoot = __DIR__ . '/../../../../../' . \AppKernel::$webRoot;
        $srcRoot = __DIR__ . '/../../../../../';

        foreach ($assets as $asset) {
            $src = $srcRoot . $asset['inputs'];
            $dest = $webRoot . $asset['output'];
            shell_exec("cp -r $src $dest");

            if (!file_exists(str_replace('*', '', $dest))) {
                mkdir(str_replace('*', '', $dest));
            }
            $scranDirSrc = scandir(str_replace("*", "", $src));
            for ($i = 2; $i < count($scranDirSrc) - 2; $i++) {
                $output->writeln("<info>[file+]</info> " . $dest . $scranDirSrc[$i]);
            }
        }
    }

}
