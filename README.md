## Servebolt Frontend Cache for Magento

This plugin enables Magento to make correct caching headers that mark 
Magento responses to be cacheable or not by external web cache, e.g. Ngnix,
Tengine or Varnish.  

This module should be installed by professionals that know what they are doing. 
Knowledge of Magento and Magento's caching system is required and crucial to 
ensure proper functioning.

There are many risks with frontend caching of php/html responses, and the site
should be tested thoroughly after installation.


### Requirements

- PHP >= 7.2
- Mage_Core

### Recommended

- PHP >= 7.1

###  Compatibility
 
- Magento CE >= 1.9
- Magento EE >= 1.14


###  Installation
 
1. initialize composer project with Magento
1. add repository to your root _composer.json_
`"repositories": [{"type": "git", "url": "https://github.com/Servebolt/magento-frontendcache.git"}]`
1. add module requirement  
`$ composer require servebolt/frontend-cache`
1. The module can be activated by using the database method
1. Flush Magento cache


###  Description

This Magento module analyse request and response in terms of cacheability by 
external systems caching full response. 

There are 3 scenarios:

1. response is cacheable  
Extension removes _no_cache_ cookie, "Cache-Control" and "Pragma" headers 
(correct headers should be set by external cache system)
2. response is not cacheable  
Extension do not modify headers nor cookies set by Magento
3. should bypass external cache
Extension sets _no_cache_ cookie (by default cookie lifetime is equal to lifetime of _frontend_ cookie)


###  Usage

Extension is configurable through Magento's _config paths_ in database or in 
_config.xml_.  

**Warning!** Do not modify the original _Servebolt/CacheCookies/etc/config.xml_ 
to avoid data loss during upgrade. Create your own module, configure it using
the database approach or set your configuration using 
[n98-magerun](https://github.com/netz98/n98-magerun). 

#### Database method
```
#!sql

INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) 
VALUES 'default', 0, 'servebolt_frontendcache/enabled', 1;
```

#### n98-magerun method

To use this method please follow installation and usage instructions 
provided by n98-magerun. 
```
#!sh

$ n98-magerun.phar config:set servebolt_frontendcache/enabled 1

```

####  Configuration

The following paths are configurable:

##### Enabling module
_Config_: `servebolt_frontendcache/enabled`  
Set to "1" to enable module 

##### _no_cache_ cookie
_no_cache_ cookie serves as an instruction for external caching system to not 
cache current response and bypass all further requests within lifetime of cookie.

_Config_: `servebolt_frontendcache/cookies/no-cache/lifetime`  
Set to desired _no_cache_ cookie lifetime in seconds (if not set equal to 
lifetime of _frontend_ cookie)  

_Config_: `servebolt_frontendcache/disallowed_handles`  
A list of layout update handles which should bypass cache by setting _no_cache_
cookie.  
E.g. to disable caching when user is logged in set the following path: 
`servebolt_frontendcache/disallowed_handles/customer_logged_in` with value `1`. 

##### Cacheable requests
_Config_: `servebolt_frontendcache/allowed_requests`  
A list of requests which should be cacheable. It has a form of configuration 
sub-paths, e.g.:  
1. If you want to cache specific controller action set config `servebolt_frontendcache/allowed_requests/catalog/category/view`.    
2. If you want to cache all controller actions set config `servebolt_frontendcache/allowed_requests/catalog/category`.  
3. If you want to cache all controllers within given frontname set config `servebolt_frontendcache/allowed_requests/catalog`.     
 
By default the following requests are cacheable:
1. cms/page/*
2. catalog/category/view
3. catalog/product/view

For request to be cacheable as URI has to be allowed as well. 
See: section _Non-cacheable URIs_ 

##### Non-cacheable URIs
_Config_: `servebolt_frontendcache/non_cacheable_uris`
A list of URIs which should NOT be cacheable. 
E.g. to make specific page with URL `{BASE_URL}/some-specific-page` 
non-cacheable set the following config value:
`servebolt_frontendcache/non_cacheable_uris/some-specific-page` with value `1`. 

**Special case:** slash URI ("/")
To make a main page with URL `{BASE_URL}` non-cacheable set the following config
value using special configuration node name `servebolt_frontendcache_hompage_uri`:
`servebolt_frontendcache/non_cacheable_uris/servebolt_frontendcache_hompage_uri` 
with value `1`. 


##### Expires headers
_Config_: `servebolt_frontendcache/headers/expires/lifetime`  
Set to desired _Expires_ header lifetime in seconds (if not set equal to 
lifetime of _frontend_ cookie)  

##### Formkey bypassing
Formkey validation is a security mechanism preventing cross-site scripting and 
request forgery attacks. Formkey should be unique for each site visitor. This 
mechanism makes web pages not cacheable. 

Some actions which are protected by this mechanism by default in Magento should
not be considered as a big risk, therefore formkey validation can be bypassed. 

_Config_: `servebolt_frontendcache/formkey_bypass/enabled`  
Set to "1" to enable formkey bypassing for actions with low security risk.  
 
_Config_: `servebolt_frontendcache/formkey_bypass/bypass_requests`  
A list of requests which formkey bypassing should be applied. It has a form of 
configuration sub-paths, e.g.:  
1. If you want to bypass formkey in specific controller action set config `servebolt_frontendcache/formkey_bypass/bypass_requests/catalog/category/view`.    
2. If you want to cache all controller actions set config `servebolt_frontendcache/formkey_bypass/bypass_requests/catalog/category`.  
3. If you want to cache all controllers within given frontname set config `servebolt_frontendcache/formkey_bypass/bypass_requests/catalog`.     

By default the following requests are bypassed:
1. checkout/cart/add
2. wishlist/index/add
3. catalog/product_compare/add
4. review/product_product/post
5. review/product_product/post

### Development

Miply http://www.miply.no

### Licence (c) 2016 - [Servebolt AS](https://servebolt.com) (formerly named Raske Sider AS)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.