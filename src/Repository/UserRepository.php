<?php

namespace App\Repository;

use PDO;

class UserRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function saveGoogleUser(array $user): int
    {
        $sql = "
            INSERT INTO users (
                google_sub,
                name,
                email,
                avatar,
                last_login_at
            )
            VALUES (
                :google_sub,
                :name,
                :email,
                :avatar,
                NOW()
            )
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                email = VALUES(email),
                avatar = VALUES(avatar),
                last_login_at = NOW()
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'google_sub' => $user['google_sub'],
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar' => $user['avatar']
        ]);

        $stmt = $this->pdo->prepare(
            "SELECT id FROM users WHERE google_sub = ?"
        );

        $stmt->execute([$user['google_sub']]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Claim quyền gửi email.
     * Ngăn hai request đồng thời gửi email trùng.
     */
    public function claimWelcomeEmail(int $userId): bool
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("
                SELECT welcome_email_status, updated_at
                FROM users
                WHERE id = ?
                FOR UPDATE
            ");

            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user) {
                $this->pdo->rollBack();
                return false;
            }

            if ($user['welcome_email_status'] === 'sent') {
                $this->pdo->commit();
                return false;
            }

            // Nếu request gửi email trước đó bị treo,
            // cho phép thử lại sau 10 phút.
            if ($user['welcome_email_status'] === 'sending') {
                $stmt = $this->pdo->prepare("
                    SELECT updated_at <= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
                    FROM users
                    WHERE id = ?
                ");

                $stmt->execute([$userId]);

                if (!(bool) $stmt->fetchColumn()) {
                    $this->pdo->commit();
                    return false;
                }
            }

            $stmt = $this->pdo->prepare("
                UPDATE users
                SET welcome_email_status = 'sending'
                WHERE id = ?
            ");

            $stmt->execute([$userId]);

            $this->pdo->commit();

            return true;

        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function markEmailSent(int $userId): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET
                welcome_email_status = 'sent',
                welcome_email_sent_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([$userId]);
    }

    public function markEmailFailed(int $userId): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET welcome_email_status = 'failed'
            WHERE id = ?
        ");

        $stmt->execute([$userId]);
    }
}