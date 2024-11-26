<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['account_id']) || !isset($_POST['withdraw_amount'])) {
    header("Location: ../index.php");
    exit();
}

$account_id = $_POST['account_id'];
$amount = $_POST['withdraw_amount'];
$type = $_POST['withdraw_type'];

try {
    $conn->beginTransaction();

    // Check if the account has enough balance
    $stmt = $conn->prepare("SELECT SaldoActual FROM cuenta WHERE NumeroCuenta = ? AND IdCliente_fk = ?");
    $stmt->execute([$account_id, $_SESSION['user_id']]);
    $current_balance = $stmt->fetchColumn();

    if ($current_balance < $amount) {
        throw new Exception("Insufficient funds");
    }

    // Update account balance
    $stmt = $conn->prepare("UPDATE cuenta SET SaldoActual = SaldoActual - ? WHERE NumeroCuenta = ? AND IdCliente_fk = ?");
    $stmt->execute([$amount, $account_id, $_SESSION['user_id']]);

    // Create transaction
    $stmt = $conn->prepare("INSERT INTO transaccion (IdCuentaOrigen_fk, IdCuentaDestino_fk, TipoTransaccion, MontoAplica, Metodo, Descripcion) VALUES (?, ?, 'Retiro', ?, ?, ?)");
    $stmt->execute([$account_id, null, $amount, $type, "Retiro en $type"]);
    $transaction_id = $conn->lastInsertId();

    // Create movimientocuenta
    $stmt = $conn->prepare("INSERT INTO movimientocuenta (IdCuenta_fk, IdTransaccion_fk, TipoMovimiento, SaldoPrevio, SaldoPosterior) VALUES (?, ?, 'Debito', ?, ?)");
    $stmt->execute([$account_id, $transaction_id, $current_balance, $current_balance - $amount]);

    $conn->commit();
    header("Location: ../index.php?account=" . $account_id . "&success=withdraw");
} catch (Exception $e) {
    $conn->rollBack();
    header("Location: ../index.php?account=" . $account_id . "&error=withdraw&message=" . urlencode($e->getMessage()));
}