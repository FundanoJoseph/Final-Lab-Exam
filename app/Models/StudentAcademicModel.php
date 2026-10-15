<?php

namespace App\Models;

/**
 * Student academic history data access
 * (one row = one term/semester record).
 */
class StudentAcademicModel extends Model
{
    protected $table = 'student_academics';

    /** Allowed term values. */
    public const SEMESTERS = ['1st Semester', '2nd Semester', 'Summer'];

    /** Allowed year levels. */
    public const YEAR_LEVELS = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];

    /** Allowed beginning-of-semester statuses. */
    public const BEG_STATUSES = ['Enrolled', 'Pending', 'On Leave'];

    /** Allowed end-of-semester statuses. */
    public const END_STATUSES = ['In Progress', 'Passed', 'Failed', 'Incomplete'];

    /**
     * All academic records of one student (newest first).
     */
    public function findByStudent(int $studentId): array
    {
        return $this->db->all(
            'SELECT sa.*, c.name AS course_name, c.code AS course_code,
                    d.name AS department_name, d.code AS department_code
             FROM student_academics sa
             LEFT JOIN courses c ON c.id = sa.course_id
             LEFT JOIN departments d ON d.id = c.department_id
             WHERE sa.student_id = :sid
             ORDER BY sa.school_year DESC,
                      CASE sa.semester
                          WHEN \'1st Semester\' THEN 1
                          WHEN \'2nd Semester\' THEN 2
                          ELSE 3
                      END',
            ['sid' => $studentId]
        );
    }

    /**
     * One academic record (scoped to its student).
     */
    public function findForStudent(int $studentId, int $recordId): ?array
    {
        return $this->db->first(
            'SELECT sa.*, c.name AS course_name, c.code AS course_code,
                    d.code AS department_code
             FROM student_academics sa
             LEFT JOIN courses c ON c.id = sa.course_id
             LEFT JOIN departments d ON d.id = c.department_id
             WHERE sa.id = :id AND sa.student_id = :sid',
            ['id' => $recordId, 'sid' => $studentId]
        );
    }

    /**
     * Add an academic record.
     */
    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $id = $this->insertRow($data);
        $this->logAction('academic.created', [
            'id'        => $id,
            'student_id'=> $data['student_id'] ?? null,
            'term'      => ($data['semester'] ?? '') . ' ' . ($data['school_year'] ?? ''),
        ]);
        return $id;
    }

    /**
     * Update an academic record.
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $ok = $this->updateRow($id, $data);
        $this->logAction('academic.updated', ['id' => $id, 'fields' => array_keys($data)]);
        return $ok;
    }

    /**
     * Delete an academic record.
     */
    public function delete(int $id): bool
    {
        $ok = $this->deleteById($id);
        if ($ok) {
            $this->logAction('academic.deleted', ['id' => $id]);
        }
        return $ok;
    }

    /**
     * Total academic records (dashboard summary).
     */
    public function countAll(): int
    {
        return $this->count();
    }
}
