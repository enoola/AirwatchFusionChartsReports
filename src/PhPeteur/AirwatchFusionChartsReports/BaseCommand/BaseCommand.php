<?php
/**
 * Created by PhpStorm.
 * User: enola
 * Date: 04/03/2018
 * Time: 10:32
 */

namespace PhPeteur\AirwatchFusionChartsReports\BaseCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    const CMD_STATUS_OK = 1;
    const CMD_STATUS_KO = 0;
    const CMD_STATUS_IF = 2;

    protected $_config;
    protected $_oAW;
    protected $_output;

    public function __construct($iconfig)
    {
        $this->_config = $iconfig;
        parent::__construct();
    }

    protected function configure() {
    }

    protected function addGenericSearchOptions()
    {
        $this->addOption('outputcsv', null,InputOption::VALUE_NONE,'[default]display result(s) horizontally');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $colors = ['black', 'red', 'green', 'yellow', 'blue', 'magenta', 'cyan', 'white'];
        foreach ($colors as $color) {
            $style = new OutputFormatterStyle($color);
            $output->getFormatter()->setStyle($color, $style);
        }
        $this->_output = $output;
        try {
            $ret = $this->doRun($input, $output);
            if ($output->isVerbose()) {
                $output->writeln("Verbose invoked...");
                var_dump($ret);
            }

        } catch (QueryException $e) {
            $output->write(json_encode($e->getResponse(), JSON_PRETTY_PRINT));
        }

    }

    /*
      * A method to output string with [OK]/[KO] at the begining
      * OK->gree
      * KO->red
      * IF->yellow
      */
    public function myoutput($nStatus = self::CMD_STATUS_OK, $szText) {
        $beginText = '';
        $szdate = '['. date('Y-m-d H:i:s').']';
        switch ($nStatus) {
            case self::CMD_STATUS_OK :
                $beginText = '<green>'.$szdate.'[OK]</green>';
                break;
            case self::CMD_STATUS_KO :
                $beginText = '<red>'.$szdate.'[KO]</red>';
                break;
            case self::CMD_STATUS_IF :
                $beginText = '<yellow>'.$szdate.'[IF]</yellow>';
                break;
            default :
                //we voluntarily put 3 char this way it's quicly viewable
                $beginText = '<blue>'.$szdate.'[???]</blue>';
                break;
        }

        $this->_output->writeln($beginText.' '.$szText);
    }

    abstract protected function doRun(InputInterface $input, OutputInterface $output);
}
