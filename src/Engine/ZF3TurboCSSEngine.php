<?php
/**
 * ZF3Turbo
 *
 * Zend Framework 3 Turbo Module
 * 
 * @link https://github.com/Mecanik/ZF3Turbo
 * @copyright Copyright (c) 2019 Norbert Boros ( a.k.a Mecanik )
 * @license https://github.com/Mecanik/ZF3Turbo/blob/master/LICENSE.md
 */

namespace Mecanik\ZF3Turbo\Engine;

class ZF3TurboCSSEngine
{
    public function __construct()
    {
    }

    public function minify_css_callback($matches)
	{
        //print_r($matches);

        $openStyleTag = "<style{$matches[1]}";
        
        $css = $matches[2];
        
		// remove any comments
        $css = self::removeComments($css);
        
		// remove any CDATA section markers (if any)
        $css = self::removeCdata($css);
        
        // remove any whitespaces and new lines
        $css = self::removeSpaces($css);   

		return "{$openStyleTag}{$css}</style>";
    }

    protected function removeComments($data)
    {
        if (false !== strpos($data, '/*')) {
            $data = str_replace("/*","_COMSTART",$data);
            $data = str_replace("*/","COMEND_",$data);
            $data = preg_replace("/_COMSTART.*?COMEND_/s","",$data);
        }

        return $data;
    }

    protected function removeCdata($data)
	{
		if (false !== strpos($data, '<![CDATA[')) {
			$data = str_replace('//<![CDATA[', '', $data);
			$data = preg_replace('~/\*\s*<!\[CDATA\[\s*\*/~', '', $data);
			$data = str_replace('<![CDATA[', '', $data);
			$data = str_replace('//]]>', '', $data);
			$data = preg_replace('~/\*\s*\]\]>\s*\*/~', '', $data);
			$data = str_replace(']]>', '', $data);
        }
        
		return $data;
    }
    
    protected function removeSpaces($data)
    {
        $data = preg_replace("/\s+/u", " ", $data);
        $data = str_replace(' ', '', $data);

        return $data;
    }
}