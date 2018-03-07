<?php
/**
 * Created by PhpStorm.
 * User: enola
 * Date: 01/03/2018
 * Time: 19:23
 */

namespace PhPeteur\AirwatchFusionChartsReports;


use PhPeteur\AirwatchFusionChartsReports\Reports\AirwatchInstanceLoader;
use Symfony\Component\Yaml\Yaml;


class FusionChartsInvokers
{
    protected $_configfile ;
    protected $_arConfig;
    protected $_oAirwatchFusionChartsHelper;

    public function __construct() {

    }

    public function loadConfig($configfile)
    {
        $this->_configfile = $configfile;
        $c= __DIR__.'/config.yml';
        if (!is_readable($configfile)) {
            die('Please copy config.yml.dist to config.yml'.PHP_EOL);
        }
        $this->_configfile = Yaml::parseFile($configfile);

        $this->_AirwatchFusionChartsReports = new AirwatchInstanceLoader($this->_configfile);
        return (true);
    }

    public function getConfig() {
        return ($this->_arConfig);
    }
}