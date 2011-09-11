<?php

/*
Xtreme 0.2 - Hight performance template engine
Copyright (C) 20011-2012  Covolo Nicola

*/

class Xtreme
{
    private $baseDirectory; 
    private $compileDirectory; 
    private $templateExtension;
    private $templateDirectories; 
    private $data; 
    private $readyCompiled;
    private $groups;
    private $groups_html;
    private $outputModifiers;
    private $cache;
    private $compileCompression;
    const HTMLCODE='<!--|html|-->';

    function __construct()
    {
        $this->baseDirectory = $this->appendSeparator($_SERVER['DOCUMENT_ROOT']); 
        $this->compileDirectory = $this->baseDirectory;
        $this->templateDirectories = $this->baseDirectory;
        $this->templateExtension = '.tpl';
        $this->data = new stdClass;
        $this->readyCompiled = new stdClass;
        $this->groups = new stdClass;
        $this->groups_html=new stdClass;
        $this->outputModifiers = array();
        $this->cache = true;
        $this->compileCompression = true;
    }

    public function setBaseDirectory($new)
    {
        $this->baseDirectory = $this->compilePath($new);
    }
    public function setCompileDirectory($new)
    {
        $this->compileDirectory = $this->compilePath($new);
    }
    public function setTemplateDirectories($new)
    {
        $this->templateDirectories = $this->compilePath($new);
    }
    public function setTemplateExtension($new)
    {
        $this->templateExtension = ($new{0} == '.') ? $new : '.' . $new;
    }
    public function useCache($status)
    {
        $this->cache = $status;
    }
    public function useCompileCompression($status)
    {
        $this->compileCompression = $status;
    }


    public function assignToGroup($groupId, $blockId, $templateName = '',$type='template')
    {
            
        $storeType='groups';
        if($type=='template'){
            $x=0;
         }
        else{
            $storeType.='_html';
            $x=Xtreme::HTMLCODE; 
         }
        if (!property_exists($this->$storeType, $groupId))
            $this->$storeType->$groupId = array();    

        if (is_array($blockId))
        {
            foreach ($blockId as $n => $v){
                $v=$x.$v;
                $this->$storeType->$groupId[$n] = $v;
            }
        } elseif (is_object($blockId))
        {
            foreach (get_object_vars($blockId) as $n => $v)
                $v=$x.$v;
                $this->$storeType->$groupId[$n] = $v;
        } else
        {
            $templateName=$x.$templateName;
            $this->$storeType->$groupId[$blockId] = $templateName;
        } 
    }       
    
    public function assign($key, $value = '')
    {
        if (is_array($key))
        {
            foreach ($key as $n => $v)
                $this->data->$n = $v;
        } elseif (is_object($key))
        {
            foreach (get_object_vars($key) as $n => $v)
                $this->data->$n = $v;
        } else
        {
            $this->data->$key = $value;
        }
    }
    
    public function set($key, $value = '')
    {
        $this->assign($key, $value);
    }

    public function append($key, $value = '')
    {
        if (!property_exists($this->data, $key))
        {
            $this->data->$key = '';
        }
        $this->data->$key .= $value;
    }
    public function push($key, $value = null)
    {
        if (!property_exists($this->data, $key))
        {
            $this->data->$key = array();
        }
        $data = $this->data->$key;
        $data[] = $value;
        $this->data->$key = $data;
    }


    public function clear()
    {
        $this->data = new stdClass;
    }

    public function clearReadyCompiled()
    {
        $this->readyCompiled = new stdClass;
    }

    public function clearGroups()
    {
        $this->groups = new stdClass;
        $this->groups_html= new stdClass;
    }

    public function outputGroup($templates, $groupId, $reuse = false, $draw = false)
    {
        if (property_exists($this->groups, $groupId)){
          foreach ($this->groups->$groupId as $blockId => $templateName)
            {
                $this->assign($blockId, file_get_contents($this->getTplPath($templateName)));
             }
             unset($this->groups->$groupId);
        }
        if (property_exists($this->groups_html, $groupId)){
            foreach ($this->groups_html->$groupId as $blockId => $html)
            {
                $this->assign($blockId, $html);
             }
             unset($this->groups_html->$groupId);
        }
        return $this->output($templates);
    }

    public function output($templates, $reuse = false, $draw = false)
    {
        if (!is_array($templates))
            $templates = explode('|', $templates);
        $out = '';
        foreach ($templates as $template)
        {
            $compiledFile = $this->getCompiledPath($template);
            $templateFile = $this->getTplPath($template);
            if (isset($this->readyCompiled->$template) && $reuse)
                $out .= $this->readyCompiled->$template;
            elseif (file_exists($compiledFile) && filemtime($compiledFile) >= filemtime($templateFile) && $this->cache)
                $out .= $this->bufferedOutput($compiledFile);
            elseif (file_exists($templateFile))
            {
                $value = null;
                $this->save($template, $compiledFile, $this->compile($templateFile));
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
        $path = trim($path);
        if (substr($path, -1) != DIRECTORY_SEPARATOR)
            $path .= DIRECTORY_SEPARATOR;
        return $path;
    }
    private function getTplPath($template)
    {
        return $this->templateDirectories . $template . $this->templateExtension;
    }
    private function getCompiledPath($template)
    {
        return $this->compileDirectory . $template . '.php';
    }

    private function bufferedOutput($compiledFile)
    {
        ob_start();
        include ($compiledFile);
        $out = ob_get_clean();
        return $out;
    }

    private function compile($templateFile)
    {
        $lines = file($templateFile);
        $newLines = array();
        $matches = null;
        foreach ($lines as $line)
        {
            $num = preg_match_all('/\{([^{}]+)\}/', $line, &$matches);
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
        $html = preg_replace('#<!–[^\[].+–>#', "", $html); //ok
        $html = preg_replace('/ {2,}/', ' ', $html); //ok
        $html = str_replace(array('\r', '\n', '\t'), '', $html);
        $html = preg_replace('/>[\s]+</', '><', $html); //ok
        if (!empty($pre[0]))
            foreach ($pre[0] as $tag)
                $html = preg_replace('!#pre#!', $tag, $html, 1);
        return $html;

    }


    private function save($template, $compiledFile, $value)
    {
        $folders = explode(DIRECTORY_SEPARATOR, $template);
        $temp = $this->compileDirectory;
        $i = -1;
        $count = count($folders);
        while ($i < $count - 1)
        {
            if (!file_exists($temp))
                mkdir($temp);
            $temp .= $folders[++$i] . DIRECTORY_SEPARATOR;
        }
        $f = fopen($compiledFile, 'w');
        fwrite($f, $value);
        fclose($f);
    }

    private function transformSyntax($input)
    {
        $from = array( 
        '/(^|\[|,|\(|\+| )([a-zA-Z_][a-zA-Z0-9_]*)($|\.|\)|\[|\]|\+)/', '/(^|\[|,|\(|\+| )([a-zA-Z_][a-zA-Z0-9_]*)($|\.|\)|\[|\]|\+)/',
            '/\./', );
        $to = array('$1$this->data->$2$3', '$1$this->data->$2$3', '->');

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
                $string .= ') { ' ?>';
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
            default:
                if(substr ($parts[0],0,strlen(Xtreme::HTMLCODE))==Xtreme::HTMLCODE)
                  $string = $parts[0];
                else
                  $string = '<?php echo ' . preg_replace($from, $to, $parts[0]) . '; ?>';
                break;
        }
        return $string;
    }
}

?>
