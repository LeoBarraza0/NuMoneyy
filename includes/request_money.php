<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['to_account_id']) || !isset($_POST['from_account']) || !isset($_POST['request_amount'])) {
    header("Location: ../index.php");
    exit();
}

$to_account_id = $_POST['to_account_id'];
$from_account = $_POST['from_account'];
$amount = $_POST['request_amount'];

try {
    $conn->beginTransaction();

    // Check if the sender's account exists and is active
    $stmt = $conn->prepare("SELECT IdCuenta FROM cuenta WHERE NumeroCuenta = ? AND Estado = 'Activa'");
    $stmt->execute([$from_account]);
    $sender_account = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sender_account) {
        throw new Exception("Sender account not found or inactive");
    }

    // Create money request
    $stmt = $conn->prepare("INSERT INTO solicituddinero (IdCuentaOrigen_fk, IdCuentaDestino_fk, Monto, Estado, FechaSolicitud) VALUES (?, ?, ?, 'Pendiente', NOW())");
    $stmt->execute([$sender_account['IdCuenta'], $to_account_id, $amount]);

    $conn->commit();
    header("Location: ../index.php?account=" . $to_account_id . "&success=request_money");
} catch (Exception $e) {
    $conn->rollBack();
    header("Location: ../index.php?account=" . $to_account_id . "&error=request_money&message=" . urlencode($e->getMessage()));
}