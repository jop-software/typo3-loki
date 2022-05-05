<?php

namespace Jops\TYPO3\Loki\Log\Writers;

use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Log\Writer\FileWriter;

class JsonWriter extends FileWriter
{
    public function writeLog(LogRecord $record)
    {
        $data = '';
        $context = $record->getData();
        $message = $record->getMessage();
        if (!empty($context)) {
            // Fold an exception into the message, and string-ify it into context so it can be jsonified.
            if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
                $message .= $this->formatException($context['exception']);
                $context['exception'] = (string)$context['exception'];
            }
            $data = $context;
        }

        $message = json_encode([
            "date" => date('r', (int)$record->getCreated()),
            "severity" => strtoupper($record->getLevel()),
            "requestId" => $record->getRequestId(),
            "component" => $record->getComponent(),
            "message" => $this->interpolate($message, $context),
            "data" => $data,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


        if (false === fwrite(self::$logFileHandles[$this->logFile], $message . LF)) {
            throw new \RuntimeException('Could not write log record to log file', 1345036335);
        }

        return $this;
    }
}
