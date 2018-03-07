<?php
/**
 * Created by PhpStorm.
 * User: enola
 * Date: 02/03/2018
 * Time: 18:22
 */

//$application = new \Symfony\Component\Console\Application('kraken', '1.0.0');
require (__DIR__.'/vendor/autoload.php');

function rglob($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }

    return $files;
}



function getClassFullPath($szClassName) {
    $arTestObjLoad = [];
    $idx =0;
    $folderin = './vendor/enoola/airwatchrestwebservicesclientlib/src/PhPeteur/AirwatchWebservices/Services';
    foreach (rglob($folderin.'/'.$szClassName.'.php') as $command)
    {
        echo 'foreach('.$idx++.')'.PHP_EOL;
        /*if (substr(basename($command), 0, 1) == '_') {
            echo '>'.$command . PHP_EOL;
            continue;
        }*/
        echo $command . PHP_EOL;
        //.\vendor\enoola\airwatchrestwebservicesclientlib\Fuz\
        $reduce = str_replace('./vendor/enoola/airwatchrestwebservicesclientlib/src/','',$command);
        $class = str_replace(['/', '.php'], ['\\', ''], $reduce);
        echo '=>'.$class.'<='.PHP_EOL;

        return ($class);
        //    $arTestObjLoad[] = new $class([]);
        //$application->add(new $class($container));
    }
    return (null);
}

echo '===>'.getClassFullPath('AirwatchSystemGroupsSearch').PHP_EOL;
$clname = getClassFullPath('AirwatchSystemGroupsSearch');
new $clname([]);