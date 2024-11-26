<?php
session_start();
require_once 'config/db.php';


$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombreCompleto = $_POST['nombreCompleto'];
    $tipoDocumento = $_POST['tipoDocumento'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $fechaRegistro = date('Y-m-d H:i:s');
    $estado = 'Activo';
    $telefono = $_POST['telefono'];

    try {
        // Verificar que el correo no esté registrado
        $stmt = $conn->prepare("SELECT COUNT(*) FROM cliente WHERE Correo = ?");
        $stmt->execute([$correo]);
        $correoExiste = $stmt->fetchColumn();

        if ($correoExiste > 0) {
            $error = "El correo ya está registrado. Por favor, use otro.";
        } else {
            // Generar un IdCliente único
            do {
                $idCliente = rand(1000, 9999);
                $stmt = $conn->prepare("SELECT COUNT(*) FROM cliente WHERE IdCliente = ?");
                $stmt->execute([$idCliente]);
                $idExiste = $stmt->fetchColumn();
            } while ($idExiste > 0);

            // Insertar el nuevo cliente
            $stmt = $conn->prepare("INSERT INTO cliente (IdCliente, NombreCompleto, Tipo_Documento, Correo, Direccion, Fecha_Registro, Estado, Contrasena, Telefono) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");
            $stmt->execute([$idCliente, $nombreCompleto, $tipoDocumento, $correo, $direccion, $fechaRegistro, $estado, $contrasena, $telefono]);

            $success = "Registro exitoso. Ya puedes iniciar sesión.";
        }
    } catch (PDOException $e) {
        $error = "Error en el registro: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - NuMoney</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="register-container">
        <h1>Register for NuMoney</h1>
        <form action="register.php" method="post" class="register-form">
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="nombreCompleto">Full Name</label>
                <input type="text" id="nombreCompleto" name="nombreCompleto" required>
            </div>
            <div class="form-group">
                <label for="tipoDocumento">Document Type</label>
                <select id="tipoDocumento" name="tipoDocumento" required>
                    <option value="CC">Cédula de Ciudadanía</option>
                    <option value="CE">Cédula de Extranjería</option>
                    <option value="TI">Tarjeta de Identidad</option>
                    <option value="PP">Pasaporte</option>
                </select>
            </div>
            <div class="form-group">
                <label for="correo">Email</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div class="form-group">
                <label for="telefono">Telefono</label>
                <input type="text" id="telefono" name="telefono" required>
            </div>
            <div class="form-group">
                <label for="direccion">Direccion</label>
                <input type="text" id="direccion" name="direccion" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Password</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>