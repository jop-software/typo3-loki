<div align="center">
    <h1>TYPO3 Loki Client</h1>
    <p>Monitor logging output of your TYPO3 installation</p>
</div>

## Professional Support
Professional support is available, please contact [info@jop-software.de](mailto:info@jop-software.de) for more information.

## Configuration

There are two possible ways to use this extension.

1. Use the JsonWriter to convert all logs to JSON before writing them to the log file.  
This way, you can process the logs with a different loki client like [Promtail](https://grafana.com/docs/loki/latest/clients/promtail/)

```php
$GLOBALS['TYPO3_CONF_VARS']['LOG']['writerConfiguration'] = [
    // Log handling configuration for ERROR logs
    // Set this to DEBUG to process all logs.
    \TYPO3\CMS\Core\Log\LogLevel::ERROR => [
        // Convert all logs to JSON to scrape them with loki clients like Promtail
        \Jops\TYPO3\Loki\Log\Writers\JsonWriter::class => [],
    ]
];
```

2. Use the LokiWriter to send logs directly to a configured loki instance.  
You can define labels here, that will be attached to the log line sent to loki.  
See: [Configure a Loki instance](#configure-a-loki-instance)

```php
$GLOBALS['TYPO3_CONF_VARS']['LOG']['writerConfiguration'] = [
    // Log handling configuration for ERROR logs
    // Set this to DEBUG to process all logs.
    \TYPO3\CMS\Core\Log\LogLevel::ERROR => [
        // Configure LokiWrite to send logs to Loki
        \Jops\TYPO3\Loki\Log\Writers\LokiWriter::class => [
            "labels" => [
                "key" => "value",
            ],
        ],
    ]
];
```

### Configure a Loki instance.

If you want to use the LokiWriter, you need to have a loki instance configured. This can be done with the Extension 
Configuration in the backend or in the `AdditionalConfiguration.php`.  
If your Loki installation is secured with http basic auth, you can provide those credentials here as well. Keep in mind
that the credentials **are store as plain text** in the configuration file.

This could be an example configuration:

```php
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['loki'] = [
    "base-url" => "https://loki.example.com"
    // Optional: Add basic-auth credentials if needed-
    "basic-auth" => [
        "username" => "username",
        "password" => "secure-password"
    ]
];
```

## Local Development
We use [DDEV](https://ddev.com) for local development.

You get a complete ddev setup in this repository, just run `ddev start`.

## License
This project is licensed under [GPL-2.0-or-later](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html), see the [LICENSE](./LICENSE) file for more information.

<div align="center">
    <p>&copy; 2022, <a href="mailto:info@jop-software.de">jop-software Inh. Johannes Przymusinski</a></p>
</div>
