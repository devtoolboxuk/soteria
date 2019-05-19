# soteria

[![Build Status](https://api.travis-ci.org/devtoolboxuk/soteria.svg?branch=master)](https://travis-ci.org/devtoolboxuk/soteria)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/devtoolboxuk/soteria/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/devtoolboxuk/soteria/?branch=master)
[![Coveralls](https://coveralls.io/repos/github/devtoolboxuk/soteria/badge.svg?branch=master)](https://coveralls.io/github/devtoolboxuk/soteria?branch=master)
[![CodeCov](https://codecov.io/gh/devtoolboxuk/soteria/branch/master/graph/badge.svg)](https://codecov.io/gh/devtoolboxuk/soteria)

[![Latest Stable Version](https://img.shields.io/packagist/v/devtoolboxuk/soteria.svg?style=plastic)](https://packagist.org/packages/devtoolboxuk/soteria)
[![Total Downloads](https://img.shields.io/packagist/dt/devtoolboxuk/soteria.svg?style=flat-square)](https://packagist.org/packages/devtoolboxuk/soteria)
[![License](https://img.shields.io/packagist/l/devtoolboxuk/soteria.svg?style=flat-square)](https://packagist.org/packages/devtoolboxuk/soteria)


## Table of Contents

- [Background](#Background)
- [Usage](#Usage)
- [Maintainers](#Maintainers)
- [License](#License)

## Background

Various security libraries rolled into one place.

The XSS cleaner is a port from  https://github.com/voku/anti-xss with the ability for it to work on some older systems.

When I get around to upgrading my legacy systems, the XSS cleaner will be updated to use voku/anti-xss directly (because it's awesome)

I've also added a URL decoder, as I found some items causing a few issues with invisible characters such as \r\n (in a URL, you probably wouldn't want this)

## Usage

```sh
$ composer require devtoolboxuk/soteria
```

Then include Composer's generated vendor/autoload.php to enable autoloading:

```php
require 'vendor/autoload.php';
```

```php
use devtoolboxuk\soteria;

$this->security = new SoteriaService();
```


## XSS
```php
$xss = $this->security->xss();
```

#### XSS Clean

Great for clearing out data in posted data

 ```php
#$data can be either a string or an array
$xss->clean($data); //Outputs data that has had XSS data removed.
```

#### XSS Detected
```php
$xss->clean($data);
$xss->isXssFound(); //Returns true / false
```

#### XSS Clean a URL

Great for clearing out crappy URLs (does the same as clean, but also removes invisible characters like \r \n)

```php
#$data can be either a string or an array
$xss->cleanUrl($data); //Outputs data that has had XSS data removed.
```

#### XSS Detected
```php
$xss->cleanUrl($data);
$xss->isXssFound(); //Returns true / false
```

## Filter
```php
$filter = $this->security->filter();
```

#### Filter Email
 ```php
$filter->email('test@local.com');
```

#### Filter Was an invalid email address used
```php
$filter->email('test@local.com');
$filter->isValid(); //Returns true / false
```


## Maintainers

[@DevToolboxUk](https://github.com/DevToolBoxUk).


## License

[MIT](LICENSE) © DevToolboxUK