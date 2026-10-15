<?php

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Central database access layer.
 *
 * Every query in the application goes through this class so that:
 *  - credentials come from the external config file,
 *  - a single PDO connection is reused (no local file/session state),
 *  - prepared statements are used for every query (SQL-injection safe),
 *  - MySQL and SQLite both work (portable between InfinityFree and local dev).
 */
class Database
{
    /** @var Database|null Singleton instance */
    private static $instance = null;

    /** @var PDO */
    private $pdo;

    /**
     * Build the PDO connection from the external configuration.
     *
     * @throws PDOException when the connection cannot be established.
     */
    private function __construct()
    {
        $config = Config::get('db');
        $driver = $config['driver'] ?? 'mysql';

        try {
            if ($driver === 'sqlite') {
                $path = $config['sqlite']['path'];
                $dir  = dirname($path);
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }
                $this->pdo = new PDO('sqlite:' . $path, null, null, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                // Enforce foreign keys so ON DELETE CASCADE works on SQLite too.
                $this->pdo->exec('PRAGMA foreign_keys = ON');
            } else {
                $mysql = $config['mysql'];
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    $mysql['host'],
                    $mysql['port'],
                    $mysql['database'],
                    $mysql['charset']
                );
                $this->pdo = new PDO($dsn, $mysql['username'], $mysql['password'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false, // real prepared statements
                ]);
            }
        } catch (PDOException $e) {
            // Log the sensitive connection details internally, show a friendly
            // message to the user (see index.php exception handler).
            Logger::error('database.connection_failed', [
                'driver' => $driver,
                'error'  => $e->getMessage(),
            ]);
            throw new \RuntimeException(
                'Database connection failed. Please check the settings in config/config.php.',
                0,
                $e
            );
        }
    }

    /** Prevent cloning of the singleton. */
    private function __clone() {}

    /**
     * Get the shared Database instance.
     */
    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Expose the underlying PDO handle (for advanced use / transactions).
     */
    public function pdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Run a prepared statement and return it, ready to fetch.
     *
     * @param string $sql    SQL with named placeholders (:name)
     * @param array  $params Placeholder values
     */
    public function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch every matching row.
     */
    public function all(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    /**
     * Fetch a single row (or null).
     */
    public function first(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Fetch a single scalar value (e.g. COUNT(*)).
     */
    public function scalar(string $sql, array $params = [])
    {
        $value = $this->run($sql, $params)->fetchColumn();
        return $value === false ? null : $value;
    }

    /**
     * Execute an INSERT/UPDATE/DELETE and return affected row count.
     */
    public function execute(string $sql, array $params = []): int
    {
        return $this->run($sql, $params)->rowCount();
    }

    /**
     * Insert a row and return its auto-increment id.
     *
     * @return string|int last insert id
     */
    public function insert(string $sql, array $params = [])
    {
        $this->run($sql, $params);
        return $this->pdo->lastInsertId();
    }
}
