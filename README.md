# Matomo OpenTelemetry Plugin
This needs PHP 8.0.x or above.

## Install needed packages

```bash
composer require --ignore-platform-reqs --dev php-http/guzzle7-adapter
composer require --ignore-platform-reqs --dev open-telemetry/opentelemetry
```

or

add to composer.json:

```bash
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:open-telemetry/opentelemetry-php.git"
        }
    ],
```

and run:

```bash
composer require --dev  --ignore-platform-reqs open-telemetry/opentelemetry:dev-main#116e46d964e647a287096b8226ab415558f9d68e

```

## Activate plugin
```


## Description

Add your plugin description here.

