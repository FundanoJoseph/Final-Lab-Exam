<?php

namespace App\Models;

/**
 * Student information & records data access.
 */
class StudentModel extends Model
{
    protected $table = 'students';

    /** Allowed values for the student status field. */
    public const STATUSES = ['Active', 'Inactive', 'Graduated', 'Transferred', 'Dropped'];

    /**
     * List students (with current program + academic record count).
     */
    public function search(?string $term = null): array
    {
        $sql = 'SELECT s.*, c.name AS course_name, c.code AS course_code,
                       d.code AS department_code,
                       (SELECT COUNT(*) FROM student_academics sa WHERE sa.student_id = s.id) AS academic_count
                FROM students s
                LEFT JOIN courses c ON c.id = s.course_id
                LEFT JOIN departments d ON d.id = c.department_id';

        $params = [];
        if ($term !== null && $term !== '') {
            $sql .= ' WHERE s.student_no LIKE :q
                       OR s.first_name LIKE :q
                       OR s.last_name LIKE :q
                       OR s.email LIKE :q';
            $params['q'] = '%' . $term . '%';
        }
        $sql .= ' ORDER BY s.last_name, s.first_name';

        return $this->db->all($sql, $params);
    }

    /**
     * Load one student with course/department labels.
     */
    public function findDetailed(int $id): ?array
    {
        return $this->db->first(
            'SELECT s.*, c.name AS course_name, c.code AS course_code,
                    d.name AS department_name, d.code AS department_code
             FROM students s
             LEFT JOIN courses c ON c.id = s.course_id
             LEFT JOIN departments d ON d.id = c.department_id
             WHERE s.id = :id',
            ['id' => $id]
        );
    }

    /**
     * Create a student record.
     */
    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $id = $this->insertRow($data);
        $this->logAction('student.created', ['id' => $id, 'student_no' => $data['student_no'] ?? '']);
        return $id;
    }

    /**
     * Update student details.
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $ok = $this->updateRow($id, $data);
        $this->logAction('student.updated', ['id' => $id, 'fields' => array_keys($data)]);
        return $ok;
    }

    /**
     * Update only the student status.
     */
    public function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, self::STATUSES, true)) {
            return false;
        }
        $ok = $this->update($id, ['status' => $status]);
        $this->logAction('student.status_updated', ['id' => $id, 'status' => $status]);
        return $ok;
    }

    /**
     * Delete a student together with all academic records
     * (the FK constraints cascade; this also logs the event).
     */
    public function delete(int $id): bool
    {
        $ok = $this->deleteById($id);
        if ($ok) {
            $this->logAction('student.deleted', ['id' => $id]);
        }
        return $ok;
    }

    /**
     * Find by student number (used for uniqueness validation).
     */
    public function findByStudentNo(string $studentNo, ?int $excludeId = null): ?array
    {
        $sql = 'SELECT * FROM students WHERE student_no = :no';
        $params = ['no' => $studentNo];
        if ($excludeId !== null) {
            $sql .= ' AND id != :exclude';
            $params['exclude'] = $excludeId;
        }
        return $this->db->first($sql, $params);
    }

    /**
     * Count students by status (dashboard summary).
     */
    public function countByStatus(string $status): int
    {
        return (int) $this->db->scalar(
            'SELECT COUNT(*) FROM students WHERE status = :status',
            ['status' => $status]
        );
    }

    /**
     * Most recently added students (dashboard).
     */
    public function recent(int $limit = 5): array
    {
        $limit = max(1, min(50, $limit));
        return $this->db->all(
            "SELECT s.*, c.name AS course_name
             FROM students s
             LEFT JOIN courses c ON c.id = s.course_id
             ORDER BY s.created_at DESC
             LIMIT {$limit}"
        );
    }
}
