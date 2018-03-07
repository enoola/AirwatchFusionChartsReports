<?php
/**
 * Created by PhpStorm.
 * User: enola
 * Date: 03/03/2018
 * Time: 14:12
 */
namespace PhPeteur\AirwatchFusionChartsReports\DataSaver;

use SQLite3;

/*
 * this one is a sqlite db saver
 */
class AirwatchDevicesToSQLLite
{
    protected $_dbfilename;
    protected $_szDriver = null ;//?
    protected $_oAWSearchObjectInstance = null;
    protected $_configFilename = '';
    private $_oDatabase = null;

    public function __construct($configFilename, $dbCustomerName) {
        $this->_configFilename = $configFilename;
        $this->_dbfilename = $dbCustomerName;
        $_szDriver = 'I don\'t know';
        $this->_oDatabase = null;
    }

    public function loadDB() {
        $this->_oDatabase = new SQLite3($this->_dbfilename, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
        if (is_null($this->_oDatabase)) {
            die (__CLASS__.' unable to load db'. $this->_dbfilename);
        }
    }

    public function initializeDBForCustomer($bOverwrite = false)
    {
        if ($bOverwrite) {
            if (file_exists($this->_dbfilename)) {
                unlink($this->_dbfilename);
                if (file_exists($this->_dbfilename))
                    die (__CLASS__ . " file exists even if try to remove it");
            }
        }
        $this->_oDatabase = new SQLite3($this->_dbfilename, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
        //might be good to have a database with customer db name ?
        return $this->initializeTableForCustomer();
    }

    /*
     * this function sets the db (table for customer with all field, and field type)
     * => all shall be summarized in db_config.yml
     * Table description :
     * id,
     * airwatch_id,
     * devicesn,
     * deviceimei (can be null),
     * device friendly name,
     * tags (can be null)
     * groups (can be null)
     *
     *
     */
    protected function initializeTableForCustomer() {
        $queryCreateTable = ' CREATE TABLE IF NOT EXISTS tbl_devices (
                    id INTEGER PRIMARY KEY,
                    aw_id INTEGER,
                    aw_udid INTEGER,
                    aw_serialnumber VARCHAR (255),
                    aw_macadress VARCHAR (16),
                    aw_userid INTEGER ,
                    aw_username VARCHAR (255),
                    aw_lgid TEXT,
                    aw_lgname TEXT,
                    aw_operatingsystem VARCHAR (255),
                    aw_platform VARCHAR (255),
                    aw_model VARCHAR (255),
                    aw_imei TEXT,
                    aw_easid TEXT,
                    aw_phonenumber TEXT,
                    aw_lastseen TIMESTAMP,
                    aw_enrolled TIMESTAMP,
                    aw_compromised_checked TIMESTAMP);';

                    //aw_tags TEXT,
                    //aw_localizations TEXT);';

        $result = $this->_oDatabase->exec($queryCreateTable);

        return ( $result );
    }

    public function doQuery($szQuery) {
        return ($this->_oDatabase->query($szQuery));
    }

}