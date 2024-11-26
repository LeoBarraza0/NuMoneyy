<?php
session_start();
require_once 'config/db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $telefono = $_POST['telefono'];
    $contrasena = $_POST['contrasena'];

    $stmt = $conn->prepare("SELECT IdCliente, NombreCompleto, Contrasena FROM cliente WHERE telefono = ?");
    $stmt->execute([$telefono]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($contrasena, $user['Contrasena'])) {
        $_SESSION['user_id'] = $user['IdCliente'];
        $_SESSION['user_name'] = $user['NombreCompleto'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid phone number or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NuMoney</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="login-container">
        <h1>Welcome to NuMoney</h1>
        <form action="login.php" method="post" class="login-form">
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="telefono">Telefono</label>
                <input type="telefono" id="telefono" name="telefono" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Password</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>