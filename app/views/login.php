<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <form method="POST" action="Login/  ">
        <input type="text" name="email" required placeholder="Email">
        <input type="text" name="password" required placeholder="Password">
        <button type="submit">Đăng Nhập</button>
    </form>

    <!-- Khu vực hiển thị thông báo -->
    <?php if (isset($_GET['message'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_GET['message']); ?></p>
    <?php endif; ?>
</body>

</html>