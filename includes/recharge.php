<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['account_id']) || !isset($_POST['recharge_type']) || !isset($_POST['recharge_amount'])) {
    header("Location: ../index.php");
    exit();
}

$account_id = $_POST['account_id'];
$recharge_type = $_POST['recharge_type'];
$amount = $_POST['recharge_amount'];

try {
    $conn->beginTransaction();

    // Check if the account has enough balance
    $stmt = $conn->prepare("SELECT SaldoActual FROM cuenta WHERE IdCuenta = ? AND IdCliente_fk = ?");
    $stmt->execute([$account_id, $_SESSION['user_id']]);
    $account_balance = $stmt->fetchColumn();

    if ($account_balance < $amount) {
        throw new Exception("Insufficient funds");
    }

    // Update account balance
    $stmt = $conn->prepare("UPDATE cuenta SET SaldoActual = SaldoActual - ? WHERE IdCuenta = ?");
    $stmt->execute([$amount, $account_id]);

    // Create transaction
    $stmt = $conn->prepare("INSERT INTO transaccion (IdCuentaOrigen_fk, IdCuentaDestino_fk, TipoTransaccion, MontoAplica, Metodo, Descripcion) VALUES (?, NULL, 'Recarga', ?, 'App', ?)");
    $stmt->execute([$account_id, $amount, "Recarga de " . $recharge_type]);
    $transaction_id = $conn->lastInsertId();

    // Create servicio
    $stmt = $conn->prepare("INSERT INTO servicio (IdCliente_fk, NombreServicio, Monto, FechaHora, IdTipoS_fk, IdProveedor_fk) VALUES (?, ?, ?, NOW(), ?, ?)");
    $stmt->execute([$_SESSION['user_id'], "Recarga de " . $recharge_type, $amount, 2, 2]); // Assuming IdTipoS_fk and IdProveedor_fk are 2 for recharges
    $servicio_id = $conn->lastInsertId();

    // Create movimientocuenta
    $stmt = $conn->prepare("INSERT INTO movimientocuenta (IdCuenta_fk, IdTransaccion_fk, TipoMovimiento, SaldoPrevio, SaldoPosterior) VALUES (?, ?, 'Debito', ?, ?)");
    $stmt->execute([$account_id, $transaction_id, $account_balance, $account_balance - $amount]);

    $conn->commit();
    header("Location: ../index.php?account=" . $account_id . "&success=recharge");
} catch (Exception $e) {
    $conn->rollBack();
    header("Location: ../index.php?account=" . $account_id . "&error=recharge&message=" . urlencode($e->getMessage()));
}