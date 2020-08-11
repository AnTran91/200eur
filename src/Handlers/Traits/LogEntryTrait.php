<?php

namespace App\Handlers\Traits;


use Gedmo\Loggable\Entity\LogEntry;

trait LogEntryTrait
{
    /**
     * Get the closest version before the order set in production.
     *
     * @param array $logs
     * @param \DateTimeInterface $date
     *
     * @return LogEntry|null
     */
    public function getTheClosestLogToPaymentDate(array $logs, \DateTimeInterface $date): ?LogEntry
    {
        $closestLog = current($logs);
        $min = $closestLog->getLoggedAt()->diff($date)->format('%h%i%s');
        foreach ($logs as $log) {
            if ($min > $log->getLoggedAt()->diff($date)->format('%h%i%s')) {
                $min = $log->getLoggedAt()->diff($date)->format('%h%i%s');
                $closestLog = $log;
            }
        }
        return $closestLog;
    }

    public function getTheClosestLogBeforePaymentDate(array $logs, \DateTimeInterface $date): ?LogEntry
    {   
        foreach ($logs as $log) {
            if ($log->getLoggedAt() <= $date) {
                if (!isset($min)) {
                    $min = $log->getLoggedAt()->diff($date)->format('%h%i%s');
                    $closestLog = $log;
                }
                else {
                    if ($min > $log->getLoggedAt()->diff($date)->format('%h%i%s')) {
                        $min = $log->getLoggedAt()->diff($date)->format('%h%i%s');
                        $closestLog = $log;
                    }
                }
            }
        }
        return isset($closestLog) ? $closestLog : NULL;
    }
}