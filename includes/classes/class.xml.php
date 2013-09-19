<?php

/**
 * @project XG Proyect
 * @version 2.10.x build 0000
 * @copyright Copyright (C) 2008 - 2012
 */

/**
 * 
 * @author Jstar
 * @version v2
 * @tutorial
 *   $c=xml::getInstance('config.xml');
 *   echo $c->get_config('version');
 *   $c->write_config('version','blabla');
 *   echo $c->get_config('version');
 */
 
if(!defined('INSIDE')){ die(header("location:../../"));} 
 
class xml
{
    //an istance of this class: see singleton pattern
    private static $instance = array();
    //the complete path to xml config: used to load and save it
    private $path;
    //SimpleXMLElement object that rappresent xml config
    private $config;
    //
    private $concurrency_safe;

    /**
     * xml->__construct()
     * Constructor: access is private to enable class istancing only by getInstance() method, to ensure better performace
     * 
     * @param String $sheet
     * @return null
     */
    private function __construct($sheet, $concurrency_safe)
    {
        $this->concurrency_safe = $concurrency_safe;
        $this->path = $sheet;
        $this->load_xml();
        chmod($this->path,0644); // Read and write for owner, read for everybody else
    }
    /**
     * xml->load_xml()
     * Load and parse the xml from the current path
     * 
     * @param null
     * @return null
     */
    private function load_xml()
    {
        if (!file_exists($this->path))
        {
            throw new Exception('Error: xml file doesn\'t exist');
        }
        if (!is_readable($this->path))
        {
            throw new Exception('Error: xml file is not readble');
        }

        if (!$this->concurrency_safe)
        {
            $this->config = simplexml_load_file($this->path);
        }
        else
        {

            $fo = fopen($this->path, 'r');
            flock($fo, LOCK_SH);
            $cts = file_get_contents($this->path);
            flock($fo, LOCK_UN);
            fclose($fo);
            $this->config = simplexml_load_string($cts);
        }
        if ($this->config === false) throw new Exception('Error parsing xml file');
    }
    /**
     * xml->save_xml()
     * Save the xml to the current path
     * 
     * @param null
     * @return null
     */
    private function save_xml()
    {
        if (!is_writable($this->path))
        {
            throw new Exception('Error: xml file is not writable');
        }
        if (!$this->concurrency_safe)
        {
            if ($this->config->asXML($this->path) === false) throw new Exception('Error: there are syntax errors on xml file');
        }
        else
        {
            $fp = fopen($this->path, "w");
            // acquire an exclusive lock
            if (flock($fp, LOCK_EX))
            { 
                $content = $this->config->asXML();
                if ($content === false)
                {
                    throw new Exception('Error: there are syntax errors on xml file');
                }
                fwrite($fp, $content);
                fflush($fp); // flush output before releasing the lock
                flock($fp, LOCK_UN); // release the lock
                fclose($fp);
            }
            //if is not possible then wait for 0.2 seconds and then retray
            else
            {
                fclose($fp);
                usleep(200000);
                $this->save_xml();
            }
        }
    }
    /**
     * xml->get_xml_entity()
     * Search in the xml for a entity rappresented by $config_name 
     * 
     * @param String $config_name: the key
     * @return SimpleXMLElement object
     */
    private function get_xml_entity($config_name, $can_add = false)
    {
        //searching inside <configurations> and where config name=$config_name
        $result = $this->doXpathQuery('/configurations/config[name="' . $config_name . '"]');
        //if don't exist create it

        if (empty($result))
        {
            if ($can_add)
            {
                $new_conf = $this->config->addChild('config');
                $new_conf->addChild('name', $config_name);
                $new_conf->addChild('value');
                $result = $new_conf;
            }
            else
            {
                throw new Exception(sprintf('Item with id "%s" does not exists.', $config_name));
            }
        }
        //if multiple result are returned so key is not unique
        elseif (count($result) !== 1)
        {
            throw new Exception(sprintf('Item with id "%s" is not unique.', $config_name));
        }
        list($result) = $result;
        return $result;
    }
    /**
     * xml->doXpathQuery()
     * This function execute a Xpath query
     * 
     * @param String $query
     * @return Array
     */
    public function doXpathQuery($query)
    {
        $result = $this->config->xpath($query);
        if ($result === false)
        {
            throw new Exception('there is an error in the xpath query');
        }
        return $result;
    }
    /**
     * xml->get_config()
     * This function search in loaded xml for a value according to specific configuration name passed
     * 
     * @param String $config_name
     * @return String: the configuration value of given key 
     */
    public function get_config($config_name)
    {
        // (string) is a cast to String type from SimpleXMLElement object: we need this to extract value
        return (string )$this->get_xml_entity($config_name)->value;
    }
    /**
     * xml->get_configs()
     * This function return all configurations loaded from xml file
     * 
     * @return Array: an associative array of key-value
     */
    public function get_configs()
    {
        $config = array();
        $x = $this->config->children();
        foreach ($x as $xmlObject)
        {
            $config[(string )$xmlObject->name] = (string )$xmlObject->value;
        }
        return $config;
    }
    /**
     * xml->write_config()
     * This function write the xml configuration file updating one or multiple key-value at time
     * 
     * @param mixed $config_name : String for single update or an associative array of key=>value 
     * @param String $config_value : The value that will be setted in corrispective key $config_name
     * @param Boolean $can_add : Choose if add new entities
     * @return null
     */
    public function write_config($config_name, $config_value = false, $can_add = true)
    {
        //if $config_name is an array, then we wont update all values and do single save task at the end
        if (is_array($config_name))
        {
            foreach ($config_name as $key => $value)
            {
                $this->get_xml_entity($key, $can_add)->value = $value;
            }
        }
        else
        {
            $this->get_xml_entity($config_name, $can_add)->value = $config_value;
        }
        $this->save_xml();
    }
    /**
     * xml::getInstance()
     * Static function used to istance this class: implements singleton pattern to avoid multiple xml parsing.
     * 
     * @param String $sheet : the complete name of xml configuration file. 
     * @return xml object
     */
    public static function getInstance($sheet, $concurrency_safe = false)
    {
        if (!isset(self::$instance[$sheet]))
        {
            //make new istance of this class and save it to field for next usage
            $c = __class__;
            self::$instance[$sheet] = new $c($sheet, $concurrency_safe);
        }

        return self::$instance[$sheet];
    }
}

?> 