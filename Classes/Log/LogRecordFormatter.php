<?php

namespace Jops\TYPO3\Loki\Log;

use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogRecord;

class LogRecordFormatter
{
    /**
     * Format the given LogRecord as an array.
     *
     * This uses LogRecord::toArray internally but adds some additional fields.
     */
    public static function toArray(LogRecord $logRecord): array
    {
        return array_merge($logRecord->toArray(), [
            "numeric_level" => LogLevel::normalizeLevel($logRecord->getLevel()),
        ]);
    }
}
