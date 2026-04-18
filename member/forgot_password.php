<?php
session_start();
require_once '../config.php';
require_once '../Email_Helper.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate unique token
            $token = sha1(uniqid() . rand());
            $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Delete old tokens
            $pdo->prepare("DELETE FROM token WHERE user_id = ?")->execute([$user['id']]);

            // Insert new token
            $insert = $pdo->prepare("INSERT INTO token (id, expire, user_id) VALUES (?, ?, ?)");
            if ($insert->execute([$token, $expire, $user['id']])) {
                $reset_link = base("member/reset_password.php?token=$token");

                // Styled HTML email content
                $subject = "Reset Your Password – Adidas Shop";
                $htmlContent = '
                <!DOCTYPE html>
                <html>
                <head><meta charset="UTF-8"></head>
                <body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;">
                    <div style="max-width: 500px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <div style="background: #000000; padding: 20px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; letter-spacing: 3px;">ADIDAS</h1>
                        </div>
                        <div style="padding: 30px;">
                            <p style="font-size: 16px; color: #333;">Dear ' . htmlspecialchars($user['name']) . ',</p>
                            <p style="font-size: 16px; color: #333;">We received a request to reset your password. Click the button below to create a new password:</p>
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="' . $reset_link . '" style="display: inline-block; background: #000000; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">Reset Password</a>
                            </div>
                            <p style="font-size: 14px; color: #666;">This link will expire in <strong>1 hour</strong>.</p>
                            <p style="font-size: 14px; color: #666;">If you did not request this, please ignore this email.</p>
                            <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">
                            <p style="font-size: 12px; color: #999;">Adidas Shop Team</p>
                        </div>
                    </div>
                </body>
                </html>';

                // Send email
                $mail = get_mail();
                $mail->addAddress($user['email'], $user['name']);
                $mail->Subject = $subject;
                $mail->Body = $htmlContent;
                $mail->send();

                $success = "A password reset link has been sent to your email address.";
            } else {
                $error = "Failed to save reset token. Please try again.";
            }
        } else {
            // Security: do not reveal that email does not exist
            $success = "If your email is registered, you will receive a reset link.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <div class="logo"><h1>ADIDAS</h1><p>Reset your password</p></div>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!$success && !$error): ?>
        <form method="POST">
            <div class="form-group"><label>Email Address</label><input type="email" name="email" required></div>
            <button type="submit" class="btn-primary">Send Reset Link</button>
        </form>
        <?php endif; ?>
        <div class="auth-link"><a href="login.php">← Back to Login</a></div>
    </div>
</div>
</body>
</html>