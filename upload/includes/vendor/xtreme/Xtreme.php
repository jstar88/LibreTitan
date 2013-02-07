<?php

/**
 * Xtreme
 * 
 * @package Xtreme  
 * @author Covolo Nicola
 * @copyright Covolo Nicola
 * @license GNU v3 *[no profit]
 * @access public
 * @since 2011
 * @version 2.0 
 * -> static architecture for better performance;
 * -> single global cache;
 * -> fix the internal cache system;
 * -> multidimensional language array support;
 * -> now you can choose to throw an exception if a key in template don't exist in language file;
 * -> automatic OS-dipendent path separators replacement;
 * -> cache saved in php formatt for better performance;
 * @version 2.1
 * -> fixed a performance issue;
 * -> fixed the return string parsing an int number like function argument;
 * -> added some functions for test;  
 * -> more configurable template syntax;
 * -> replaced all object with array for better performance;
 * @version 2.2
 * -> internal structure merged from 3 to 1 array for better performance;
 * -> loop in template from php;
 * -> callback structure for the compile task
 */
abstract class Xtreme
{

    //----don't change these!----
    const SHOW_TAG = 'SHOW_TAG';
    const HIDE_TAG = 'HIDE_TAG';
    const DELETE_TAG = 'DELETE_TAG';
    const THROW_EXCEPTION = 'THROW_EXCEPTION';
    const PHP = 'PHP';
    const JSON = 'JSON';
    const XML = 'XML';
    const INI = 'INI';
    //---------------------------

    const TEMPLATE_CACHE_DIRECTORY = 'templates';
    const LANG_CACHE_DIRECTORY = 'langs';
    const DEFAULT_TEMPLATE = 'tpl';
    const DEFAULT_MASTER_LEFT = '{';
    const DEFAULT_MASTER_RIGHT = '}';
    const DEFAULT_ARRAY_LINK = '.';
    const DEFAULT_LANG_EXTENSION = self::JSON;
    const DEFAULT_LANGCACHE_ARRAYNAME = 'lang';
    const DEFAULT_FILE_PERMISSION = 0755;


    //--------only internal usage
    private static $groups_template;
    private static $languages;
    private static $readyCompiled;
    private static $hdd_access;
    private static $fList;
    private static $scripts;
    private static $csses;

    //--------external dependency
    private static $baseDirectory;
    private static $compileDirectory;
    private static $langDirectory;
    private static $langExtension;
    private static $templateExtension;
    private static $templateDirectories;
    private static $scriptsDirectory;
    private static $cssesDirectory;
    private static $useCache;
    private static $useCompileCompression;
    private static $config;
    private static $onInexistenceTag;
    private static $country;
    private static $langCacheArrayName;
    private static $filePermission;

