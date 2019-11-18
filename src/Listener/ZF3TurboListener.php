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

namespace Mecanik\ZF3Turbo\Listener;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

class ZF3TurboListener implements ListenerAggregateInterface
{
    // Zend\EventManager\ListenerAggregateTrait define the property ($listeners)
    use ListenerAggregateTrait;

    /**
     * ZF3Turbo configuration!
     */
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        if(!$this->config['enabled'])
        {
            return;
        }

        $this->listeners[] = $events->attach('route', [$this, 'onRoute'], $priority);
        $this->listeners[] = $events->attach('finish', [$this, 'onFinish'], $priority);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            $events->detach($listener);
            unset($this->listeners[$index]);
        }
    }

    public function onRoute(EventInterface $event)
    {
        if($this->config['engine_options']['ssl']['enabled'])
        {
            if (php_sapi_name() == "cli") {
                // Do not execute HTTPS redirect in console mode.
                return;
            }
            
            // Get request URI
            $uri = $event->getRequest()->getUri();
            $scheme = $uri->getScheme();
            
            // If scheme is not HTTPS, redirect to the same URI, but with
            // HTTPS scheme.
            if ($scheme != 'https'){
                $uri->setScheme('https');
                $response=$event->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $uri);
                $response->setStatusCode($this->config['engine_options']['ssl']['redirect_code']);
                $response->sendHeaders();
                return $response;
            }
        }
    }

    public function onFinish(EventInterface $event)
    {
        $response = $event->getResponse();
        $content = $response->getBody();

        if($this->config['engine_options']['js_minifier']['enabled'] === true) {
            if($this->config['engine_options']['js_minifier']['minify_inline_js']) {
                $content = preg_replace_callback('/(\\s*)<script(\\b[^>]*?>)([\\s\\S]*?)<\\/script>(\\s*)/i', [\Mecanik\ZF3Turbo\Engine\ZF3TurboJSEngine::class, 'minify_js_callback'], $content);
            }
        }

        if($this->config['engine_options']['css_minifier']['enabled'] === true) {
            if($this->config['engine_options']['css_minifier']['minify_inline_styles']) {
                $content = preg_replace_callback('/\\s*<style(\\b[^>]*>)([\\s\\S]*?)<\\/style>\\s*/i', [\Mecanik\ZF3Turbo\Engine\ZF3TurboCSSEngine::class, 'minify_css_callback'], $content);
            }    
        }

        if($this->config['engine_options']['html_minifier']['enabled'] === true) {
            if($this->config['engine_options']['html_minifier']['remove_comments']) {
                $content = preg_replace_callback('/<!--([\\s\\S]*?)-->/', [\Mecanik\ZF3Turbo\Engine\ZF3TurboHTMLEngine::class, 'remove_comments_callback'], $content);
            }
    
            if($this->config['engine_options']['html_minifier']['remove_whitespaces']) {
                // trim each line.
                //@TODO take into account attribute values that span multiple lines.
                $content = preg_replace('/^\\s+|\\s+$/m', '', $content);
                        
                // remove ws around block/undisplayed elements
                $content = preg_replace('/\\s+(<\\/?(?:area|article|aside|base(?:font)?|blockquote|body'
                    .'|canvas|caption|center|col(?:group)?|dd|dir|div|dl|dt|fieldset|figcaption|figure|footer|form'
                    .'|frame(?:set)?|h[1-6]|head|header|hgroup|hr|html|legend|li|link|main|map|menu|meta|nav'
                    .'|ol|opt(?:group|ion)|output|p|param|section|t(?:able|body|head|d|h||r|foot|itle)'
                    .'|ul|video)\\b[^>]*>)/iu', '$1', $content);
                    
                // remove whitespaces outside of all elements
                $content = preg_replace('/>((\\s)(?:\\s*))?([^<]+?)((\\s)(?:\\s*))?</u', '>$2$3$5<', $content);
    
                // remove whitespaces before end of all empty elements
                $content = preg_replace('/\\s*\\/>/', '/>', $content);
            }
    
            if($this->config['engine_options']['html_minifier']['remove_trailing_slashes']) {
                $content = preg_replace(
                    '~<(area|base|br|col|command|embed|hr|img|input|keygen|link|meta|param|source|track|wbr)([^>]*?)\\s*[/]?>~i'
                    ,'<$1$2>'
                    ,$content);
            }
        }

        if($this->config['engine_options']['http2_push']['enabled'] === true) {

            $http_pusher = new \Mecanik\ZF3Turbo\Engine\ZF3TurboHTTP2Engine($event->getApplication()->getResponse(), $content, $event->getApplication()->getRequest());

            if($this->config['engine_options']['http2_push']['push_css'] === true) {
                $http_pusher->build_css();
            }

            if($this->config['engine_options']['http2_push']['push_js'] === true) {
                $http_pusher->build_js();
            }

            if($this->config['engine_options']['http2_push']['push_images'] === true) {
                $http_pusher->build_images();
            }

            $http_pusher->push_http2();
        }

        $response->setContent($content);
    }
}