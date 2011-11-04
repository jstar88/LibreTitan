<?php

/*
Xtreme 0.6 - Hight performance template engine
Copyright (C) 2011-2012  Covolo Nicola

*/

class Xtreme
{
    
    //----don't change these!----
    const SHOW_TAG='SHOW_TAG';
    const HIDE_TAG='HIDE_TAG';
    const DELETE_TAG='DELETE_TAG'; 
    const PHP='PHP';  
    const JSON='JSON';
    const XML='XML';
    const INI='INI';
    //---------------------------
    
    const GROUPS_CACHE_POSTFIX='_group';
    const TEMPLATE_CACHE_DIRECTORY='templates';
    const LANG_CACHE_DIRECTORY='langs';        
    const DEFAULT_TEMPLATE='tpl';
    const DEFAULT_MASTER_LEFT='{';
    const DEFAULT_MASTER_RIGHT='}';    
    const DEFAULT_LANG_EXTENSION=self::JSON;
    
    private $baseDirectory; 
    private $compileDirectory; 
    private $langDirectory;
    private $langExtension;
    private $templateExtension;
    private $templateDirectories; 
    private $readyCompiled;
    private $groups_php;
    private $groups_html;
    private $outputModifiers;
    private $cache;
    private $compileCompression;
    private $config;
    private $onInexistenceTag;
    private $country;
    private $languages;
    

    function __construct()
    {
        $this->baseDirectory = $this->appendSeparator(dirname(__FILE__)); 
        $this->compileDirectory = $this->baseDirectory;
        $this->templateDirectories = $this->baseDirectory;
        $this->langDirectory=$this->baseDirectory;
        $this->templateExtension = self::DEFAULT_TEMPLATE;
        $this->langExtension = self::DEFAULT_LANG_EXTENSION;
        $this->readyCompiled = new stdClass;
        $this->groups_php = new stdClass;
        $this->groups_html=new stdClass;
        $this->outputModifiers = array();
        $this->cache = true;
        $this->compileCompression = true;
        $this->config = array(
        'master' => array(
            'left' => self::DEFAULT_MASTER_LEFT, 'right' => self::DEFAULT_MASTER_RIGHT
            )
         );
         $this->onInexistenceTag=self::HIDE_TAG;
         $this->country='';
         $this->languages=array();
    }

    public function setBaseDirectory($new)
    {
        $this->baseDirectory = $this->appendSeparator($new);
    }
    public function setCompileDirectory($new)
    {
        $this->compileDirectory = $this->compilePath($new);
    }
    public function setLangDirectory($new)
    {
        $this->langDirectory = $this->compilePath($new);
    }
    public function setTemplateDirectories($new)
    {
        $this->templateDirectories = $this->compilePath($new);
    }
    public function setTemplateExtension($new)
    {
        $this->templateExtension = ($new{0} == '.') ? substr($new,1) : $new;
    }
    public function setLangExtension($new)
    {
        $this->langExtension = constant("self::$new");
    }
    public function setConfig($new)
    {
        $this->config = $new;
    }
    public function setOnInexistenceTagEvent($new)
    {
        $this->onInexistenceTag=constant("self::$new");     
    }
    public function useCache($status)
    {
        $this->cache = $status;
    }
    public function useCompileCompression($status)
    {
        $this->compileCompression = $status;
    }
    public function switchCountry($country,$cleanOld=false){
        $country=$this->appendSeparator($country);
        if($cleanOld)
        {
            unset($this->languages[$country]);
        }
        $this->country=$country;
        if(!isset($this->languages[$country]))
        {
            $this->languages[$country]=new stdClass();        
        }
    }
    public function assignLangFile($path,$phpVars=null){
        $langPath=$this->getLangPath($path);
        $langCompiledPath=$this->getCompiledPath($path,false);
        $lang='';
        
        if(defined("LANG_{$path}_INSIDE"))
            return;
        
        if($this->langExtension!=self::PHP && file_exists($langCompiledPath)){
            $lang=$this->open_JSON($langCompiledPath);   
        }
        elseif(file_exists($langPath)){
            $function="open_".$this->langExtension;
            $lang=$this->$function($langPath,$phpVars);
            $this->save($path,$langCompiledPath,json_encode($lang),false); 
        }
        else
           die('Lang (' . $langPath . ') not found '); 
        define("LANG_{$path}_INSIDE",true);
        $this->assign($lang);     
    }
    private function open_PHP($path,$phpVars)
    {
        $container=array();
        if($phpVars==null)
            $phpVars=array('lang');
        require($path);
        foreach($phpVars as $var)
            if(isset($$var))
               $container=array_merge_recursive($container,$$var);
        return $container;           
    }
    private function open_JSON($path)
    {
        return json_decode(file_get_contents($path),true);           
    }
    private function open_XML($path)
    {
        return simplexml_load_file($path);       
    }
    private function open_INI($path)
    {
        if(function_exists('parse_ini_string'))
            return parse_ini_string(file_get_contents($path),true);
        else
            return parse_ini_file($path,true);            
    }


