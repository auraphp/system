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
use Aura\Cli\Getopt;
use Aura\Cli\Option;
use Aura\Framework\System;
use Aura\Framework\Exception\TestFileNotFound;

/**
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
        $this->getVendorDotPackage();
    }
    
    /**
     * 
     * Get Vendor.Package
     *
     */
    public function getVendorDotPackage()
    {
        $this->stdio->out('Please enter vendor.package name: ');
        $input = $this->stdio->in();
        switch( $input ) {
            case 'help':
            case '--h':
            case '--help':
            case '':
                $this->showHelp();
                $this->getVendorDotPackage();
                break;
            case 'exit':
                $this->showExit();
                break;
            default :
                $this->createVendorDotPackage($input);
        }
    }
    
    public function showHelp()
    {
        $this->stdio->outln('Eg for Vendor.Package are Aura.Cli , Aura.Router');
        $this->stdio->outln('Vendor can be your name, your company name etc');
    }
    
    public function showExit()
    {
        $this->stdio->outln('Exiting!');
    }
    
    public function createVendorDotPackage($vendor_package)
    {
        list($vendor, $package) = explode( ".", strtolower($vendor_package) );
        $vendor = ucfirst($vendor);
        $package = ucfirst($package);
        // Get Package Path
        $package_path = $this->system->getPackagePath();
        $full_vendor_package_path = $package_path . DIRECTORY_SEPARATOR . $vendor . "." . $package;
        if( is_dir( $full_vendor_package_path ) ) {
            // Directory already exists
            // errln
            $this->stdio->errln(" $vendor.$package Already exists");
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
