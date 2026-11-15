<?php

$name = htmlspecialchars(
    $user['name'],
    ENT_QUOTES,
    'UTF-8'
);

$email = htmlspecialchars(
    $user['email'],
    ENT_QUOTES,
    'UTF-8'
);

$avatar = !empty($user['avatar'])
    ? htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8')
    : null;

$avatarHtml = $avatar
    ? '<img src="' . $avatar . '"
        width="80"
        height="80"
        style="border-radius:50%;object-fit:cover;">'
    : '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>Welcome to <?= APP_NAME ?></title>
</head>

<body style="
    margin:0;
    padding:30px 10px;
    background:#f3f4f6;
    font-family:Arial,sans-serif;
">

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td align="center">

<table width="600"
       cellpadding="0"
       cellspacing="0"
       style="
            max-width:600px;
            background:#ffffff;
            border-radius:12px;
            overflow:hidden;
       ">

    <tr>
        <td style="
            background:#2563eb;
            padding:35px;
            text-align:center;
            color:white;
        ">
            <h1 style="margin:0;">
                Welcome!
            </h1>

            <p>
                Chào mừng bạn đến với <?= APP_NAME ?>
            </p>
        </td>
    </tr>

    <tr>
        <td style="
            padding:35px;
            text-align:center;
        ">

            <?= $avatarHtml ?>

            <h2>
                Xin chào <?= $name ?>!
            </h2>

            <p style="
                color:#555;
                line-height:1.7;
            ">
                Cảm ơn bạn đã đăng nhập và trở thành
                thành viên của <?= APP_NAME ?>.
            </p>

            <div style="
                background:#f3f4f6;
                padding:20px;
                border-radius:8px;
                text-align:left;
            ">
                <p><b>Thông tin tài khoản</b></p>

                <p>Họ tên: <?= $name ?></p>

                <p>Email: <?= $email ?></p>

                <p>Đăng nhập: Google</p>
            </div>

            <p style="
                margin-top:30px;
                color:#555;
            ">
                Chúc bạn có trải nghiệm tuyệt vời!
            </p>

        </td>
    </tr>

    <tr>
        <td style="
            background:#f9fafb;
            padding:20px;
            text-align:center;
            color:#888;
            font-size:12px;
        ">
            © <?= date('Y') ?> <?= APP_NAME ?>.
            All rights reserved.
        </td>
    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>