    public function assignToGroup($groupId, $blockId, $templateName = '',$type='template')
    {
            
        $storeType='groups';
        if($type=='template'){
            $storeType.='_php';
         }
        else{
            $storeType.='_html';
         }
        if (!property_exists($this->$storeType, $groupId))
            $this->$storeType->{$groupId} = array();    

        if (is_array($blockId))
        {
            foreach ($blockId as $n => $v){
                $this->$storeType->{$groupId}[$n] = $v;
            }
        } elseif (is_object($blockId))
        {
            foreach (get_object_vars($blockId) as $n => $v)
                $this->$storeType->{$groupId}[$n] = $v;
        } else
        {
            $this->$storeType->{$groupId}[$blockId] = $templateName;
        } 
    }       
    
    public function assign($key, $value = '')
    {
        if (is_array($key))
        {
            foreach ($key as $n => $v)
                $this->languages[$this->country]->$n = $v;
        } elseif (is_object($key))
        {
            foreach (get_object_vars($key) as $n => $v)
                $this->languages[$this->country]->$n = $v;
        } elseif(is_array($value))
        {
            $this->languages[$this->country]->$key = (object)$value;
        }
        else
            $this->languages[$this->country]->$key = $value;    
    }
    
    public function set($key, $value = '')
    {
        $this->assign($key, $value);
    }

    public function append($key, $value = '')
    {
        if (!property_exists($this->languages[$this->country], $key))
        {
            $this->languages[$this->country]->$key = '';
        }
        $this->languages[$this->country]->$key .= $value;
    }
    public function push($key, $value = null)
    {
        if (!property_exists($this->languages[$this->country], $key))
        {
            $this->languages[$this->country]->$key = array();
        }
        $data = $this->languages[$this->country]->$key;
        $data[] = $value;
        $this->languages[$this->country]->$key = $data;
    }


    public function clear()
    {
        $this->languages[$this->country] = new stdClass;
    }

    public function clearReadyCompiled()
    {
        $this->readyCompiled = new stdClass;
    }

    public function clearGroups()
    {
        $this->groups_php = new stdClass;
        $this->groups_html= new stdClass;
    }

    public function outputGroup($groupId,$template, $reuse = false, $draw = false)
    {
        
        if (property_exists($this->groups_php, $groupId)){
          foreach ($this->groups_php->$groupId as $blockId => $templateName)
            {
                $this->assign($blockId, $this->compile($this->getTplPath($templateName)));
             }
             
        }
        if (property_exists($this->groups_html, $groupId)){
            foreach ($this->groups_html->$groupId as $blockId => $html)
            {
                $this->assign($blockId, $html);
             }
             
        }
        return $this->output($template,$reuse,$draw,true);
    }

    public function output($templates, $reuse = false, $draw = false,$forGroup=false)
    {
        if (!is_array($templates))
            $templates = explode('|', $templates);
        $out = '';
        foreach ($templates as $template)
        {
            $template=str_replace(array('/','\\'),DIRECTORY_SEPARATOR,$template);            
            if($forGroup)
                $compiledFile = $this->getCompiledPath($template.self::GROUPS_CACHE_POSTFIX);
            else                
                $compiledFile = $this->getCompiledPath($template);
            $templateFile = $this->getTplPath($template);
            
            if (isset($this->readyCompiled->$template) && $reuse)
                $out .= $this->readyCompiled->$template;
            elseif (file_exists($compiledFile) && filemtime($compiledFile) >= filemtime($templateFile) && $this->cache)
                $out .= $this->bufferedOutput($compiledFile);
            elseif (file_exists($templateFile))
            {
                $value = null;
                $this->save($template, $compiledFile, $this->compile($templateFile),true);
                $buffer = $this->bufferedOutput($compiledFile);
                $out .= $buffer;
                if ($reuse)
                    $this->readyCompiled->$template = $buffer;
            } else
                die('Template (' . $templateFile . ') not found ');
        }
        if (!$draw)
            return $out;
        echo $out;
    }

  
    private function compilePath($path)
    {
        $temp = $this->appendSeparator($path);
        return ($temp{0} != DIRECTORY_SEPARATOR) ? $this->baseDirectory . $temp : $temp;
    }
    private function appendSeparator($path)
    {
        $path=str_replace(array('/','\\'),DIRECTORY_SEPARATOR,$path);
        $path = trim($path);
        if (substr($path, -1) != DIRECTORY_SEPARATOR)
            $path .= DIRECTORY_SEPARATOR;
        return $path;
    }
    private function getTplPath($template)
    {
        return ($this->templateDirectories) . $template . '.'. ($this->templateExtension);
    }
    private function getCompiledPath($path,$isTemplate=true)
    {
        if($isTemplate)
            return ($this->compileDirectory) .(self::TEMPLATE_CACHE_DIRECTORY).DIRECTORY_SEPARATOR. $path . '.php';
        return $this->compileDirectory . self::LANG_CACHE_DIRECTORY.DIRECTORY_SEPARATOR. $this->country . $path . '.' . strtolower(self::JSON) ; 
    }
    private function getLangPath($langini)
    {
        return $this->langDirectory . $this->country . $langini . '.' . strtolower($this->langExtension);
    }

