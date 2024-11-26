<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['account_id']) || !isset($_POST['add_money_amount'])) {
    header("Location: ../index.php");
    exit();
}

$account_id = $_POST['account_id'];
$amount = $_POST['add_money_amount'];
$type = $_POST['add_money_type'];
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;

try {
    $conn->beginTransaction();

    // Update account balance
    $stmt = $conn->prepare("UPDATE cuenta SET SaldoActual = SaldoActual + ? WHERE NumeroCuenta = ? AND IdCliente_fk = ?");
    $stmt->execute([$amount, $account_id, $_SESSION['user_id']]);

    // Create transaction
    $stmt = $conn->prepare("INSERT INTO transaccion (IdCuentaOrigen_fk, IdCuentaDestino_fk, TipoTransaccion, MontoAplica, Metodo, Descripcion) VALUES (?, ?, 'Deposito', ?, ?, ?)");
    $stmt->execute([null, $account_id, $amount, $type, $payment_method ? "Deposito en $type con $payment_method" : "Deposito en $type"]);
    $transaction_id = $conn->lastInsertId();

    // Create movimientocuenta
    $stmt = $conn->prepare("INSERT INTO movimientocuenta (IdCuenta_fk, IdTransaccion_fk, TipoMovimiento, SaldoPrevio, SaldoPosterior) VALUES (?, ?, 'Credito', (SELECT SaldoActual - ? FROM cuenta WHERE NumeroCuenta = ?), (SELECT SaldoActual FROM cuenta WHERE NumeroCuenta = ?))");
    $stmt->execute([$account_id, $transaction_id, $amount, $account_id, $account_id]);

    $conn->commit();
    header("Location: ../index.php?account=" . $account_id . "&success=add_money");
} catch (Exception $e) {
    $conn->rollBack();
    header("Location: ../index.php?account=" . $account_id . "&error=add_money&message=" . urlencode($e->getMessage()));
}