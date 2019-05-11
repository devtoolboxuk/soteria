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

## Usage

Usage of the hashing service

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
 ```php
$xss->clean($string); //Outputs data that has had XSS data removed.
```

#### XSS Detected
```php
$xss->clean($string);
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