<?php

namespace App\Models;

/**
 * System user (login account) data access.
 */
class UserModel extends Model
{
    protected $table = 'users';

    /**
     * Find a user by username (used by Auth::attempt).
     */
    public function findByUsername(string $username): ?array
    {
        return $this->db->first(
            'SELECT * FROM users WHERE username = :username',
            ['username' => $username]
        );
    }

    /**
     * Update the stored password hash.
     */
    public function updatePassword(int $id, string $passwordHash): bool
    {
        $ok = $this->db->execute(
            'UPDATE users SET password_hash = :hash WHERE id = :id',
            ['hash' => $passwordHash, 'id' => $id]
        ) > 0;
        $this->logAction('user.password_updated', ['user_id' => $id]);
        return $ok;
    }

    /**
     * Record a successful login timestamp.
     */
    public function recordLogin(int $id): void
    {
        $this->db->execute(
            'UPDATE users SET last_login_at = :now WHERE id = :id',
            ['now' => date('Y-m-d H:i:s'), 'id' => $id]
        );
    }
}
