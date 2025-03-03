<?php

class GlobalLogger {
    private static $instance = null;
    private $logDirectory;
    private $maxFileSize;
    private $pdo;

    const DEBUG    = 'DEBUG';
    const INFO     = 'INFO';
    const WARNING  = 'WARNING';
    const ERROR    = 'ERROR';
    const CRITICAL = 'CRITICAL';

    private function __construct($config = []) {
        $this->logDirectory = $config['logDirectory'] ?? __DIR__ . '/logs';
        if (!is_dir($this->logDirectory)) {
            mkdir($this->logDirectory, 0755, true);
        }
        $this->maxFileSize = $config['maxFileSize'] ?? 10485760;

        if (isset($config['db'])) {
            try {
                $this->pdo = new PDO(
                    $config['db']['dsn'], 
                    $config['db']['username'], 
                    $config['db']['password']
                );
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                $this->pdo = null;
            }
        }
    }

    public static function getInstance($config = []) {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function log($level, $module, $message, array $context = [], $source = null, $trace_id = null, $error_details = null, $metadata = []) {
// Retrieve the APP_ENV environment variable from $_ENV (default to 'production' if not set)
$env = $_ENV['APP_ENV'] ?? 'production';
$level = strtoupper($level);

// If in production, only log messages that are WARNING or higher
if ($env === 'production' && in_array($level, [self::DEBUG, self::INFO])) {
    return;
}
// In development, all levels will be logged

        // Get high-precision timestamp
        $time = hrtime(true); // Nanoseconds since Unix epoch
        $seconds = intdiv($time, 1_000_000_000); // Extract seconds
        $nanoseconds = $time % 1_000_000_000; // Extract remaining nanoseconds
    
        // Format timestamp in ISO 8601 with nanosecond precision
        $timestamp = gmdate('Y-m-d\TH:i:s', $seconds) . sprintf('.%09dZ', $nanoseconds);
    
        // Format context and source as inline key=value pairs
        $contextString = $this->formatKeyValuePairs($context);
        $sourceString = $this->formatKeyValuePairs($source);
    
        // Build log line
        $logLine = sprintf(
            "%s | %-8s | %-12s | %s || %s%s%s\n",
            $timestamp,
            $level,
            $module,
            $message,
            ($contextString ? "$contextString" : ""),
            ($sourceString ? " | $sourceString" : ""),
            ($trace_id ? " | trace_id=$trace_id" : "")
        );
    
        $date = gmdate('Y-m-d');
        $currentLogFile = "{$this->logDirectory}/app-{$date}.log";
    
        if (file_exists($currentLogFile) && filesize($currentLogFile) >= $this->maxFileSize) {
            rename($currentLogFile, $currentLogFile . '.1');
        }
    
        file_put_contents($currentLogFile, $logLine, FILE_APPEND | LOCK_EX);
    
        if (($level === self::ERROR || $level === self::CRITICAL) && $this->pdo) {
            try {
                $stmt = $this->pdo->prepare("
                    INSERT INTO logs (timestamp, level, module, message, context, source, trace_id, error_details, metadata)
                    VALUES (:timestamp, :level, :module, :message, :context, :source, :trace_id, :error_details, :metadata)
                ");
                $stmt->execute([
                    ':timestamp'     => $timestamp,
                    ':level'         => $level,
                    ':module'        => $module,
                    ':message'       => $message,
                    ':context'       => json_encode($context),
                    ':source'        => json_encode($source),
                    ':trace_id'      => $trace_id,
                    ':error_details' => $error_details,
                    ':metadata'      => json_encode($metadata),
                ]);
            } catch (PDOException $e) {
                // Handle DB log failure
            }
        }
    }
    
    private function formatKeyValuePairs($data) {
        if (!is_array($data) || empty($data)) return "";
        $pairs = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $pairs[] = "$key=" . json_encode($value);
            } else {
                $pairs[] = "$key=$value";
            }
        }
        return implode(" | ", $pairs);
    }
}
?>
