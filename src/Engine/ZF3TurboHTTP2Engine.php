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

class ZF3TurboHTTP2Engine
{
	/**
	 * \Zend\Http\PhpEnvironment\Response
	 */
	protected $response;

	/**
	 * HTML content
	 */
	protected $content;

	/**
	 * \Zend\Http\Request
	 */
	protected $request;

	/**
	 * @TODO
	 */
	protected $hostname;

	/**
	 * List of items to push
	 */
	protected $pushable_items = [];

    public function __construct($response, $content, $request)
    {
		$this->response = $response;
		$this->content = $content;
		$this->request = $request;

		//@TODO - Check hostname
        $this->hostname = parse_url($request->getUriString(), PHP_URL_HOST);
    }

	public function build_css()
	{
		preg_match_all('/<link rel="stylesheet".*?href=["\']+(.*?)["\']+/im', $this->content, $matches, PREG_SET_ORDER, 0);
		
		foreach($matches as $mm)
		{
			$no_hostame_url = parse_url($mm[1], PHP_URL_PATH);

			if (is_readable('./public'.$no_hostame_url))  {
				$this->pushable_items[] = "<$no_hostame_url>; rel=preload; as=style";		 
			} 
			else
				continue;
		}
	}

	public function build_js()
	{
		preg_match_all('/src=\"([a-zA-Z\d\/\\\\\.\-_?\=]*\.js)\"/im', $this->content, $matches, PREG_SET_ORDER, 0);
		
		foreach($matches as $mm)
		{
			$no_hostame_url = parse_url($mm[1], PHP_URL_PATH);

			if (is_readable('./public'.$no_hostame_url))  {
				$this->pushable_items[] = "<$no_hostame_url>; rel=preload; as=script";		 
			} 
			else
				continue;
		}
	}

	public function build_images()
	{
		preg_match_all('/<img.*?src=["\']+(.*?)["\']+/im', $this->content, $matches, PREG_SET_ORDER, 0);
		
		foreach($matches as $mm)
		{
			$no_hostame_url = parse_url($mm[1], PHP_URL_PATH);

			if (is_readable('./public'.$no_hostame_url))  {
				$this->pushable_items[] = "<$no_hostame_url>; rel=preload; as=image";		 
			} 
			else
				continue;
		}
	}

	public function push_http2()
	{
		$items = implode(',', $this->pushable_items);
		
		$headers = $this->response->getHeaders();
		
		$headers->addHeaderLine("Link: ".$items);
	}
}