# API Utility

[![Latest Version on Packagist](https://img.shields.io/packagist/v/decodeblock/api-utility.svg?style=flat-square)](https://packagist.org/packages/decodeblock/api-utility)
[![Tests Status](https://img.shields.io/github/actions/workflow/status/decodeblock/api-utility/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/decodeblock/api-utility/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Code Style Status](https://img.shields.io/github/actions/workflow/status/decodeblock/api-utility/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/decodeblock/api-utility/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/decodeblock/api-utility.svg?style=flat-square)](https://packagist.org/packages/decodeblock/api-utility)

A Laravel package providing helper functions for streamlined API development. Simplify response handling and enhance the structure of your APIs.

---

## Installation

Install the package via Composer:

```bash
composer require decodeblock/api-utility
```

---

## Usage

### Example
Using the `ApiResponder` trait for handling JSON responses:

```php
use Decodeblock\ApiUtility\Traits\ApiResponder;

class YourController extends Controller
{
    use ApiResponder;

    public function index()
    {
        return $this->success(['message' => 'Hello, API Utility!']);
    }
}
```

The `ApiResponder` trait provides methods like:
- `success($data, $message = '', $status = 200)`: For successful responses.
- `error($message, $status = 400)`: For error responses.

---

## Testing

Run tests using:

```bash
composer test
```

---

## Changelog

Detailed changes for each release are documented in the [CHANGELOG](CHANGELOG.md).

---

## Contributing

We welcome contributions! Please see the [CONTRIBUTING](CONTRIBUTING.md) guide for details.

---

## Credits

- **[Gabriel Ibenye](https://github.com/gabbyti)**

---

## License

This package is licensed under the [MIT License](LICENSE.md).
