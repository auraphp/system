<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Cli\MakePackage;
use Aura\Cli\Command as CliCommand;
use Aura\Framework\System;

/**
 * 
 * Run : php package/Aura.Framework/commands/make-package Vendor.Package
 * 
 * Vendor.Package is optional. If its not provided, you will get a prompt.
 * 
 * @package Aura.Framework
 * 
 */
class Command extends CliCommand
{
    
    /**
     * 
     * The Aura system directory object.
     * 
     * @var System
     * 
     */
    protected $system;
    
    /**
     * 
     * Sets the System object.
     * 
     * @param System $system The System object.
     * 
     * @return void
     * 
     */
    public function setSystem(System $system)
    {
        $this->system = $system;
    }
    
    /**
     * 
     * Runs the specified PHPUnit test suite.
     * 
     * @return void
     * 
     */
    public function action()
    {
        if (empty($this->params)) {
            $this->getVendorDotPackage();
        } else {
            // go back to the original arguments
            $argv = $this->context->getArgv();
            $this->processInput( $argv[0] );
        }
    }
    
    /**
     * 
     * Get input of form Vendor.Package
     *
     */
    public function getVendorDotPackage()
    {
        $this->stdio->out('Please enter vendor.package name: ');
        $input = $this->stdio->in();
        $this->processInput( $input );
    }
    
    /**
     * 
     * Check given input is of the form vendor.package
     * Show Help and go and get until he exit
     */
    public function processInput( $input )
    {
        /** 
         * Get it in an array than list. There may be no dots in input
         */
        $list = explode( ".", strtolower($input) );
        switch( $input ) {
            case 'help':
            case '--h':
            case '--help':
            case '':
                $this->showHelp();
                $this->getVendorDotPackage();
                break;
            case 'exit':
            case 'quit':
                $this->showExit();
                break;
            default :
                /**
                 * Count not equals 2 means its not vendor.package format
                 * Show help and get vendor.package format
                 */
                if( count ($list) != 2 ) {
                    $this->showHelp();
                    $this->getVendorDotPackage();
                } else {
                    /**
                     * $list[0] is vendor , $list[1] is package
                     */
                    $this->createVendorDotPackage($list[0], $list[1]);
                } 
        }
    }
    
    /**
     * Show help message
     */
    public function showHelp()
    {
        $this->stdio->outln('Eg for Vendor.Package are Aura.Cli , Aura.Router');
        $this->stdio->outln('Vendor can be your name, your company name etc');
        $this->stdio->outln('To exit from shell. Type exit or quit');
    }
    
    /**
     * Print and Exit
     */
    public function showExit()
    {
        $this->stdio->outln('Exiting!');
    }
    
    /*
     * Create the vendor.package directory structure
     */
    public function createVendorDotPackage($vendor, $package)
    {
        $vendor = ucfirst($vendor);
        $package = ucfirst($package);
        // Get Package Path
        $package_path = $this->system->getPackagePath();
        $full_vendor_package_path = $package_path . DIRECTORY_SEPARATOR . $vendor . "." . $package;
        if( is_dir( $full_vendor_package_path ) ) {
            // Directory already exists
            // errln
            $this->stdio->errln(" $vendor.$package Already exists");
            $this->getVendorDotPackage();
            // Lets get rid 0f the recursive calls that will occur :)
            return;
        }
        // Create directory recursive , Web , Cli
        mkdir($full_vendor_package_path . DIRECTORY_SEPARATOR . 'src/Web', 0755, TRUE );
        mkdir($full_vendor_package_path . DIRECTORY_SEPARATOR . 'src/Cli', 0755, TRUE );
        // all tests goes here
        mkdir($full_vendor_package_path . DIRECTORY_SEPARATOR . 'tests');
        // commands directory
        mkdir($full_vendor_package_path . DIRECTORY_SEPARATOR . 'commands');
        // scripts directory
        mkdir($full_vendor_package_path . DIRECTORY_SEPARATOR . 'scripts');
        // config directory
        mkdir($full_vendor_package_path . DIRECTORY_SEPARATOR . 'config');
        // All assets lies here
        mkdir($full_vendor_package_path . DIRECTORY_SEPARATOR . 'assets/images', 0755, TRUE );
        mkdir($full_vendor_package_path . DIRECTORY_SEPARATOR . 'assets/css', 0755, TRUE );
        mkdir($full_vendor_package_path . DIRECTORY_SEPARATOR . 'assets/js', 0755, TRUE );
        // Show message on success
        $this->stdio->outln("Congrats $vendor.$package created successfully!");
    }
}
