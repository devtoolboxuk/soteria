# soteria

## Table of Contents

- [Background](#Background)
- [Usage](#Usage)
- [Maintainers](#Maintainers)
- [License](#License)

## Background

Various security libraries rolled into one place.

The XSS cleaner is a port from  https://github.com/voku/anti-xss with the ability for it to work on some older systems.

When I get around to upgrading my legacy systems, the XSS cleaner will be updated to use voku/anti-xss directly (because it's awesome)

I've also added a URL decoder, as I found some items causing a few issues

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