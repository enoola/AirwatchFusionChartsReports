<?php
/**
 * Created by PhpStorm.
 * User: enola
 * Date: 01/03/2018
 * Time: 18:08
 */
namespace PhPeteur\AirwatchFusionChartsReports\Reports;

use Symfony\Component\Yaml\Yaml;
use \PhPeteur\AirwatchWebservices\Services\AirwatchServicesSearch;
use \PhPeteur\AirwatchWebservices\Services\AirwatchSystemGroupsSearch;


/*
 * Gather informations from Airwatch ws
 */
class AirwatchInstanceLoader
{
    protected $_oAirwatchWS;
    protected $_configFilename;
    protected $_arConfigFile;

    public function __construct($configfilename)
    {
        $this->_oAirwatchWS = null;
        $this->_configFilename = $configfilename;
    }

    public function loadConfig(  )
    {
        if (!is_readable($this->_configFilename)) {
            die('Please copy config.yml.dist to config.yml' . PHP_EOL);
        }

        $this->_arConfigFile = Yaml::parseFile($this->_configFilename);

    }

    public function getAirwatchClassInstance($szClassName)
    {
        if (!is_null($this->_oAirwatchWS))
            return ($this->_oAirwatchWS);

        $classFullName = self::getClassFullPath($szClassName);
        $this->_oAirwatchWS = new $classFullName($this->_arConfigFile);

        if (is_null($this->_oAirwatchWS) ) {
            throw new Exception ("unable to load : ".$szClassName);
        }
        return (true);
    }



//    public function getJSonOutOfAirwatchSearch($mainFieldToGather, $arMapSubFieldsToGatherDataFrom) {
    public function getJSonOutOfAirwatchSearch($mainFieldToGather, $arMapSubFieldsToGatherDataFrom)
    {
        $idx=0;
        $arResGroups = $this->_oAirwatchWS->Search();
        //var_dump( $arResGroups );
        //exit;
        $arResForJSON = array('data'=>[]);
        foreach ($arResGroups['data'][$mainFieldToGather] as $oneGroup) {
            if ($oneGroup['Devices'] > 10) {

                foreach ($arMapSubFieldsToGatherDataFrom as $onefusionchartsfield => $onevaluefieldfromairwatch) {
                    $arResForJSON['data'][$idx][$onefusionchartsfield] = $oneGroup[$onevaluefieldfromairwatch];
                }
                $idx++;
            }
        }

        $arResForJSON['chart'] = [];
        $arResForJSON['chart']['caption'] = 'Groups devices';
        $arResForJSON['chart']['subCaption'] = 'devices subcap';
        //$arResForJSON['chart']['numberPrefix'] = '';
        $arResForJSON['chart']['theme'] = 'ocean';

        return (json_encode($arResForJSON,JSON_PRETTY_PRINT));
    }

    function getClassFullPath($szClassName) {
        //$idx =0;
        $folderin = './vendor/enoola/airwatchrestwebservicesclientlib/src/PhPeteur/AirwatchWebservices/Services';
        foreach (rglob($folderin.'/'.$szClassName.'.php') as $command)
        {
            //echo 'foreach('.$idx++.')'.PHP_EOL;
            /*if (substr(basename($command), 0, 1) == '_') {
                echo '>'.$command . PHP_EOL;
                continue;
            }*/
            //echo $command . PHP_EOL;
            //.\vendor\enoola\airwatchrestwebservicesclientlib\Fuz\
            $reduce = str_replace('./vendor/enoola/airwatchrestwebservicesclientlib/src/','',$command);
            $class = str_replace(['/', '.php'], ['\\', ''], $reduce);
            //echo '=>'.$class.'<='.PHP_EOL;

            return ( $class );
            //    $arTestObjLoad[] = new $class([]);
            //$application->add(new $class($container));
        }
        return (null);
    }
}

function rglob($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }

    return $files;
}
/*
function my_autoloader($szClassName) {
    include 'vendor/enoola/airwatchwebservicesclientlib/src/PhPeteur/Services/'.$szClassName.'.php';
}
*/

