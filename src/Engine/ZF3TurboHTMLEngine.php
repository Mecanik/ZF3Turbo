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

class ZF3TurboHTMLEngine
{
    public function __construct()
    {
    }

    public function remove_comments_callback($matches)
	{
		return (0 === strpos($matches[1], '[') || false !== strpos($matches[1], '<![')) ? $matches[0] : '';
    }
}