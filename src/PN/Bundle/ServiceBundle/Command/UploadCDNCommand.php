<?php

namespace PN\Bundle\ServiceBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UploadCDNCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('upload-cdn')
                ->setDescription('Generate SiteMap')
//                ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
//                ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT * FROM `image` WHERE base_path IS NOT NULL AND cdn_server IS NULL   LIMIT 100");
        $statement->execute();
        $results = $statement->fetchAll();
        $baseDir = __DIR__ . "/../../../../../" . \AppKernel::$webRoot . "uploads/";
        $remoteBaseDir = "/uploads/";

        $conn = ftp_connect(\AppKernel::CDN_HOST);
        ftp_login($conn, \AppKernel::CDN_USERNAME, \AppKernel::CDN_PASSWORD);
        foreach ($results as $result) {
            $file = $baseDir . rtrim($result['base_path'], "/") . '/' . $result['name'];
            if (file_exists($file)) {
                $remoteFileDir = $remoteBaseDir . rtrim($result['base_path'], "/") . '/';
                $this->ftp_mksubdirs($conn, $remoteFileDir);
                $upload = ftp_put($conn, $remoteFileDir . $result['name'], $file, FTP_BINARY);
                if ($upload) {
                    $statement = $connection->prepare("UPDATE `image` SET `cdn_server` = '1' WHERE `image`.`id` =" . $result['id']);
                    $statement->execute();
                    unlink($file);
//                    echo "successfully uploaded $file TO " . $remoteFileDir . $result['name'] . "\n";
                } else {
                    echo "There was a problem while uploading $file\n";
                }
            } else {
                $statement = $connection->prepare("UPDATE `image` SET `cdn_server` = '0' WHERE `image`.`id` =" . $result['id']);
                $statement->execute();
            }
        }
        ftp_close($conn);
//        $output->writeln("Done");
    }

    function ftp_mksubdirs($conn, $ftpath) {
//        @ftp_chdir($conn, $ftpBaseDir); // /var/www/uploads
        $parts = explode('/', $ftpath); // 2013/06/11/username
        foreach ($parts as $part) {
            if (!@ftp_chdir($conn, $part)) {
                ftp_mkdir($conn, $part);
                ftp_chdir($conn, $part);
            }
        }
    }

}
