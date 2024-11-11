<?php

namespace Jops\TYPO3\Loki\Log\Writers;

use Jops\TYPO3\Loki\Log\LogRecordFormatter;
use RuntimeException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Log\Exception\InvalidLogWriterConfigurationException;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Log\Writer\AbstractWriter;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LokiWriter extends AbstractWriter
{
    protected ExtensionConfiguration $extensionConfiguration;
    protected RequestFactory $requestFactory;

    /**
     * LogQL streams we attach to the log sent to loki.
     *
     * @var array<string, string>
     */
    protected array $labels;

    /**
     * @param array<string, string> $options
     * @throws InvalidLogWriterConfigurationException
     */
    public function __construct(array $options = [])
    {
        $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $this->requestFactory = GeneralUtility::makeInstance(RequestFactory::class);

        parent::__construct($options);
    }

    public function writeLog(LogRecord $record): self
    {
        /** @var ?string $baseUrl */
        $baseUrl = $this->extensionConfiguration->get("loki", "base-url");

        if (!$baseUrl) {
            throw new RuntimeException("No base url for loki found.");
        }

        $body = json_encode([
            "streams" => [
                [
                    // Those are the labels we can query after
                    "stream" => $this->labels,
                    // This is "unix epoch in nanoseconds" and the log line string
                    "values" => [
                        [ $this->unixEpochNanoSeconds(), json_encode(LogRecordFormatter::toArray($record))],
                    ],
                ],
            ],
        ]);

        $options = [
            "headers" => [
                "Content-Type" => "application/json",
            ],
            "http_errors" => false,
            "body" => $body,
            "timeout" => 1,
        ];

        /** @var string $username */
        $username = $this->extensionConfiguration->get("loki", "basic-auth/username");
        /** @var string $password */
        $password = $this->extensionConfiguration->get("loki", "basic-auth/password");

        if ($username !== "" && $password !== "") {
            $options["auth"] = [
                $username,
                $password,
            ];
        }

        try {
            $this->requestFactory->request("{$baseUrl}/loki/api/v1/push", "POST", $options);
        } catch (\Throwable $t) {
            // NOOP. Don't throw an exception when loki could not be reached.
        }
        

        return $this;
    }

    /**
     * @param array<string, string> $labels
     */
    public function setLabels(array $labels): void
    {
        $this->labels = $labels;
    }

    /**
     * Return the current unix epoch in nanoseconds.
     * This makes use of the <code>time()</code> so, even tho the result is in "nanoseconds", the precision is only
     * one second.
     */
    protected function unixEpochNanoSeconds(): string
    {
        $epoch = time() * 1000000000;
        return "{$epoch}";
    }
}
