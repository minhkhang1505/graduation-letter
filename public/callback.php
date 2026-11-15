<?php

require_once __DIR__ . '/../config/database.php';

use App\Auth\GoogleAuth;
use App\Repository\UserRepository;
use App\Services\EmailService;

use function error_log;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Google Identity Services double-submit-cookie CSRF check.
$cookieCsrf = $_COOKIE['g_csrf_token'] ?? '';
$postCsrf = $_POST['g_csrf_token'] ?? '';

if (
    !$cookieCsrf ||
    !$postCsrf ||
    !hash_equals($cookieCsrf, $postCsrf)
) {
    http_response_code(400);
    exit('Invalid CSRF token');
}

$idToken = $_POST['credential'] ?? '';

if (!$idToken) {
    http_response_code(400);
    exit('Missing Google credential');
}

try {
    $googleAuth = new GoogleAuth();

    $user = $googleAuth->verifyToken($idToken);

    $repository = new UserRepository($pdo);

    // Save or update Google user.
    $userId = $repository->saveGoogleUser($user);

    // Prevent session fixation.
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id' => $userId,
        'google_sub' => $user['google_sub'],
        'name' => $user['name'],
        'email' => $user['email'],
        'avatar' => $user['avatar']
    ];

    // Send welcome email only once.
    if ($repository->claimWelcomeEmail($userId)) {
        try {
            $emailService = new EmailService();

            $emailService->sendWelcomeEmail($user);

            $repository->markEmailSent($userId);

            $_SESSION['email_status'] = 'sent';

        } catch (\Throwable $e) {
            $repository->markEmailFailed($userId);

            $_SESSION['email_status'] = 'failed';

            error_log($e->getMessage());
        }
    } else {
        $_SESSION['email_status'] = 'already_sent';
    }

    header('Location: index.php');
    exit;

} catch (\Throwable $e) {
    error_log($e->getMessage());

    http_response_code(401);
    exit('Google authentication failed.');
}