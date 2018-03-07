<?php
/**
 * Created by PhpStorm.
 * User: enola
 * Date: 04/03/2018
 * Time: 10:43
 */

namespace PhPeteur\AirwatchFusionChartsReports\Commands\Cron;

use PhPeteur\AirwatchFusionChartsReports\DataSaver\AirwatchDevicesToSQLLite;
use PhPeteur\AirwatchWebservices\Services\AirwatchMDMDevicesSearch;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use PhPeteur\AirwatchFusionChartsReports\BaseCommand\BaseCommand;

class SaveAirwatchCustomerFlatDevicesToSQLiteCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('save-customer-flatdevices-tosqlite')
            ->addArgument('customerdbfilename', InputArgument::REQUIRED,'Customer db filename.')
            ->addArgument('configfilename', InputArgument::REQUIRED, 'Config filename.')
            ->addArgument( 'rewrite',InputArgument::OPTIONAL, 'Rewrite db file.')
            ->setDescription('Gather Airwatch devices from instance and saves it to DB');
    }

    protected function doRun(InputInterface $input, OutputInterface $output)
    {
        $timea = time();
        $this->myoutput(BaseCommand::CMD_STATUS_IF,'starting..');

        $bDBFilenameExists = file_exists($input->getArgument('customerdbfilename'));
        if ( $bDBFilenameExists == true )
            $this->myoutput(BaseCommand::CMD_STATUS_IF, 'DB file: '.$input->getArgument('customerdbfilename').' exists.');

        $bRewrite = false;
        $rewrite = $input->getArgument('rewrite');
        echo $rewrite.PHP_EOL;
        if (!is_null($rewrite)) {
            if (strcmp($rewrite,'Y') == 0) {
                $this->myoutput(BaseCommand::CMD_STATUS_IF, 'Will rewrite file : ' . $input->getArgument('customerdbfilename') . '.');
                $bRewrite = true;
            }
        }
        if (($bRewrite == false) && ($bDBFilenameExists)) {
            $this->myoutput(BaseCommand::CMD_STATUS_KO, 'Will stop for now (update not implemented');
            return ;
        }

        $cfg = $input->getArgument('configfilename');
        $dbfilename = $input->getArgument('customerdbfilename');
        /*
         * creation of the db.. if doesn't exists
         */

        $odb = new AirwatchDevicesToSQLLite($cfg,$dbfilename);
        if ($odb->initializeDBForCustomer(true) == true)
            echo 'db created.'.PHP_EOL;


        $arMapDBFieldsWithAirwatchResult = [
            'aw_id' => 'Id',
            'aw_udid' => 'Udid' ,
            'aw_serialnumber' => 'SerialNumber',
            'aw_macadress' =>'MacAddress',
            'aw_userid' =>'UserId',
            'aw_username' => 'UserName',
            'aw_lgid'=> 'LocationGroupId',
            'aw_lgname'=>'LocationGroupName',
            'aw_operatingsystem' => 'OperatingSystem',
            'aw_platform' => 'Platform',
            'aw_model' => 'Model',
            'aw_imei' =>'Imei',
            'aw_easid'=>'EasId',
            'aw_phonenumber'=> 'PhoneNumber',
            'aw_lastseen'=> 'LastSeen',
            'aw_enrolled'=> 'LastEnrolledOn',
            'aw_compromised_checked'=> 'LastCompromisedCheckOn'
            //'aw_tags' => null,
            //'aw_localizations'
        ];




        /*
         * It is safe to continue so
         * Secondly we'll find devices and collect them
         * awcmd mdm-devices-search --Lgid
         */
        $objDevices = new AirwatchMDMDevicesSearch($this->_config );
        //NEED TO LOOP (multiple pages :))
        //parent::myoutput(parent::CMD_STATUS_IF,  'Will make this search for you : awcmd mdm-devices-search --Lgid '.$input->getArgument('ogid'));
        $resDevices = ['data' =>[]];
        $resDevices['data']['results'] = 0;
        $npage = 0;

        $resDevices['data']['Total'] = 0;
        $glcountquerywritten = 0;
        do {
            parent::myoutput(parent::CMD_STATUS_IF,  'Will make this search for you : awcmd mdm-devices-search --page '.$npage);

            $resDevicesPartial = $objDevices->Search( ['page'=> $npage ] );
            $resDevices['data']['Devices'] = [];

            if (strcmp($resDevicesPartial['status'], "204 No Content") == 0)
                break;


            foreach($resDevicesPartial['data']['Devices'] as $k => $val) {
                $val['LastSeen'] = str_replace('T',' ',$val['LastSeen']);

                //$oDate =  \DateTime::createFromFormat('Y-m-d H:i:s.u', $val['LastSeen'], \DateTimeZone::EUROPE);
                $oDate =  \DateTime::createFromFormat('Y-m-d H:i:s.u', $val['LastSeen']);
                $val['tm'] = $oDate->getTimestamp();
                //$val['inactivityDays'] = self::getInactivityDays($val['tm'],$curTimestamp);
                //echo '['.$val['inactivityDays'].']';

                $resDevices['data']['Devices'][] = $val;
            }

            parent::myoutput(parent::CMD_STATUS_IF,  'Will insert results into db: ' . $dbfilename.'.');

            foreach ($resDevices['data']['Devices'] as $arOneDevice)
            {
                $strquery = self::convertOneDeviceToSQLInsert($arOneDevice, $arMapDBFieldsWithAirwatchResult);

                if (!$odb->doQuery($strquery)) {
                    self::myoutput(BaseCommand::CMD_STATUS_KO, '[' . $glcountquerywritten  . '] Query went wrong.');
                }
                $glcountquerywritten++;
            }
            unset( $resDevices['data']['Devices']);

            $resDevices['data']['results'] += count($resDevicesPartial['data']['Devices']);
            $npage++;

        } while ($resDevices['data']['results'] < $resDevicesPartial['data']['Total']);


        $this->myoutput(self::CMD_STATUS_IF, 'rows gathered from aw: '.$resDevices['data']['results']);
        $this->myoutput(self::CMD_STATUS_IF, 'rows successfully written in db: '.$glcountquerywritten);

        $resultquery = $odb->doQuery('SELECT COUNT(*) FROM tbl_devices');
        $arCount = $resultquery->fetchArray();
        $this->myoutput(self::CMD_STATUS_IF, 'rows COUNTED in db: '.$arCount[0]);

        $tmduration = time() - $timea;
        $this->myoutput(self::CMD_STATUS_IF, 'it took me: '.$tmduration.' seconds to write infos.');


    }

    protected function convertOneDeviceToSQLInsert($arOneDevice, $arMapDBFieldsWithAirwatchResult) {

        $strquery = 'INSERT INTO tbl_devices (';
        $strvalues = ' ) VALUES (';

        foreach ($arMapDBFieldsWithAirwatchResult as $dbkey => $awkey) {
            if (strcmp($awkey, 'LocationGroupId') == 0) {
                $strvalues .= "'".$arOneDevice[$awkey]['Id']['Value'] . "' , ";

            } else if (strcmp($awkey, 'UserId') == 0) {
                if (array_key_exists('Id',$arOneDevice[$awkey] )) {
                    $strvalues .= "'".$arOneDevice[$awkey]['Id']['Value'] . "' , ";
                } else {
                    $strvalues .= "0 ,";
                }

            } else if (strcmp($awkey, 'Id') == 0) {
                $strvalues .= "'".$arOneDevice[$awkey]['Value'] . "' , ";
            } else {
                $strvalues .= "'".$arOneDevice[$awkey]. "' , ";
            }
            $strquery  .= $dbkey.', ';
        }
        $finalquery = substr($strquery, 0, -2). substr($strvalues,0, -2). ' );';

        return ( $finalquery );
    }


//    protected function

}