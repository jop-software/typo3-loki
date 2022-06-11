<?php

namespace Jops\TYPO3\Loki\Log\Writers;

use Throwable;
use RuntimeException;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Log\Writer\FileWriter;

class JsonWriter extends FileWriter
{
    public function writeLog(LogRecord $record): self
    {
        $message = json_encode($record->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (false === fwrite(self::$logFileHandles[$this->logFile], $message . LF)) {
            throw new RuntimeException('Could not write log record to log file', 1345036335);
        }

        return $this;
    }
}
