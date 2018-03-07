<?php
/**
 * Created by PhpStorm.
 * User: enola
 * Date: 04/03/2018
 * Time: 10:40
 */

/*
 * useful to invoke cron like commands
 */

require (__DIR__.'/vendor/autoload.php');


use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Application;

use PhPeteur\AirwatchFusionChartsReports\Commands\Cron\SaveAirwatchCustomerFlatDevicesToSQLiteCommand;

$configfile = __DIR__.'/config.yml';
if (!is_readable($configfile)) {
    die('Please copy config.yml.dist to config.yml'.PHP_EOL);
}
$cfg = Yaml::parseFile($configfile);


//var_dump($cfg);
//exit;
$application = new Application("Airwatch command line tool to generate reports");
$application->add(new SaveAirwatchCustomerFlatDevicesToSQLiteCommand( $cfg ));


$application->run();