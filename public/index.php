<?php

require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user = $_SESSION['user'] ?? null;

$emailStatus = $_SESSION['email_status'] ?? null;
unset($_SESSION['email_status']);

function e($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= e(APP_NAME) ?></title>

    <link rel="stylesheet"
          href="assets/css/style.css">

    <script src="https://accounts.google.com/gsi/client"
            async
            defer>
    </script>

    <script src="assets/js/app.js" defer></script>
</head>

<body>

<div class="container">

    <?php if (!$user): ?>

        <div class="login-card">

            <div class="logo">G</div>

            <h1>Welcome!</h1>

            <p class="description">
                Đăng nhập bằng Google để tiếp tục.
                Bạn sẽ nhận được email chào mừng
                sau lần đăng nhập đầu tiên.
            </p>

            <div id="g_id_onload"
                 data-client_id="<?= e(GOOGLE_CLIENT_ID) ?>"
                 data-login_uri="<?= e(APP_URL . '/callback.php') ?>"
                 data-ux_mode="redirect"
                 data-auto_prompt="false">
            </div>

            <div class="google-button-container">

                <div class="g_id_signin"
                     data-type="standard"
                     data-size="large"
                     data-theme="outline"
                     data-text="signin_with"
                     data-shape="rectangular"
                     data-logo_alignment="left">
                </div>

            </div>

            <p class="privacy">
                Bằng việc đăng nhập, bạn đồng ý
                với chính sách sử dụng của website.
            </p>

        </div>

    <?php else: ?>

        <div class="dashboard">

            <div class="dashboard-header">

                <h1>Dashboard</h1>

                <form action="logout.php" method="POST">

                    <input type="hidden"
                           name="csrf_token"
                           value="<?= e($_SESSION['csrf_token']) ?>">

                    <button type="submit"
                            class="logout-button">
                        Đăng xuất
                    </button>

                </form>

            </div>

            <div class="profile-card">

                <?php if ($user['avatar']): ?>

                    <img class="avatar"
                         src="<?= e($user['avatar']) ?>"
                         alt="User avatar">

                <?php endif; ?>

                <h2>
                    Xin chào, <?= e($user['name']) ?>!
                </h2>

                <p><?= e($user['email']) ?></p>

                <div class="status">
                    <span class="status-dot"></span>
                    Đăng nhập Google thành công
                </div>

                <?php if ($emailStatus === 'sent'): ?>

                    <div class="alert success">
                        Email chào mừng đã được gửi
                        đến hộp thư của bạn!
                    </div>

                <?php elseif ($emailStatus === 'failed'): ?>

                    <div class="alert warning">
                        Đăng nhập thành công nhưng
                        gửi email chưa thành công.
                        Hệ thống sẽ thử lại ở lần đăng nhập sau.
                    </div>

                <?php elseif ($emailStatus === 'already_sent'): ?>

                    <div class="alert info">
                        Bạn đã nhận email chào mừng trước đó.
                    </div>

                <?php endif; ?>

            </div>

        </div>

    <?php endif; ?>

</div>

</body>
</html>