    /**
     * Xtreme::init()
     * 
     * @return null
     */
    public static function init()
    {
        self::$baseDirectory = self::appendSeparator(dirname(__file__));
        self::$compileDirectory = self::$baseDirectory;
        self::$templateDirectories = self::$baseDirectory;
        self::$langDirectory = self::$baseDirectory;
        self::$templateExtension = self::DEFAULT_TEMPLATE;
        self::$langExtension = self::DEFAULT_LANG_EXTENSION;
        self::$langCacheArrayName = self::DEFAULT_LANGCACHE_ARRAYNAME;
        self::$readyCompiled = array();
        self::$groups_template = array();
        self::$useCache = true;
        self::$useCompileCompression = true;
        self::$config = array('master' => array('left' => self::DEFAULT_MASTER_LEFT, 'right' => self::DEFAULT_MASTER_RIGHT), 'arrayLink' => self::DEFAULT_ARRAY_LINK);
        self::$onInexistenceTag = self::HIDE_TAG;
        self::$country = '';
        self::$languages = array();
        self::$hdd_access = 0;
        self::$fList = array();
        self::$filePermission = self::DEFAULT_FILE_PERMISSION;
        self::$scriptsDirectory = self::$baseDirectory;
        self::$csses=self::$baseDirectory;
        self::$scripts = array('default' => array());
        self::$csses = array('default' => array());
    }
    //-------------PATH FUNCTIONS---------------
    /**
     * Xtreme::appendSeparator()
     * Function used to append a separator at the end of path if it there isn't
     * 
     * @param mixed $path
     * @return the path
     */
    private static function appendSeparator($path)
    {
        if (substr($path, -1) != DIRECTORY_SEPARATOR)
            $path .= DIRECTORY_SEPARATOR;
        return $path;
    }
    /**
     * Xtreme::makeAbsolute()
     * Function used to added the root directory to path that don't have the directory separator at the start position
     * @param mixed $path
     * @return the path
     */
    private static function makeAbsolute($path)
    {
        return ($path{0} != DIRECTORY_SEPARATOR) ? self::$baseDirectory . $path : $path;
    }
    /**
     * Xtreme::fixSeparators()
     * Function used to make the paths OS-indipendeds
     * 
     * @param mixed $path
     * @return the path
     */
    private static function fixSeparators($path)
    {
        return trim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path));
    }
    /**
     * Xtreme::sanitizePath()
     * Function used to sanitize the input path. This function iglobe the above declarated functions
     * 
     * @param mixed $path
     * @return null
     */
    private static function sanitizePath($path)
    {
        return self::makeAbsolute(self::appendSeparator(self::fixSeparators($path)));
    }

    /**
     * Xtreme::sanitizeFoolder()
     * Function used to sanitize the input foolder. must be like "folder/*"
     * 
     * @param mixed $foolder
     * @return null
     */
    private static function sanitizeFoolder($foolder)
    {
        if ($foolder{0} == DIRECTORY_SEPARATOR)
            $foolder = substr($foolder, 1);
        return self::appendSeparator(self::fixSeparators($foolder));
    }

    /**
     * Xtreme::sanitizeRoot()
     * Function used to sanitize the input root. must be like "*rootpath/*"
     * 
     * @param mixed $path
     * @return null
     */
    private static function sanitizeRoot($path)
    {
        return self::appendSeparator(self::fixSeparators($path));
    }

    /**
     * Xtreme::getTplPath()
     * Function to know the complete path of template.
     * 
     * @param mixed $template
     * @return The complete path of the template passed. 
     */
    private static function getTplPath($template)
    {
        return self::$templateDirectories . self::fixSeparators($template) . '.' . self::$templateExtension;
    }

    /**
     * Xtreme::getCompiledTplPath()
     * Function to know the complete path of compiled templates(caches).
     * 
     * @param mixed $template
     * @return he complete path of compiled templates passed.
     */
    private static function getCompiledTplPath($template)
    {
        return self::$compileDirectory . self::TEMPLATE_CACHE_DIRECTORY . DIRECTORY_SEPARATOR . self::fixSeparators($template) . '.php';
    }

    /**
     * Xtreme::getLangPath()
     * Function to know the complete path of language file.
     * 
     * @param mixed $lang
     * @return The complete path of the language file passed.
     */
    private static function getLangPath($lang)
    {
        return self::$langDirectory . self::$country . self::fixSeparators($lang) . '.' . strtolower(self::$langExtension);
    }

    /**
     * Xtreme::getCompiledLangPath()
     * Function to know the complete path of compiled language file(caches).
     * 
     * @param mixed $lang
     * @return  The complete path of compiled language file passed.
     */
    private static function getCompiledLangPath($lang)
    {
        return self::$compileDirectory . self::LANG_CACHE_DIRECTORY . DIRECTORY_SEPARATOR . self::$country . self::fixSeparators($lang) . '.' . strtolower(self::JSON);
    }
    private static function getCssPath($name){
        return self::$cssesDirectory.self::fixSeparators($name).'.css';   
    }
    private static function getScriptPath($name){
        return self::$cssesDirectory.self::fixSeparators($name).'.js';   
    }
    //-----------------------------------------

    //-------USER PREFERENCE FUNCTIONS
    /**
     * Xtreme::setBaseDirectory()
     * 
     * @param mixed $new
     * @return null
     */
    public static function setBaseDirectory($new)
    {
        self::$baseDirectory = self::sanitizeRoot($new);
    }
    /**
     * Xtreme::setCachesDirectory()
     * 
     * @param mixed $new
     * @return null
     */
    public static function setCachesDirectory($new)
    {
        self::$compileDirectory = self::sanitizePath($new);
    }
    /**
     * Xtreme::setLanguagesDirectory()
     * 
     * @param mixed $new
     * @return null
     */
    public static function setLanguagesDirectory($new)
    {
        self::$langDirectory = self::sanitizePath($new);
    }
    /**
     * Xtreme::setTemplatesDirectory()
     * 
     * @param mixed $new
     * @return null
     */
    public static function setTemplatesDirectory($new)
    {
        self::$templateDirectories = self::sanitizePath($new);
    }
    public static function setScriptsDirectory($new)
    {
        self::$scriptsDirectory = self::sanitizePath($new);
    }
    public static function setCssDirectory($new)
    {
        self::$cssesDirectory = self::sanitizePath($new);
    }
    /**
     * Xtreme::setTemplateExtension()
     * 
     * @param mixed $new
     * @return null
     */
    public static function setTemplateExtension($new)
    {
        self::$templateExtension = ($new{0} == '.') ? substr($new, 1) : $new;
    }
    /**
     * Xtreme::setLangExtension()
     * 
     * @param mixed $new
     * @return null
     */
    public static function setLangExtension($new)
    {
        self::$langExtension = constant("self::$new");
    }
    /**
     * Xtreme::setConfig()
     * 
     * @param mixed $new
     * @return null
     */
    public static function setConfig($new)
    {
        self::$config = $new;
    }
    /**
     * Xtreme::setOnInexistenceTagEvent()
     * 
     * @param mixed $new
     * @return null
     */
    public static function setOnInexistenceTagEvent($new)
    {
        self::$onInexistenceTag = constant("self::$new");
    }
    /**
     * Xtreme::useCache()
     * 
     * @param mixed $status
     * @return null
     */
    public static function useCache($status)
    {
        self::$useCache = $status;
    }
    /**
     * Xtreme::useCompileCompression()
     * 
     * @param mixed $status
     * @return null
     */
    public static function useCompileCompression($status)
    {
        self::$useCompileCompression = $status;
    }

    /**
     * Xtreme::switchCountry()
     * Function used to switch language foolder.if second param is true then keys in memory of old language will be cleaned.
     * 
     * @param mixed $country
     * @param bool $cleanOld
     * @return null
     */
    public static function switchCountry($country, $cleanOld = false)
    {
        $country = self::sanitizeFoolder($country);
        if ($cleanOld && isset(self::$languages[$country]))
        {
            unset(self::$languages[$country]);
        }
        self::$country = $country;
        if (!isset(self::$languages[$country]))
        {
            self::$languages[$country] = array();
        }
    }

    /**
     * Xtreme::setFilePermission()
     * Function used to set the file and folder permission when created.
     * Default: 0755
     * 
     * @param mixed $id
     * @return nul
     */
    public static function setFilePermission($id)
    {
        self::$filePermission = $id;
    }
    //------------------------------------------

    //-----------USEFUL FUNCTIONS FOR BUILDING PAGES
    public static function get()
    {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        if ($numargs == 0)
            throw new Exception('Function get() must be called with at least 1 arguments');
        if (!isset(self::$languages[self::$country][$arg_list[0]]))
        {
            return self::errorManagement($arg_list, $numargs);
        }
        $return = self::$languages[self::$country][$arg_list[0]];
        for ($i = 1; $i < $numargs; $i++)
        {
            if (!isset($return[$arg_list[$i]]))
            {
                return self::errorManagement($arg_list, $numargs);
            }
            $return = $return[$arg_list[$i]];
        }
        return $return;
    }

    private static function buildArrayString($arg_list, $numargs)
    {
        $return = $arg_list[0];
        for ($i = 1; $i < $numargs; $i++)
        {
            $param = $arg_list[$i];
            if (!is_numeric($param))
                $param = "'$param'";
            $return .= "[$param]";
        }
        return $return;
    }
    private static function errorManagement($arg_list, $numargs)
    {
        $return = '';
        switch (self::$onInexistenceTag)
        {
            case self::HIDE_TAG:
                $return = self::buildArrayString($arg_list, $numargs);
                $return = "<!--$return-->";
                break;
            case self::DELETE_TAG:
                $return = '';
                break;
            case self::SHOW_TAG:
                $return = self::buildArrayString($arg_list, $numargs);
                $return = "{$return}";
                break;
            case self::THROW_EXCEPTION:
                $desc = self::buildArrayString($arg_list, $numargs);
                throw new Exception('Tryed to access to null language reference: ' . $desc);
                break;
            default:
                break;
        }
        return $return;
    }

    /**
     * Xtreme::assignLangFile()
     * Assign a language file
     * 
     * @param mixed $path
     * @param mixed $phpVars
     * @return null
     */
    public static function assignLangFile($path, $phpVars = null)
    {
        $langPath = self::getLangPath($path);
        $langCompiledPath = self::getCompiledLangPath($path);
        $lang = '';
        if (self::$langExtension != self::PHP)
        {
            if (file_exists($langCompiledPath))
            {
                $lang = self::open_PHP($langCompiledPath, self::$langCacheArrayName);
            } elseif (file_exists($langPath))
            {
                $function = "open_" . self::$langExtension;
                $lang = self::$function($langPath, $phpVars);
                self::saveAsPHP($langCompiledPath, $lang);
            }
            else
                die('Lang (' . $langPath . ') not found ');
        }
        else
        {
            $lang = self::open_PHP($langPath, $phpVars);
        }
        self::assign($lang);
    }

    /* @example
    Xtreme assign('key','value');
    Xtreme::assign(array('key1'=>'value1','key2'=>'value2'));
    Xtreme::assin(new stdClass());
    Xtreme::assign('key',array('value1','value2','value3'));
    */
    /**
     * Xtreme::assign()
     * Assign a key-value. The key will be replaced with value contents when the bufferedOutput is called
     * 
     * @param mixed $key
     * @param string $value
     * @return null
     */
    public static function assign($key, $value = '')
    {
        if (is_array($key))
        {
            foreach ($key as $n => $v)
                self::$languages[self::$country][$n] = $v;
        } elseif (is_object($key))
        {

            foreach (get_object_vars($key) as $n => $v)
                self::$languages[self::$country][$n] = $v;
        } elseif (is_array($value))
        {
            foreach ($value as $k => $v)
            {
                self::$languages[self::$country][$key][$k] = $v;
            }
        }
        else
            self::$languages[self::$country][$key] = $value;
    }
    public static function addScript($script)
    {
        if (self::$scripts[self::$country]['default'] != $script)
        {
            self::$scripts[self::$country]['default'] = $script;
        }
    }
    public static function addScripts($scripts)
    {
        foreach ($scripts as $script)
        {
            self::addScript($script);
        }
    }
    public static function addScriptToGroup($script, $id)
    {
        if (!isset(self::$scripts[self::$country][$id]) || self::$scripts[self::$country][$id] != $script)
        {
            self::$scripts[self::$country][$id] = $script;
        }
    }
    public static function addScriptsToGroup($scripts, $id)
    {
        foreach ($scripts as $script)
        {
            self::addScriptToGroup($script, $id);
        }
    }
    public static function addScriptGroups($groups)
    {
        foreach ($groups as $groupID => $scripts)
        {
            self::$scripts[self::$country][$groupID] = $scripts;
        }
    }
    public static function addCss($css)
    {
        if (self::$csses[self::$country]['default'] != $css)
        {
            self::$csses[self::$country]['default'] = $css;
        }
    }
    public static function addCsses($csses)
    {
        foreach ($csses as $css)
        {
            self::addCss($css);
        }
    }
    public static function addCssToGroup($css, $id)
    {
        if (!isset(self::$csses[self::$country][$id]) || self::$csses[self::$country][$id] != $css)
        {
            self::$csses[self::$country][$id] = $css;
        }
    }
    public static function addCssesToGroup($csses, $id)
    {
        foreach ($csses as $css)
        {
            self::addCssToGroup($css, $id);
        }
    }
    public static function addCssGroups($groups)
    {
        foreach ($groups as $groupID => $csses)
        {
            self::$csses[self::$country][$groupID] = $csses;
        }
    }

    /**
     * Xtreme::append()
     * Function to append a new value to the corrispective key passed 
     * 
     * @param mixed $key
     * @param string $value
     * @return null
     */
    public static function append($key, $value = '')
    {
        if (!isset(self::$languages[self::$country][$key]))
        {
            self::$languages[self::$country][$key] = '';
        }
        self::$languages[self::$country][$key] .= $value;
    }

    /**
     * Xtreme::push()
     * Push a value in a array placed in passed key
     * 
     * @param mixed $key
     * @param mixed $value
     * @return null
     */
    public static function push($key, $value = null)
    {
        if (!isset(self::$languages[self::$country][$key]))
        {
            self::$languages[self::$country][$key] = array();
        }
        $data = self::$languages[self::$country][$key];
        $data[] = $value;
        self::$languages[self::$country][$key] = $data;
    }

    /**
     * Xtreme::output()
     * Get the html from parsed templates.
     * 
     * @param mixed $templates : an Array of templates that will be parsed and appended to html.
     * @param bool $reuse : if true, the parsed template is saved in internal variable for next fast usage
     * @param bool $draw : if true, output the html in screen.
     * @return html if draw option is set to false, nothing otherwise.
     */
    public static function output($templates, $reuse = false, $draw = false, $forGroup = false, $groupId = false)
    {
        if (!is_array($templates))
            $templates = explode('|', $templates);
        $out = '';
        foreach ($templates as $template)
        {
            if ($forGroup)
            {
                $templateName = self::getGroupCacheName($groupId, $template);
                $compiledTemplateFile = self::getCompiledTplPath(md5($templateName));
                $templateFile = self::getTplPath($template);
            }
            else
            {
                $compiledTemplateFile = self::getCompiledTplPath($template);
                $templateFile = self::getTplPath($template);
            }

            if (isset(self::$readyCompiled[$compiledTemplateFile]) && $reuse)
                $out .= self::$readyCompiled[$compiledTemplateFile]['code'];
            elseif (file_exists($compiledTemplateFile) && filemtime($compiledTemplateFile) >= filemtime($templateFile) && self::$useCache)
            {
                $tmp = self::bufferedOutput($compiledTemplateFile);
                $out .= $tmp;
                if ($reuse)
                {
                    self::$readyCompiled[$compiledTemplateFile]['code'] = $tmp;
                }
            } elseif (file_exists($templateFile))
            {
                if ($forGroup)
                {
                    self::compileGroup($groupId);
                }
                $phpcont = self::compile($templateFile);
                self::save($compiledTemplateFile, $phpcont);
                $tmp = self::bufferedOutput($compiledTemplateFile);
                $out .= $tmp;
                if ($reuse)
                {
                    self::$readyCompiled[$compiledTemplateFile]['code'] = $tmp;
                }
            }
            else
                die('Template (' . $templateFile . ') not found ');
        }
        if (!$draw)
            return $out;
        echo $out;
    }
    public static function assignForReuse($templateName, $key, $value = null)
    {
        //if cache don't exist then this is a pre-assign
        $templateName = self::getCompiledTplPath($templateName);

        if (!isset(self::$readyCompiled[$templateName]))
        {
            self::assign($key, $value);
        }
        else
        {
            if (is_array($key))
            {
                foreach ($key as $n => $v)
                    self::replaceCacheValues($templateName, $n, $v);
            } elseif (is_object($key))
            {
                foreach (get_object_vars($key) as $n => $v)
                    self::replaceCacheValues($templateName, $n, $v);
            } elseif (is_array($value))
            {
                foreach ($value as $n => $v)
                    self::replaceCacheValues($templateName, $n, $v);
            }
            else
            {
                self::replaceCacheValues($templateName, $key, $value);
            }
        }

    }

    public static function assignToGroup($groupId, $blockId, $templateName = '')
    {

        if (!isset(self::$groups_template[$groupId]))
            self::$groups_template[$groupId] = array();

        if (is_array($blockId))
        {
            foreach ($blockId as $n => $v)
            {
                self::$groups_template[$groupId][$n]['template'] = self::getTplPath($v);
            }
        } elseif (is_object($blockId))
        {
            foreach (get_object_vars($blockId) as $n => $v)
                self::$groups_template[$groupId][$n]['template'] = self::getTplPath($v);
        }
        else
        {
            self::$groups_template[$groupId][$blockId]['template'] = self::getTplPath($templateName);
        }
    }
    public static function assignGroupToGroup($startGroup, $startTemplate, $toGroup, $key)
    {
        $startTemplate = self::getTplPath($startTemplate);
        $CacheName = self::getGroupCacheName($startGroup, $startTemplate);
        self::$groups_template[$toGroup][$key]['cacheName'] = $CacheName;
        self::$groups_template[$toGroup][$key]['children_group'] = $startGroup;
        self::$groups_template[$toGroup][$key]['template'] = $startTemplate;
    }
    public static function doLoopGroup($groupId, $key, $loop_valueName, $loop_keyName = '')
    {
        if (!isset(self::$groups_template[$groupId]))
            throw new Exception("il gruppo contenitore: $groupId non esiste");
        if (!isset(self::$groups_template[$groupId][$key]))
            throw new Exception("la chiave : $key alla quale corrisponde il contenuto iterato non esiste");
        self::$groups_template[$groupId][$key]['foreach']['loop_valueName'] = $loop_valueName;
        self::$groups_template[$groupId][$key]['foreach']['loop_keyName'] = $loop_keyName;
    }

    public static function outputGroup($groupId, $template, $reuse = false, $draw = false)
    {
        return self::output($template, $reuse, $draw, true, $groupId);
    }

    public static function clearCurrentLanguage()
    {
        self::$languages[self::$country] = array();
    }

    public static function clearReadyCompiled()
    {
        self::$readyCompiled = array();
    }

    public static function clearGroups()
    {
        self::$groups_template = array();
    }

    //----------FILES FUNCTION

    /**
     * Xtreme::saveAsPHP()
     * Save an array to php code
     * 
     * @param mixed $path
     * @param mixed $array
     * @return null
     */
    private static function saveAsPHP($path, $array)
    {
        $page = '<?php';
        $page .= self::transformArrayToPHP($array, '$' . self::$langCacheArrayName);
        $page .= '?>';
        self::save($path, $page);
    }

    /**
     * Xtreme::transformArrayToPHP()
     * transform recursively an array to php code
     * 
     * @param mixed $array : the array to parse
     * @param mixed $string : the rappresentation of passed array in php code
     * @return null
     */
    private static function transformArrayToPHP($array, $string)
    {
        foreach ($array as $key => $value)
        {
            $string .= '[\'' . $key . '\']=';
            if (is_array($value))
            {
                self::transformArrayToPHP($value, $string);
            }
            $string .= $value . ';';
        }
    }

    /**
     * Xtreme::save()
     * Recursively create folders path and save the contents to target file
     * 
     * @param mixed $file : target file complete path es('/myfolder/myfile.json') 
     * @param mixed $content : the contents that you want to save in passed file path
     * @return null
     */
    private static function save($file, $content)
    {
        $path = substr($file, 0, strrpos($file, DIRECTORY_SEPARATOR, -1));
        if (!file_exists($path) && mkdir($path, self::$filePermission, true) === false)
            throw new Exception("failed to create [$path] directory");
        if (file_put_contents($file, $content) === false)
            throw new Exception("failed to save [$file]");
    }

    /**
     * Xtreme::open_PHP()
     * Open a php file and recursively merge multidimensional values of differents array in one.
     * 
     * @param mixed $path : the target file path.
     * @param mixed $phpVars : an array containg the arrays name in php file 
     * @return the created array.
     */
    private static function open_PHP($path, $phpVars)
    {
        $container = array();
        require ($path);
        foreach ($phpVars as $var)
            if (isset($$var))
                $container = array_merge_recursive($container, $$var);
        return $container;
    }

    /**
     * Xtreme::open_JSON()
     * Open a json file and decode its content to multidimensional array.
     * 
     * @param mixed $path :the path of json file.
     * @return the created array.
     */
    private static function open_JSON($path)
    {
        return json_decode(file_get_contents($path), true);
    }

    /**
     * Xtreme::open_XML()
     * Open a xml file and decode its content to multidimensional array.
     * 
     * @param mixed $path :the path of xml file.
     * @return the created array.
     */
    private static function open_XML($path)
    {
        return simplexml_load_file($path);
    }


    /**
     * Xtreme::open_INI()
     * Open a ini file and decode its content to multidimensional array.
     * 
     * @param mixed $path :the path of ini file.
     * @return the created array.
     */
    private static function open_INI($path)
    {
        if (function_exists('parse_ini_string'))
            return parse_ini_string(file_get_contents($path), true);
        else
            return parse_ini_file($path, true);
    }
    //--------------------------------------------

    //----------- PARSE FUNCTIONS
    private static function replaceCacheValues($templateName, $key, $value)
    {
        //if the key don't exist in the map then map it
        if (!isset(self::$readyCompiled[$templateName]['map'][$key]))
        {
            self::$readyCompiled[$templateName]['map'][$key] = self::getKeyMap(self::$readyCompiled[$templateName]['code'], self::get($key));
        }
        //replacing
        foreach (self::$readyCompiled[$templateName]['map'][$key] as $position)
        {
            $tmp = substr_replace(self::$readyCompiled[$templateName]['code'], $value, $position);
        }
        if (!empty(self::$readyCompiled[$templateName]['map'][$key]))
            self::$readyCompiled[$templateName]['code'] = $tmp;
    }
    private static function getKeyMap($code, $value)
    {
        $positions = array();
        if (empty($value))
            return $positions;
        //fixing php stupid sense
        $first = strpos($code, $value);
        if ($first !== false && $first == 0)
        {
            $positions[] = 0;
        }
        //end
        while ($pos = strpos($code, $value))
        {
            $positions[] = $pos;
            $code = substr($code, $pos + strlen($value));
        }
        return $positions;
    }
    private static function compile($string)
    {
        self::$hdd_access++;
        $lines = file($string);
        $newLines = array();
        $matches = null;
        $masterLeft = self::$config['master']['left'];
        $masterRight = self::$config['master']['right'];
        $regex = "/\\{$masterLeft}([^{$masterLeft}{$masterRight}]+)\\{$masterRight}/";

        foreach ($lines as $line)
        {
            $num = preg_match_all($regex, $line, &$matches);
            if ($num > 0)
            {
                for ($i = 0; $i < $num; $i++)
                {
                    $match = $matches[0][$i];
                    if (strpos($matches[1][$i], ';') !== false)
                        continue;
                    $new = self::transformSyntax($matches[1][$i]);
                    $line = str_replace($match, $new, $line);
                }
            }
            $newLines[] = $line;
        }
        if (self::$useCompileCompression)
            return self::html_compress(implode('', $newLines));
        else
            return implode('', $newLines);
    }

    private static function compileGroup($groupId)
    {
        if (isset(self::$groups_template[$groupId]))
        {
            foreach (self::$groups_template[$groupId] as $blockId => $info)
            {
                if (isset($info['children_group']))
                {
                    self::compileGroup($info['children_group']);
                }

                if (isset($info['foreach']))
                    self::assign($groupId, array($blockId => self::buildForeach(self::compile($info['template']), $blockId, $info['foreach']['loop_valueName'], $info['foreach']['loop_keyName'])));
                else
                    self::assign($groupId, array($blockId => self::compile($info['template'])));
                unset(self::$groups_template[$groupId[$blockId]]);
            }
        }
    }
    private static function buildForeach($compiledCode, $arrayName, $loop_valueName, $loop_keyName)
    {
        if (!empty($loop_keyName))
            return "<?php foreach (self::get('$arrayName') as $$loop_keyName => $loop_valueName) { ?> $compiledCode <?php } ?>";
        return "<?php foreach (self::get('$arrayName') as $$loop_valueName){ ?> $compiledCode  <?php } ?>";
    }
    private static function getGroupCacheName($groupId, $template)
    {
        $paths = "";
        if (isset(self::$groups_template[$groupId]))
        {
            foreach (self::$groups_template[$groupId] as $blockId => $info)
            {
                if (isset($info['cacheName']))
                    $paths .= $info['cacheName'];
                else
                    $paths .= $info['template'];
            }
        }
        return $template . $paths;
    }

    private static function html_compress($html)
    {
        preg_match_all('!(<(?:code|pre).*>[^<]+</(?:code|pre)>)!', $html, $pre);
        $html = preg_replace('!<(?:code|pre).*>[^<]+</(?:code|pre)>!', '#pre#', $html); //ok
        $html = preg_replace('#<!â€“[^\[].+â€“>#', "", $html); //ok
        $html = preg_replace('/ {2,}/', ' ', $html); //ok
        $html = str_replace(array('\r', '\n', '\t'), '', $html);
        $html = preg_replace('/>[\s]+</', '><', $html); //ok
        if (!empty($pre[0]))
            foreach ($pre[0] as $tag)
                $html = preg_replace('!#pre#!', $tag, $html, 1);
        return $html;

    }
    private static function replace_callback($args)
    {
        $function_before = $args[1];
        $function_arg = $args[2];
        $function_arg = preg_replace_callback('/([a-zA-Z0-9_]*)\\' . self::$config['arrayLink'] . '/i', 'self::callback_1', $function_arg);
        $function_arg = preg_replace_callback('/[,]([a-zA-Z0-9_]*[^,])/i', 'self::callback_2', $function_arg);
        if (strpos($function_arg, ',') === false)
            $function_arg = self::single_param_replace($function_arg);
        return "{$function_before}self::get($function_arg)";
    }
    private static function callback_1($args)
    {
        return self::single_param_replace($args[1]) . ',';
    }
    private static function callback_2($args)
    {
        return ',' . self::single_param_replace($args[1]);
    }
    private static function single_param_replace($param)
    {
        if (!is_numeric($param))
            return "'$param'";
        return $param;
    }

    private static function transformSyntax($input)
    {
        $from = '/(^|\[|,|\(|\+| )([a-zA-Z0-9_\\' . self::$config['arrayLink'] . ']*)($|\.|\)|\[|\]|\+)/';
        $to = 'self::replace_callback';

        $parts = explode(':', $input);

        $string = '';
        switch ($parts[0])
        {
            case 'if':
            case 'switch':
                $string = '<?php ' . $parts[0] . '(' . preg_replace_callback($from, $to, $parts[1]) . ') { ' . ($parts[0] == 'switch' ? 'default: ?>' : ' ?>');
                break;
            case 'foreach':
                $pieces = explode(',', $parts[1]);
                $string = '<?php foreach(' . preg_replace_callback($from, $to, $pieces[0]) . ' as ';
                $string .= '$' . $pieces[1];
                if (sizeof($pieces) == 3)
                {
                    $string .= '=>' . '$' . $pieces[2];
                    self::$fList[$pieces[2]] = $pieces[2];
                }
                $string .= ') {  ?>';
                self::$fList[$pieces[1]] = $pieces[1];

                break;
            case 'end':
            case 'endswitch':
                $string = '<?php } ?>';
                break;
            case 'else':
                $string = '<?php } else { ?>';
                break;
            case 'case':
                $string = '<?php break; case ' . preg_replace_callback($from, $to, $parts[1]) . ': ?>';
                break;
            case 'include':
                $string = '<?php echo self::output("' . $parts[1] . '"); ?>';
                break;
            case 'group':
                $string = self::get($parts[1], $parts[2]);
                break;
            case 'loop':
                $param = explode('.', $parts[1]);
                $string = self::buildArrayString($param, count($param));
                $string = "<?php echo $$string ?>";
                break;
            case 'script':
                $string = '<?php echo self::getScripts('.$parts[1].'); ?>';
                break;
            case 'css':
                $string = '<?php echo self::getCsses('.$parts[1].'); ?>';
                break;
            case 'addScript':
                if(isset($parts[2]))
                    self::addScriptToGroup($parts[2],$parts[1]);
                else
                    self::addScript($parts[1]);
                break;
            case 'addCss':
                if(isset($parts[2]))
                    self::addCssesToGroup($parts[2],$parts[1]);
                else    
                    self::addCss($parts[2]);
                break;
            default:
                $string = '<?php echo ' . preg_replace_callback($from, $to, $parts[0]) . '; ?>';
                break;
        }
        return $string;
    }
    private static function getCsses($group='')
    {
        $string='';
        if(empty($group)){
            $group='default';    
        }
        foreach(self::$csses[self::$country][$group] as $css){
                $string.=self::makeCss($css);    
        }    
        return $string;
    }
    private static function getScripts($group='')
    {
        $string='';
        if(empty($group)){
            $group='default';    
        }
        foreach(self::$scripts[self::$country][$group] as $script){
                $string.=self::makeScript($script);    
        }    
        return $string;
    }
    private static function makeCss($lnk){
        $lnk=self::getCssPath($lnk);
        return "<style>$lnk</style>";    
    }
    private static function makeScript($lnk){
        $lnk=self::getScriptPath($lnk);
        return "<script>$lnk</script>";    
    }

    private static function bufferedOutput($compiledFile)
    {
        self::$hdd_access++;
        ob_start();
        include ($compiledFile);
        $out = ob_get_clean();
        return $out;
    }
    //--------------------------------------------

    //------------- FUNCTIONS FOR TESTING
    public static function getHddAccess()
    {
        return self::$hdd_access;
    }
    public static function keyExist($key)
    {
        return isset(self::$languages[self::$country][$key]);
    }
}

?>