    private function bufferedOutput($compiledFile)
    {
        ob_start();
        include ($compiledFile);
        $out = ob_get_clean();
        return $out;
    }

    private function compile($string)
    {
        $lines = file($string);
        $newLines = array();
        $matches = null;
        $masterLeft=$this->config['master']['left'];
        $masterRight=$this->config['master']['right'];
        $regex="/\\{$masterLeft}([^{$masterLeft}{$masterRight}]+)\\{$masterRight}/";
        
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
                    $new = $this->transformSyntax($matches[1][$i]);
                    $line = str_replace($match, $new, $line);
                }
            }
            $newLines[] = $line;
        }
        if ($this->compileCompression)
            return $this->html_compress(implode('', $newLines));
        else
            return implode('', $newLines);
    }

    private function html_compress($html)
    {
        preg_match_all('!(<(?:code|pre).*>[^<]+</(?:code|pre)>)!', $html, $pre);
        $html = preg_replace('!<(?:code|pre).*>[^<]+</(?:code|pre)>!', '#pre#', $html); //ok
        $html = preg_replace('#<!�[^\[].+�>#', "", $html); //ok
        $html = preg_replace('/ {2,}/', ' ', $html); //ok
        $html = str_replace(array('\r', '\n', '\t'), '', $html);
        $html = preg_replace('/>[\s]+</', '><', $html); //ok
        if (!empty($pre[0]))
            foreach ($pre[0] as $tag)
                $html = preg_replace('!#pre#!', $tag, $html, 1);
        return $html;

    }


    private function save($templateName, $compiledFile, $value,$isTemplate)
    {       
       $path=substr($compiledFile,0,strrpos($compiledFile, DIRECTORY_SEPARATOR,-1));
       mkdir($path,0755,true);     
       if(file_put_contents($compiledFile,$value)===false)
            echo "failed to save $compiledFile";       
    }

    private function transformSyntax($input)
    {
        $from = array( 
         '/(^|\,|\(|\+| )([a-zA-Z_][a-zA-Z0-9_]*)($|\.|\)|\->|\+)/',
         '/\./' );
        $to = array('$1$this->get($2$3)',  '->');

        $parts = explode(':', $input);

        $string = '';
        switch ($parts[0])
        { 
            case 'if':
            case 'switch':
                $string = '<?php '.$parts[0] . '(' . preg_replace($from, $to, $parts[1]) . ') { ' . ($parts[0] ==
                    'switch' ? 'default: ?>' : ' ?>');
                break;
            case 'foreach':
                $pieces = explode(',', $parts[1]);
                $string = '<?php foreach(' . preg_replace($from, $to, $pieces[0]) . ' as ';
                $string .= preg_replace($from, $to, $pieces[1]);
                if (sizeof($pieces) == 3)

                    $string .= '=>' . preg_replace($from, $to, $pieces[2]);
                $string .= ') {  ?>';
                break;
            case 'end':
            case 'endswitch':
                $string = '<?php } ?>';
                break;
            case 'else':
                $string = '<?php } else { ?>';
                break;
            case 'case':
                $string = '<?php break; case ' . preg_replace($from, $to, $parts[1]) . ': ?>';
                break;
            case 'include':
                $string = '<?php echo $this->output("' . $parts[1] . '"); ?>';
                break;
            case 'group':
                $string =  $this->get($parts[1]);
                break;
            default:
                $string = '<?php echo ' . preg_replace($from, $to, $parts[0]) . '; ?>';
                break;
        }
        return $string;
    }
    public function get($key,$index=false){
        if (!property_exists($this->languages[$this->country], $key)){
            $return='';
            switch($this->onInexistenceTag){
                case self::HIDE_TAG:
                    $return= "<!--$key[$index]-->";    
                    break;
                case self::DELETE_TAG:
                    $return= "";
                    break;
                case self::SHOW_TAG:
                    if($index=== false)
                        $return=$key;
                    else
                        $return='{'.$key.'['.$index.']}';
                    break;
                default:
                    break;              
            }
            return $return;               
        } 
        if(empty($index))   
            return $this->languages[$this->country]->$key;
        else    
            return $this->languages[$this->country]->{$key}[$index];
    }
}

?>
