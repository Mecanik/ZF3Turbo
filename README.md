# ZF3Turbo - Zend Framework 3 Speed Optimiser Module
 [![Latest Stable Version](https://poser.pugx.org/mecanik/zf3turbo/v/stable)](https://packagist.org/packages/mecanik/zf3turbo)
 [![License](https://poser.pugx.org/mecanik/zf3turbo/license)](https://packagist.org/packages/mecanik/zf3turbo)
 [![Total Downloads](https://poser.pugx.org/mecanik/zf3turbo/downloads)](https://packagist.org/packages/mecanik/zf3turbo)
 
Description
------------
Module to supercharge your website performance by minifying/compressing HTML, inline content and pushing assets via HTTP/2. This module works for HTML5 type for now, do not use with XHTML.


### Current Features:
* Events registered via ListenerAggregateInterface
* Easy to switch on/off any specific feature or the whole module
* Ability to force https for all routes (via onRoute)
* Ability to minify inline javascript (removes comments, spaces)
* Ability to minify inline stylesheet (removes comments, spaces, CDATA, line breaks)
* Ability to minify HTML (removes comments, spaces, line breaks)
* Ability to push scripts via HTTP/2
* Ability to push stylesheets via HTTP/2
* Ability to push images via HTTP/2
* PHP 7 friendly (and required)
* Does not "break" your pages/content
* Superfast processing, no cpu overhead (regex patterns have been optimised to be very fast)
* No other dependencies (than Zend Framework 3)

### Planned Features:
* Caching via Redis
* Caching via Memcached
* Caching via CDN (push/pull)
* Better HTML, JS, CSS minify
* More...

Installation
------------
Installation is done via Composer:

```
composer require mecanik/zf3turbo
```

Module Configuration
----------------
Create config/autoload/zf3turbo.global.php with the content:

```php
<?php
return [
    'zf3turbo' => [
        // Completely enable/disable all functionality
        'enabled' => true,

        // Control current engine options
        'engine_options' => [

            // Force all routes to https:// and choose redirect http code
            // Recommended redirect_code is 301
            'ssl' => [
                'enabled' => false,
                'redirect_code' => 301,
            ],

            'html_minifier' => [

                // Completely enable/disable all functionality
                'enabled' => true,

                // remove HTML comments (not containing IE conditional comments)
                // Recommended: true
                'remove_comments' => true,

                // remove whitespaces and line breaks
                // Recommended: true
                'remove_whitespaces' => true,

                // remove trailing slash from void elements
                // Optional
                'remove_trailing_slashes' => true,
            ],

            // Soon...
            'html_cache' => [],

            'css_minifier' => [

                // Completely enable/disable all functionality
                'enabled' => true,

                // This will minify inline <style></style> tags, will strip comments and white spaces and new lines.
                // Warning: Might break some styles, use with caution.
                'minify_inline_styles' => true,
            ],

            'js_minifier' => [

                // Completely enable/disable all functionality
                'enabled' => false,

                // This will minify inline <script></script> tags, will strip comments and white spaces and new lines.
                // Warning: Might break some styles, use with caution.
                'minify_inline_js' => true,
            ],

            // Push assets via HTTP/2 to the browser
            'http2_push' => [

                // Completely enable/disable all functionality
                'enabled' => true,

                // Push all local CSS files via HTTP/2
                'push_css' => true,

                // Push all local JS files via HTTP/2
                'push_js' => true,

                // Push all local IMG files via HTTP/2
                'push_images' => true,
            ],
        ],
    ],
];
```

Module Usage
----------------

Load the module (in any order):

```
'Mecanik\ZF3Turbo'
```

That's all for now, enjoy!