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

 // Remove logDirectory (and optionally maxFileSize) from the external config,
    // so that they can only be controlled via environment variables or hard-coded fallback.
    private function __construct($config = []) {
        // Use only environment variable or fallback (project root logs folder)
        $this->logDirectory = $_ENV['LOG_DIRECTORY'] ?? __DIR__ . '/../../logs';
        if (!is_dir($this->logDirectory)) {
            mkdir($this->logDirectory, 0755, true);
        }
        
        // Get max file size from environment variable, fallback to 10MB
        $this->maxFileSize = $_ENV['MAX_LOG_FILE_SIZE'] ?? 10485760;
        
        // Read DB configuration from environment variables
        if (isset($_ENV['DB_DSN']) && $_ENV['DB_DSN'] !== '') {
            $dsn      = $_ENV['DB_DSN'];
            $username = $_ENV['DB_USERNAME'] ?? '';
            $password = $_ENV['DB_PASSWORD'] ?? '';
            try {
                $this->pdo = new PDO($dsn, $username, $password);
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
    
        // In production, only log messages that are WARNING or higher
        if ($env === 'production' && in_array($level, [self::DEBUG, self::INFO])) {
            return;
        }
    
        $microtime = microtime(true);
        $seconds = floor($microtime);
        $nanoseconds = ($microtime - $seconds) * 1_000_000_000;
        $timestamp = gmdate('Y-m-d\TH:i:s', $seconds) . sprintf('.%09dZ', $nanoseconds);
        
        // Format context and source as inline key=value pairs for text logging
        $contextString = $this->formatKeyValuePairs($context);
        $sourceString  = $this->formatKeyValuePairs($source);
    
        // In production, remove extra details (e.g. file, line) from the text log
        if ($env === 'production') {
            $sourceString = '';
        }
    
        // Build text log line
        $logLine = sprintf(
            "%s | %-8s | %-12s | %s || %s%s%s\n",
            $timestamp,
            $level,
            $module,
            $message,
            ($contextString ? $contextString : ""),
            ($sourceString ? " | $sourceString" : ""),
            ($trace_id ? " | trace_id=$trace_id" : "")
        );
    
        // Determine file names based on the current date
        $date = gmdate('Y-m-d');
        $currentLogFile = "{$this->logDirectory}/app-{$date}.log";
        $currentJsonLogFile = "{$this->logDirectory}/app-{$date}.json";
    
        // Rotate text log file if needed
        if (file_exists($currentLogFile) && filesize($currentLogFile) >= $this->maxFileSize) {
            rename($currentLogFile, $currentLogFile . '.1');
        }
        // Append text log entry
        file_put_contents($currentLogFile, $logLine, FILE_APPEND | LOCK_EX);
    
        // Build log data array for JSON logging
        $jsonData = [
            'timestamp'     => $timestamp,
            'level'         => $level,
            'module'        => $module,
            'message'       => $message,
            'context'       => $context,   // stored as an array
            'source'        => $source,    // stored as an array or string
            'trace_id'      => $trace_id,
            'error_details' => $error_details,
            'metadata'      => $metadata,
        ];
    
        // Pretty-print JSON log entry
        $jsonLogEntry = json_encode($jsonData, JSON_PRETTY_PRINT);
    
        // Rotate JSON log file if needed
        if (file_exists($currentJsonLogFile) && filesize($currentJsonLogFile) >= $this->maxFileSize) {
            rename($currentJsonLogFile, $currentJsonLogFile . '.1');
        }
        // Append JSON log entry with newline separator
        file_put_contents($currentJsonLogFile, $jsonLogEntry . "\n", FILE_APPEND | LOCK_EX);
    
        // Log all entries that pass the env filtering to the DB as well.
        require_once __DIR__ . '/GlobalDB.php';
        $db = GlobalDB::getInstance()->getConnection();
    
        if ($db) {
            try {
                $stmt = $db->prepare("
                    INSERT INTO logs (timestamp, level, message, context, source, trace_id, error_details, metadata)
                    VALUES (:timestamp, :level, :message, :context, :source, :trace_id, :error_details, :metadata)
                ");
                $stmt->execute([
                    ':timestamp'     => $timestamp,
                    ':level'         => $level,
                    ':message'       => $message,
                    ':context'       => json_encode($context),
                    ':source'        => json_encode($source),
                    ':trace_id'      => $trace_id,
                    ':error_details' => $error_details,
                    ':metadata'      => json_encode($metadata),
                ]);
            } catch (PDOException $e) {
                // Handle DB log failure
                error_log("DB Log Error: " . $e->getMessage());
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
