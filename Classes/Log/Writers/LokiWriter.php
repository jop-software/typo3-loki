<?php

namespace Jops\TYPO3\Loki\Log\Writers;

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
    protected array $streams;

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
        /** @var string $baseUrl */
        $baseUrl = $this->extensionConfiguration->get("loki", "base-url");

        if (!$baseUrl) {
            throw new \RuntimeException("No base url for loki found.");
        }

        $body = json_encode([
            "streams" => [
                [
                    // Those are the labels we can query after
                    "stream" => $this->streams,
                    // This is "unix epoch in nano seconds" and the log line string
                    "values" => [
                        [ time() * 1000000000, json_encode($record->toArray())],
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

        $this->requestFactory->request("{$baseUrl}/loki/api/v1/push", "POST", $options);

        return $this;
    }

    /**
     * @param array<string, string> $streams
     */
    public function setStreams(array $streams): void
    {
        $this->streams = $streams;
    }
}
