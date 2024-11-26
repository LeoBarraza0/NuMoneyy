<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['from_account_id']) || !isset($_POST['to_account']) || !isset($_POST['transfer_amount'])) {
    header("Location: ../index.php");
    exit();
}

$from_account_id = $_POST['from_account_id'];
$to_account = $_POST['to_account'];
$amount = $_POST['transfer_amount'];

try {
    $conn->beginTransaction();

    // Check if the sender's account has enough balance
    $stmt = $conn->prepare("SELECT SaldoActual FROM cuenta WHERE NumeroCuenta = ? AND IdCliente_fk = ?");
    $stmt->execute([$from_account_id, $_SESSION['user_id']]);
    $sender_balance = $stmt->fetchColumn();

    if ($sender_balance < $amount) {
        throw new Exception("Insufficient funds");
    }

    // Check if the recipient's account exists and is active
    $stmt = $conn->prepare("SELECT NumeroCuenta, SaldoActual FROM cuenta WHERE NumeroCuenta = ? AND Estado = 'Activa'");
    $stmt->execute([$to_account]);
    $recipient_account = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recipient_account) {
        throw new Exception("Recipient account not found or inactive");
    }

    // Update sender's account balance
    $stmt = $conn->prepare("UPDATE cuenta SET SaldoActual = SaldoActual - ? WHERE NumeroCuenta = ?");
    $stmt->execute([$amount, $from_account_id]);

    // Update recipient's account balance
    $stmt = $conn->prepare("UPDATE cuenta SET SaldoActual = SaldoActual + ? WHERE NumeroCuenta = ?");
    $stmt->execute([$amount, $recipient_account['NumeroCuenta']]);

    // Create transaction
    $stmt = $conn->prepare("INSERT INTO transaccion (IdCuentaOrigen_fk, IdCuentaDestino_fk, TipoTransaccion, MontoAplica, Metodo, Descripcion) VALUES (?, ?, 'Transferencia', ?, 'App', 'Transferencia entre cuentas')");
    $stmt->execute([$from_account_id, $recipient_account['NumeroCuenta'], $amount]);
    $transaction_id = $conn->lastInsertId();

    // Create movimientocuenta for sender
    $stmt = $conn->prepare("INSERT INTO movimientocuenta (IdCuenta_fk, IdTransaccion_fk, TipoMovimiento, SaldoPrevio, SaldoPosterior) VALUES (?, ?, 'Debito', ?, ?)");
    $stmt->execute([$from_account_id, $transaction_id, $sender_balance, $sender_balance - $amount]);

    // Create movimientocuenta for recipient
    $stmt = $conn->prepare("INSERT INTO movimientocuenta (IdCuenta_fk, IdTransaccion_fk, TipoMovimiento, SaldoPrevio, SaldoPosterior) VALUES (?, ?, 'Credito', ?, ?)");
    $stmt->execute([$recipient_account['NumeroCuenta'], $transaction_id, $recipient_account['SaldoActual'], $recipient_account['SaldoActual'] + $amount]);

    $conn->commit();
    header("Location: ../index.php?account=" . $from_account_id . "&success=transfer");
} catch (Exception $e) {
    $conn->rollBack();
    header("Location: ../index.php?account=" . $from_account_id . "&error=transfer&message=" . urlencode($e->getMessage()));
}