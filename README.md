glesys
======  
#### Description 
A small library to interact with the [GleSYS api](https://github.com/GleSYS/API).

#### Installation with composer
It's not available on packagist so:
```json
{
	"repositories": [
		{
			"type": "vcs",
			"url": "https://github.com/osadi/glesys"
		}
	],
	"require": {
		"glesys/api": "dev-master",
	}
}
```

#### Usage
use `post`or `get` as the prefix depending on your preferred method, and then call the module and function you want to access.  

eg:
 * postDomainListrecords
 * getServerList

And pass any parameters as an array.


See https://github.com/GleSYS/API/wiki/Full-API-Documentation for a complete list of available modules and functions.


#### Example


```php
<?php
use Glesys\GlesysApi;

$glesys = new GlesysApi('CL12345', '1234_your_api_key_5678');

$domainList = $glesys->postDomainListrecords(
    array(
        'domainname' => 'domain.tld',
    )
);

```
