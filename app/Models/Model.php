<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Logger;
use PDOStatement;

/**
 * Base model.
 *
 * Centralises access to the shared Database instance and offers a few
 * convenience helpers. Every query uses prepared statements – never
 * concatenate user input into SQL.
 */
abstract class Model
{
    /** @var Database */
    protected $db;

    /** @var string Table name (set by the concrete model) */
    protected $table = '';

    public function __construct()
    {
        $this->db = Database::instance();
    }

    /**
     * Fetch every row of the model's table (ordered).
     */
    public function all(string $orderBy = 'id'): array
    {
        return $this->db->all("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
    }

    /**
     * Fetch one row by primary key (or null).
     */
    public function findById(int $id): ?array
    {
        return $this->db->first("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }

    /**
     * Delete a row by primary key.
     */
    public function deleteById(int $id): bool
    {
        return $this->db->execute("DELETE FROM {$this->table} WHERE id = :id", ['id' => $id]) > 0;
    }

    /**
     * Count all rows (optionally with a WHERE clause).
     */
    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }
        return (int) $this->db->scalar($sql, $params);
    }

    /**
     * Run an arbitrary prepared statement through the shared connection.
     */
    protected function run(string $sql, array $params = []): PDOStatement
    {
        return $this->db->run($sql, $params);
    }

    /**
     * Generic create helper – inserts an associative array safely.
     *
     * @return int The new row id
     */
    protected function insertRow(array $data): int
    {
        $columns = array_keys($data);
        $fields  = implode(', ', $columns);
        $holders = implode(', ', array_map(fn ($c) => ":{$c}", $columns));

        $id = $this->db->insert(
            "INSERT INTO {$this->table} ({$fields}) VALUES ({$holders})",
            $data
        );
        return (int) $id;
    }

    /**
     * Generic update helper – updates columns by id safely.
     */
    protected function updateRow(int $id, array $data): bool
    {
        $sets = implode(', ', array_map(fn ($c) => "{$c} = :{$c}", array_keys($data)));
        $data['id'] = $id;

        return $this->db->execute(
            "UPDATE {$this->table} SET {$sets} WHERE id = :id",
            $data
        ) > 0;
    }

    /**
     * Standard audit log for CRUD operations.
     */
    protected function logAction(string $action, array $context): void
    {
        $user = \App\Core\Auth::user();
        Logger::info($action, array_merge(['actor' => $user['username'] ?? 'system'], $context));
    }
}
