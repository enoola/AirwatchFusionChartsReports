<?php
/**
 * Created by PhpStorm.
 * User: enola
 * Date: 03/03/2018
 * Time: 10:38
 */
?>
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
?>
<div id="chart-1"></div>
<table>
    <tr><td>1</td><td>2</td></tr>
    <tr><td>3</td><td>4 </td></tr>
</table>
</body>
</html>
