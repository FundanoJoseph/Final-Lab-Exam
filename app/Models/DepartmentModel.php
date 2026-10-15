<?php

namespace App\Models;

/**
 * Department data access (with search).
 */
class DepartmentModel extends Model
{
    protected $table = 'departments';

    /**
     * List departments, optionally filtered by a search term.
     */
    public function search(?string $term = null): array
    {
        if ($term === null || $term === '') {
            return $this->db->all(
                'SELECT d.*,
                        (SELECT COUNT(*) FROM courses c WHERE c.department_id = d.id) AS course_count
                 FROM departments d
                 ORDER BY d.name'
            );
        }

        $like = '%' . $term . '%';
        return $this->db->all(
            'SELECT d.*,
                    (SELECT COUNT(*) FROM courses c WHERE c.department_id = d.id) AS course_count
             FROM departments d
             WHERE d.name LIKE :q OR d.code LIKE :q OR d.description LIKE :q
             ORDER BY d.name',
            ['q' => $like]
        );
    }

    /**
     * Create a department.
     */
    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $id = $this->insertRow($data);
        $this->logAction('department.created', ['id' => $id, 'name' => $data['name'] ?? '']);
        return $id;
    }

    /**
     * Update a department.
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $ok = $this->updateRow($id, $data);
        $this->logAction('department.updated', ['id' => $id, 'fields' => array_keys($data)]);
        return $ok;
    }

    /**
     * Delete a department (only when no courses reference it).
     */
    public function delete(int $id): bool
    {
        $ok = $this->deleteById($id);
        if ($ok) {
            $this->logAction('department.deleted', ['id' => $id]);
        }
        return $ok;
    }

    /**
     * How many courses belong to this department?
     */
    public function courseCount(int $id): int
    {
        return (int) $this->db->scalar(
            'SELECT COUNT(*) FROM courses WHERE department_id = :id',
            ['id' => $id]
        );
    }

    /**
     * Find by code (used for uniqueness validation).
     */
    public function findByCode(string $code, ?int $excludeId = null): ?array
    {
        $sql = 'SELECT * FROM departments WHERE code = :code';
        $params = ['code' => $code];
        if ($excludeId !== null) {
            $sql .= ' AND id != :exclude';
            $params['exclude'] = $excludeId;
        }
        return $this->db->first($sql, $params);
    }
}
