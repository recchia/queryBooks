<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Adapter\GoogleBooksApiAdapter;
use \PHPExcel_IOFactory;
use \PHPExcel;

$console = new Application('Linio Query Books', '0.3');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);
$console
    ->register('search')
    ->setDefinition(array(
        new InputArgument('apiName', InputArgument::REQUIRED, "Api name where search for books"),
        new InputArgument('source', InputArgument::REQUIRED, "Source file with isbn codes"),
        new InputArgument('target', InputArgument::REQUIRED, "Target file where store data requested"),
    ))
    ->setDescription('Search books in some API databases')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $apiName = $input->getArgument('apiName');
        $source = $input->getArgument('source');
        $target = $input->getArgument('target');
        $output->writeln($root);
        if (file_exists($source)) {
            switch ($apiName) {
                case 'google':
                    $excel = PHPExcel_IOFactory::load($source);
                    $sheet = $excel->getActiveSheet();
                    $highestRow = $sheet->getHighestRow();
                    $isbns = [];
                    for ($row = 1; $row <= $highestRow; ++$row) {
                        $isbns[] = $sheet->getCellByColumnAndRow(0, $row)->getValue();
                    }
                    $api = new GoogleBooksApiAdapter(['api_key' => 'AIzaSyDfR5cB9PNeD-fn6FtEs12n5CsbFXQQgDU']);
                    $books = $api->find($isbns);
                    break;
                default:
                    $output->writeln('<error>Unrecognized API</error>');
                    break;
            }
        } else {
            $output->writeln('<error>The target file ' . $source . ' does not exist</error>');
        }
    })
;

return $console;
