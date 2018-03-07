<html>
<head></head>
<body>
<pre>

<?php
/**
 * Created by PhpStorm.
 * User: enola
 * Date: 01/03/2018
 * Time: 12:49
 */

require (__DIR__.'/vendor/autoload.php');


use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Application;
use PhPeteur\AirwatchWebservices\Services\AirwatchSystemGroupsSearch;

$configfile = __DIR__.'/config.yml';
if (!is_readable($configfile)) {
    die('Please copy config.yml.dist to config.yml'.PHP_EOL);
}
$cfg = Yaml::parseFile($configfile);

$oGroups = new AirwatchSystemGroupsSearch($cfg);
$arResGroups = $oGroups->Search();

$arResForJSON = array('data'=>[]);
foreach ($arResGroups['data']['LocationGroups'] as $oneGroup) {
    if ($oneGroup['Devices'] > 0) {
        $arResForJSON[] = ['label' => $oneGroup['Name'], 'value' => $oneGroup['Devices']];
    }
    }

var_dump($arResForJSON);

?>
</pre>
</body>
</html>

