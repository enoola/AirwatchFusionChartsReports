<?php
/**
 * Created by PhpStorm.
 * User: enola
 * Date: 04/03/2018
 * Time: 18:08
 */

namespace PhPeteur\AirwatchFusionChartsReports\Reports;


/*
 * this class will help us read from the db containing all devices in flat
 */
class AWDeviceSQLLiteReader
{
    protected $_dbfilename;
    private $_oDatabase = null;

    public function __construct( $dbCustomerName )
    {
        $this->_dbfilename = $dbCustomerName;
        $this->_oDatabase = null;
    }

    public function loadDB() {
        $this->_oDatabase = new \SQLite3($this->_dbfilename, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
        if (is_null($this->_oDatabase)) {
            throw new \Exception (__CLASS__.' Unable to load db'. $this->_dbfilename);
        }
    }

    public function seekOneDeviceById($deviceId) : array
    {
        $query = 'SELECT * FROM tbl_devices WHERE Id = '.$deviceId.'.';
        $oRes = $this->doQuery($query);
        $arRes = [];
        foreach ($oRes->fetchArray() as $k => $val) {
            echo '=>' . $k .PHP_EOL;
            var_dump($val);
        }

    }

    public function getDeviceById($devId) {
        $arDevices = [];
        $szQuery = 'SELECT * FROM tbl_devices WHERE aw_id='.$devId.';';
        $oRes = $this->doQuery($szQuery) ;
        while ($arOneRes = $oRes->fetchArray(SQLITE3_ASSOC))
            $arDevices[] = $arOneRes;

        return ($arDevices);
    }

    public function getAllDevices() {
        $arDevices = [];
        $szQuery = 'SELECT * FROM tbl_devices;';
        $oRes = $this->doQuery($szQuery) ;
        while ($arOneRes = $oRes->fetchArray(SQLITE3_ASSOC))
            $arDevices[] = $arOneRes;
        return ( $arDevices );
    }

    public function getDevices($whereCondition) {
        $arDevices = [];
        $szQuery = 'SELECT * FROM tbl_devices WHERE '.$whereCondition.';';
        $oRes = $this->doQuery($szQuery) ;
        while ($arOneRes = $oRes->fetchArray(SQLITE3_ASSOC))
            $arDevices[] = $arOneRes;
        return ( $arDevices );
    }

    public function doQuery($szQuery) {
        return ($this->_oDatabase->query($szQuery));
    }

    public function doQueryAndFetch($szQuery) : array
    {
        $arDevices = [];
        $oRes = $this->doQuery($szQuery) ;
        while ($arOneRes = $oRes->fetchArray(SQLITE3_ASSOC))
            $arDevices[] = $arOneRes;

        return ( $arDevices );
    }



}