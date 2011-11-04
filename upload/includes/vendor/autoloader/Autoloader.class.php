<?php

/**
 * Autoloader is a class scanner with caching.
 * 
 * Sample Usage:
 * 
 *     <code>
 *     include_once('coughphp/extras/Autoloader.class.php');
 *     Autoloader::addClassPath('app_path/classes/');
 *     Autoloader::addClassPath('shared_path/classes/');
 *     Autoloader::setCacheFilePath('app_path/tmp/class_path_cache.txt');
 *     Autoloader::excludeFolderNamesMatchingRegex('/^CVS|\..*$/');
 *     spl_autoload_register(array('Autoloader', 'loadClass'));
 *     </code>
 * 
 * @package default
 * @author Anthony Bush, Wayne Wight
 * @copyright 2006-2008 Academic Superstore. This software is open source protected by the FreeBSD License.
 * @version 2008-09-22
 **/
class Autoloader
{
    protected static $root = '';
    protected static $classPaths = array();
    protected static $classFileSuffix = '.php';
    protected static $cacheFilePath = null;
    protected static $cacheFileName = null;
    protected static $cachedPaths = null;
    protected static $excludeFolderNames = '/^CVS|\..*$/'; // CVS directories and directories starting with a dot (.).
    protected static $hasSaver = false;

    public function setRoot($ro)
    {
        self::$root = $ro;
    }
    /**
     * Sets the paths to search in when looking for a class.
     * 
     * @param array $paths
     * @return void
     **/
    public static function setClassPaths($paths)
    {
        self::$classPaths = $paths;
    }

    /**
     * Adds a path to search in when looking for a class.
     * 
     * @param string $path
     * @return void
     **/
    public static function addClassPath($path)
    {
        self::$classPaths[] = self::$root . $path;
    }

    /**
     * Set the full file path to the cache file to use.
     * 
     * Example:
     * 
     *     <code>
     *     Autoloader::setCacheFilePath('/tmp/class_path_cache.txt');
     *     </code>
     * 
     * @param string $path
     * @return void
     **/
    public static function setCacheFilePath($path, $name)
    {
        self::$cacheFilePath = self::$root . $path;
        self::$cacheFileName = $name;
    }

    /**
     * Sets the suffix to append to a class name in order to get a file name
     * to look for
     * 
     * @param string $suffix - $className . $suffix = filename.
     * @return void
     **/
    public static function setClassFileSuffix($suffix)
    {
        self::$classFileSuffix = $suffix;
    }

    /**
     * When searching the {@link $classPaths} recursively for a matching class
     * file, folder names matching $regex will not be searched.
     * 
     * Example:
     * 
     *     <code>
     *     Autoloader::excludeFolderNamesMatchingRegex('/^CVS|\..*$/');
     *     </code>
     * 
     * @param string $regex
     * @return void
     **/
    public static function excludeFolderNamesMatchingRegex($regex)
    {
        self::$excludeFolderNames = $regex;
    }

    /**
     * Returns true if the class file was found and included, false if not.
     *
     * @return boolean
     **/
    public static function loadClass($className)
    {

        //don't load again!
        if (defined($className))
            return false;
        //search also in root,noob!
        $filePath = self::$root . $className;
        if (file_exists($filePath))
        {
            include ($filePath);
            define($className, true);
            return true;
        }
        $filePath = self::getCachedPath($className);
        if ($filePath && file_exists($filePath))
        {
            $filePath = self::getCachedPath($className);
            include ($filePath);
            define($className, true);
            return true;
        }

        foreach (self::$classPaths as $path)
        {
            $path = self::$root . $path;
            if ($filePath = self::searchForClassFile($className, $path))
            {
                self::$cachedPaths[$className] = $filePath;
                if (!self::$hasSaver)
                {
                    register_shutdown_function(array(__class__, 'saveCachedPaths'));
                    self::$hasSaver = true;
                }
                include ($filePath);
                define($className, true);
                return true;
            }
        }
        return false;
    }

    protected static function getCachedPath($className)
    {
        self::loadCachedPaths();
        if (isset(self::$cachedPaths[$className]))
        {
            return self::$cachedPaths[$className];
        }
        else
        {
            return false;
        }
    }

    protected static function loadCachedPaths()
    {
        if (is_null(self::$cachedPaths))
        {
            if (self::$cacheFilePath && self::$cacheFileName && is_file(self::$cacheFilePath . self::$cacheFileName))
            {
                self::$cachedPaths = unserialize(file_get_contents(self::$cacheFilePath . self::$cacheFileName));
            }
        }
    }

    /**
     * Write cached paths to disk.
     * 
     * @return void
     **/
    public static function saveCachedPaths()
    {
        if (!file_exists(substr(self::$cacheFilePath, 0, -1)))
            if (!mkdir(self::$cacheFilePath, 0755, true))
                echo ('Autoload cache folder not writable: ' . self::$cacheFilePath);
        $fileContents = serialize(self::$cachedPaths);
        $bytes = file_put_contents(self::$cacheFilePath .DIRECTORY_SEPARATOR. self::$cacheFileName, $fileContents);
        if ($bytes === false)
        {
            echo ('Autoloader could not write the cache file: ' . self::$cacheFilePath . self::$cacheFileName);
        }
    }

    protected static function searchForClassFile($className, $directory)
    {
        if (is_dir($directory) && is_readable($directory))
        {
            $d = dir($directory);
            while ($f = $d->read())
            {
                $subPath = $directory . $f;
                if (is_dir($subPath))
                {
                    // Found a subdirectory
                    if (!preg_match(self::$excludeFolderNames, $f))
                    {
                        if ($filePath = self::searchForClassFile($className, $subPath . DIRECTORY_SEPARATOR))
                        {
                            return $filePath;
                        }
                    }
                }
                else
                {
                    // Found a file
                    if ($f == $className . self::$classFileSuffix)
                    {
                        return $subPath;
                    }
                }
            }
        }
        return false;
    }
    public static function init()
    {
        spl_autoload_register(array(__class__, 'loadClass'));
    }

}

?>