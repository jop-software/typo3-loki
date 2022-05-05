<div align="center">
    <h1>TYPO3 Loki Client</h1>
    <p>Monitor logging output of your TYPO3 installation</p>
</div>

## State of the Project
This is currently experimental and probably not in a usable state for you.

Currently ony a JSON Log Writer in implemented, so an external Loki client like promtail can better understand the logs.

There will be a direct loki client implemented in the future.

## Professional Support
Professional support is available, please contact [info@jop-software.de](mailto:info@jop-software.de) for more information.

## Configuration
```php
$GLOBALS['TYPO3_CONF_VARS']['LOG']['writerConfiguration'] = [
    // configuration for ERROR level log entries
    \TYPO3\CMS\Core\Log\LogLevel::ERROR => [
        // add a FileWriter
        \Jops\TYPO3\Loki\Log\Writers\JsonWriter::class => []
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
