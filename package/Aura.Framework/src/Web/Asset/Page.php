<?php
/**
 * 
 * This file is part of the Aura Project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Web\Asset;
use Aura\Framework\System;

/**
 * 
 * Provides a public interface to package assets.
 * 
 * Your package should be set up like this:
 * 
 *      Vendor.Package/
 *          assets/
 *              images/
 *              scripts/
 *              styles/
 *                  foo.css
 *          config/
 *          scripts/
 *          src/
 *          tests/
 * 
 * You can then use the URL `/asset/Vendor.Package/styles/foo.css` to access
 * the package asset, even though it's not in the web document root.
 * 
 * Additionally, you can cache the assets to the web document root, so that
 * they are served statically instead of through PHP.
 * 
 * @package Aura.Framework
 * 
 */
class Page extends \Aura\Web\Page
{
    /**
     * 
     * The Aura config modes in which we should cache assets.
     * 
     * @var array
     * 
     */
    protected $cache_config_modes = array();
    
    /**
     * 
     * The subdirectory inside the web document root where we should cache
     * assets.
     * 
     * @var array
     * 
     */
    protected $web_cache_dir;
    
    /**
     * 
     * Sets the System object.
     * 
     * @param Aura\Framework\System $system
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
     * Sets the config modes in which caching should take place.
     * 
     * @param array $modes An array of mode names.
     * 
     * @return void
     * 
     */
    public function setCacheConfigModes(array $modes = array())
    {
        $this->cache_config_modes = $modes;
    }
    
    /**
     * 
     * Sets the subdirectory in the web document root where assets should
     * be cached.
     * 
     * @param string $dir
     * 
     * @return void
     * 
     */
    public function setWebCacheDir($dir)
    {
        $this->web_cache_dir = $dir;
    }
    
    /**
     * 
     * Given a package name and an asset file name, delivers the asset
     * (and caches it if the config mode is correct).
     * 
     * @param string $package The package name (e.g., `Vendor.Package`).
     * 
     * @param string $file The asset file name (e.g. `images/logo.jpg`).
     * 
     * @return void
     * 
     */
    public function actionIndex($package = null, $file = null)
    {
        // get the real path to the asset
        $fakepath = $this->system->getPackagePath("$package/assets/$file");
        $realpath = realpath($fakepath);
        
        // does the asset file exist?
        if (! file_exists($realpath) || ! is_readable($realpath)) {
            $content = "Asset not found: "
                     . htmlspecialchars($fakepath, ENT_QUOTES, 'UTF-8');
            $this->response->setStatusCode(404);
            $this->response->setContent($content);
            return;
        }
        
        // are we in a config mode that wants us to cache?
        $config_mode = $this->context->getEnv('AURA_CONFIG_MODE', 'default');
        if (in_array($config_mode, $this->cache_config_modes)) {
            // copy source to this target cache location
            $path = $this->web_cache_dir . DIRECTORY_SEPARATOR
                  . $package . DIRECTORY_SEPARATOR
                  . $file;

            $webcache = $this->system->getWebPath($path);
        
            // make sure we have a dir for it
            $dir = dirname($webcache);
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            
            // copy from the source package to the target cache dir for the 
            // next time this package asset is requested
            copy($realpath, $webcache);
        }
        
        // get the asset contents
        $fh = fopen($realpath, 'rb');
        $content = '';
        while (! feof($fh)) {
            $content .= fread($fh, 8192);
        }
        fclose($fh);
        
        // set the response content, and done!
        $this->response->setContent($content);
    }
}
