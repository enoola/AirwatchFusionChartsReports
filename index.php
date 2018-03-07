<?php
require (__DIR__.'/vendor/autoload.php');

include("./src/libs/fusionchartsphpwrapper/fusioncharts.php");

use PhPeteur\AirwatchWebservices\Services\AirwatchSystemGroupsSearch;
use PhPeteur\AirwatchFusionChartsReports\Reports\AirwatchInstanceLoader;

?>
<html>

<head>
    <title>My first chart using FusionCharts Suite XT</title>
    <script type="text/javascript" src="src/libs/fusioncharts/js/fusioncharts.js"></script>
    <script type="text/javascript" src="src/libs/fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>
</head>
<body>

<?php




/*
$configfile = __DIR__.'/config.yml';
if (!is_readable($configfile)) {
    die('Please copy config.yml.dist to config.yml'.PHP_EOL);
}
$cfg = Yaml::parseFile($configfile);
*/
/*
$oGroups = new AirwatchSystemGroupsSearch($cfg);
$arResGroups = $oGroups->Search();

$arResForJSON = array('data'=>[]);
foreach ($arResGroups['data']['LocationGroups'] as $oneGroup) {
    if ($oneGroup['Devices'] > 0) {
        $arResForJSON['data'][] = ['label' => $oneGroup['Name'], 'value' => $oneGroup['Devices']];
    }
}

$arResForJSON['chart'] = [];
$arResForJSON['chart']['caption'] = 'Groups devices';
$arResForJSON['chart']['subCaption'] = 'devices subcap';
//$arResForJSON['chart']['numberPrefix'] = '';
$arResForJSON['chart']['theme'] = 'ocean';

*/
$cfg = __DIR__.'/config.yml';


//$oGroups = new AirwatchSystemGroupsSearch($cfg);
//exit;
//$oAirwatchFusionChartsReports =  new AirwatchFusionChartsReports($oGroups);
//var_dump(json_encode($arResForJSON, JSON_PRETTY_PRINT));
//exit;

$oAirwatchFusionChartsReportsHelper = new AirwatchInstanceLoader( 'config.yml' );
$oAirwatchFusionChartsReportsHelper->loadConfig();


$oAirwatchFusionChartsReportsHelper->getAirwatchClassInstance('AirwatchSystemGroupsSearch');

$arMapFieldsAWValues = array('label'=>'Name','value'=>'Devices');

$szJson = $oAirwatchFusionChartsReportsHelper->getJSonOutOfAirwatchSearch('LocationGroups',$arMapFieldsAWValues);

//var_dump($szJson);
//exit;
//$oAirwatchFusionChartsReportsHelper->getJsonData();
//$oAirwatchFusionChartsReportsHelper->load('AirwatchSystemGroupsSearch');

/*
 *
$columnChart = new FusionCharts(
    "column3d",
    "ex1" ,
    "600",
    "400",
    "chart-1",
    "json",
    $szJson);
*/

$columnChart = new FusionCharts(
    "pie2d",
    "ex1" ,
    "600",
    "400",
    "chart-1",
    "json",
    $szJson);


    /*'{
"chart":
{
"caption":"Harry\'s SuperMart",
"subCaption":"Top 5 stores in last month by revenue",
"numberPrefix":"$",
"theme":"ocean"
},
"data":
[
{
"label":"Bakersfield Central",
"value":"880000"
},
{
"label":"Garden Groove harbour",
"value":"730000"
},
{
"label":"Los Angeles Topanga",
"value":"590000"
},
{
"label":"Compton-Rancho Dom",
"value":"520000"
},
{
"label":"Daly City Serramonte",
"value":"330000"
}
]');
    */

$str = '{ "data":
[
{
"label":"Bakersfield Central",
"value":"880000"
},
{
"label":"Garden Groove harbour",
"value":"730000"
},
{
"label":"Los Angeles Topanga",
"value":"590000"
},
{
"label":"Compton-Rancho Dom",
"value":"520000"
},
{
"label":"Daly City Serramonte",
"value":"330000"
}
]
}';

//var_dump(json_decode($str, JSON_OBJECT_AS_ARRAY));
//exit;

$columnChart->render();
?>

<div id="chart-1"></div>

</body>
</html>
