<?php

namespace App\Models;

/**
 * Course data access (with search + department join).
 */
class CourseModel extends Model
{
    protected $table = 'courses';

    /**
     * List courses joined with their department.
     */
    public function search(?string $term = null): array
    {
        $sql = 'SELECT c.*, d.name AS department_name, d.code AS department_code,
                       (SELECT COUNT(*) FROM students s WHERE s.course_id = c.id) AS student_count
                FROM courses c
                JOIN departments d ON d.id = c.department_id';

        $params = [];
        if ($term !== null && $term !== '') {
            $sql .= ' WHERE c.name LIKE :q OR c.code LIKE :q OR d.name LIKE :q';
            $params['q'] = '%' . $term . '%';
        }
        $sql .= ' ORDER BY d.name, c.name';

        return $this->db->all($sql, $params);
    }

    /**
     * Courses that belong to one department.
     */
    public function findByDepartment(int $departmentId): array
    {
        return $this->db->all(
            'SELECT c.*, d.name AS department_name, d.code AS department_code
             FROM courses c
             JOIN departments d ON d.id = c.department_id
             WHERE c.department_id = :did
             ORDER BY c.name',
            ['did' => $departmentId]
        );
    }

    /**
     * Load one course with its department data.
     */
    public function findWithDepartment(int $id): ?array
    {
        return $this->db->first(
            'SELECT c.*, d.name AS department_name, d.code AS department_code
             FROM courses c
             JOIN departments d ON d.id = c.department_id
             WHERE c.id = :id',
            ['id' => $id]
        );
    }

    /**
     * Create a course.
     */
    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $id = $this->insertRow($data);
        $this->logAction('course.created', ['id' => $id, 'name' => $data['name'] ?? '']);
        return $id;
    }

    /**
     * Update a course.
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $ok = $this->updateRow($id, $data);
        $this->logAction('course.updated', ['id' => $id, 'fields' => array_keys($data)]);
        return $ok;
    }

    /**
     * Delete a course (only when no students/records reference it).
     */
    public function delete(int $id): bool
    {
        $ok = $this->deleteById($id);
        if ($ok) {
            $this->logAction('course.deleted', ['id' => $id]);
        }
        return $ok;
    }

    /**
     * How many students reference this course (current program)?
     */
    public function studentCount(int $id): int
    {
        return (int) $this->db->scalar(
            'SELECT COUNT(*) FROM students WHERE course_id = :id',
            ['id' => $id]
        );
    }

    /**
     * How many academic records reference this course?
     */
    public function academicCount(int $id): int
    {
        return (int) $this->db->scalar(
            'SELECT COUNT(*) FROM student_academics WHERE course_id = :id',
            ['id' => $id]
        );
    }

    /**
     * Find by code (used for uniqueness validation).
     */
    public function findByCode(string $code, ?int $excludeId = null): ?array
    {
        $sql = 'SELECT * FROM courses WHERE code = :code';
        $params = ['code' => $code];
        if ($excludeId !== null) {
            $sql .= ' AND id != :exclude';
            $params['exclude'] = $excludeId;
        }
        return $this->db->first($sql, $params);
    }
}
