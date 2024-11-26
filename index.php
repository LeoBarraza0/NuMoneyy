<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's accounts
$stmt = $conn->prepare("SELECT * FROM cuenta WHERE IdCliente_fk = ?");
$stmt->execute([$user_id]);
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selected_account = isset($_GET['account']) ? $_GET['account'] : $accounts[0]['NumeroCuenta'];

// Fetch the selected account details
$stmt = $conn->prepare("SELECT * FROM cuenta WHERE NumeroCuenta = ? AND IdCliente_fk = ?");
$stmt->execute([$selected_account, $user_id]);
$current_account = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NuMoney - Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="user-info">
                <img src="https://api.dicebear.com/6.x/initials/svg?seed=<?php echo $_SESSION['user_name']; ?>" alt="User Avatar" class="avatar">
                <div>
                    <h2><?php echo $_SESSION['user_name']; ?></h2>
                    <p><?php echo $current_account['TipoCuenta']; ?></p>
                </div>
            </div>
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </header>

        <main>
            <section id="accountBalance" class="card">
                <h3>Account Balance</h3>
                <select id="accountSelect" onchange="changeAccount(this.value)">
                    <?php foreach ($accounts as $account): ?>
                        <option value="<?php echo $account['NumeroCuenta']; ?>" <?php echo ($account['NumeroCuenta'] == $selected_account) ? 'selected' : ''; ?>>
                            <?php echo $account['TipoCuenta']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p id="balance" class="balance"><?php echo number_format($current_account['SaldoActual'], 2); ?> COP</p>
                <div class="button-group">
                    <button onclick="redirect('/deposito.php')" class="btn btn-primary">Add Money</button>
                    <button onclick="showWithdrawForm()" class="btn btn-primary">Withdraw</button>
                </div>
            </section>

            <nav class="tabs">
                <button class="tab-button active" data-tab="quickActions">Quick Actions</button>
                <button class="tab-button" data-tab="transactions" onclick="r">Transactions</button>
                <button class="tab-button" data-tab="services">Services</button>
            </nav>

            <section id="quickActions" class="tab-content active">
                <div class="grid">
                    <button onclick="showTransferForm()" class="action-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        Transfer
                    </button>
                    <button onclick="showPayBillsForm()" class="action-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                        Pay Bills
                    </button>
                    <button onclick="showRequestMoneyForm()" class="action-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        Request Money
                    </button>
                    <button onclick="showRechargeForm()" class="action-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-smartphone"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                        Recharge
                    </button>
                </div>
            </section>

            <section id="transactions" class="tab-content">
                <h3>Recent Transactions</h3>
                <div id="transactionsList"></div>
            </section>

            <section id="services" class="tab-content">
                <h3>Available Services</h3>
                <!-- Add service content here -->
            </section>
        </main>

        <!-- Add Money Form -->
        <div id="addMoneyForm" class="modal">
            <div class="modal-content">
                <h3>Add Money</h3>
                <form action="includes/add_money.php" method="post">
                    <input type="hidden" name="account_id" value="<?php echo $current_account['NumeroCuenta']; ?>">
                    <label for="add_money_type">Select Type:</label>
                    <select name="add_money_type" id="add_money_type" onchange="togglePaymentMethod()">
                        <option value="cajero">Recarga de cajero</option>
                        <option value="sucursal">Recarga de una sucursal</option>
                    </select>
                    <label for="add_money_amount">Amount:</label>
                    <input type="number" name="add_money_amount" id="add_money_amount" required>
                    <div id="payment_method_div" style="display: none;">
                        <label for="payment_method">Payment Method:</label>
                        <select name="payment_method" id="payment_method">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Money</button>
                </form>
            </div>
        </div>

        <!-- Withdraw Form -->
        <div id="withdrawForm" class="modal">
            <div class="modal-content">
                <h3>Withdraw Money</h3>
                <form action="includes/withdraw_money.php" method="post">
                    <input type="hidden" name="account_id" value="<?php echo $current_account['NumeroCuenta']; ?>">
                    <label for="withdraw_type">Select Type:</label>
                    <select name="withdraw_type" id="withdraw_type">
                        <option value="Sucursal">Sucursal</option>
                        <option value="Cajero">Cajero</option>
                    </select>
                    <label for="withdraw_amount">Amount:</label>
                    <input type="number" name="withdraw_amount" id="withdraw_amount" required>
                    <button type="submit" class="btn btn-primary">Withdraw Money</button>
                </form>
            </div>
        </div>

        <!-- Transfer Form -->
        <div id="transferForm" class="modal">
            <div class="modal-content">
                <h3>Transfer Money</h3>
                <form action="includes/transfer_money.php" method="post">
                    <input type="hidden" name="from_account_id" value="<?php echo $current_account['NumeroCuenta']; ?>">
                    <label for="to_account">To Account:</label>
                    <input type="text" name="to_account" id="to_account" required>
                    <label for="transfer_amount">Amount:</label>
                    <input type="number" name="transfer_amount" id="transfer_amount" required>
                    <button type="submit" class="btn btn-primary">Transfer Money</button>
                </form>
            </div>
        </div>

        <!-- Pay Bills Form -->
        <div id="payBillsForm" class="modal">
            <div class="modal-content">
                <h3>Pay Bills</h3>
                <form action="includes/pay_bills.php" method="post">
                    <input type="hidden" name="account_id" value="<?php echo $current_account['NumeroCuenta']; ?>">
                    <label for="bill_provider">Provider:</label>
                    <input type="text" name="bill_provider" id="bill_provider" required>
                    <label for="bill_amount">Amount:</label>
                    <input type="number" name="bill_amount" id="bill_amount" required>
                    <button type="submit" class="btn btn-primary">Pay Bill</button>
                </form>
            </div>
        </div>

        <!-- Request Money Form -->
        <div id="requestMoneyForm" class="modal">
            <div class="modal-content">
                <h3>Request Money</h3>
                <form action="includes/request_money.php" method="post">
                    <input type="hidden" name="to_account_id" value="<?php echo $current_account['NumeroCuenta']; ?>">
                    <label for="from_account">From Account:</label>
                    <input type="text" name="from_account" id="from_account" required>
                    <label for="request_amount">Amount:</label>
                    <input type="number" name="request_amount" id="request_amount" required>
                    <button type="submit" class="btn btn-primary">Request Money</button>
                </form>
            </div>
        </div>

        <!-- Recharge Form -->
        <div id="rechargeForm" class="modal">
            <div class="modal-content">
                <h3>Recharge</h3>
                <form action="includes/recharge.php" method="post">
                    <input type="hidden" name="account_id" value="<?php echo $current_account['NumeroCuenta']; ?>">
                    <label for="recharge_type">Type:</label>
                    <select name="recharge_type" id="recharge_type">
                        <option value="Telefonia">Telefonia</option>
                        <option value="Internet">Internet</option>
                    </select>
                    <label for="recharge_amount">Amount:</label>
                    <input type="number" name="recharge_amount" id="recharge_amount" required>
                    <button type="submit" class="btn btn-primary">Recharge</button>
                </form>
            </div>
        </div>
    </div>

    <script>document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const accountSelect = document.getElementById('account');
    const balanceElement = document.getElementById('balance');
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    const addMoneyBtn = document.getElementById('addMoneyBtn');
    const withdrawBtn = document.getElementById('withdrawBtn');
    const transferBtn = document.getElementById('transferBtn');
    const payBillsBtn = document.getElementById('payBillsBtn');
    const requestMoneyBtn = document.getElementById('requestMoneyBtn');
    const rechargeBtn = document.getElementById('rechargeBtn');

    // Modals
    const addMoneyForm = document.getElementById('addMoneyForm');
    const withdrawForm = document.getElementById('withdrawForm');
    const transferForm = document.getElementById('transferForm');
    const payBillsForm = document.getElementById('payBillsForm');
    const requestMoneyForm = document.getElementById('requestMoneyForm');
    const rechargeForm = document.getElementById('rechargeForm');

    // Event Listeners
    accountSelect.addEventListener('change', updateBalance);
    tabButtons.forEach(button => {
        button.addEventListener('click', () => switchTab(button.dataset.tab));
    });

    addMoneyBtn.addEventListener('click', () => showModal(addMoneyForm));
    withdrawBtn.addEventListener('click', () => showModal(withdrawForm));
    transferBtn.addEventListener('click', () => showModal(transferForm));
    payBillsBtn.addEventListener('click', () => showModal(payBillsForm));
    requestMoneyBtn.addEventListener('click', () => showModal(requestMoneyForm));
    rechargeBtn.addEventListener('click', () => showModal(rechargeForm));

    // Close modals when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    });

    // Functions
    function updateBalance() {
        const accountId = accountSelect.value;
        if (!accountId) return;

        fetch(`api/get_balance.php?account_id=${accountId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    balanceElement.textContent = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(data.balance);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function switchTab(tabId) {
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));

        document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }

    function showModal(modal) {
        modal.style.display = 'block';
    }

    function hideModal(modal) {
        modal.style.display = 'none';
    }

    function showAddMoneyForm() {
        showModal(addMoneyForm);
        hideModal(withdrawForm);
    }

    function showWithdrawForm() {
        showModal(withdrawForm);
        hideModal(addMoneyForm);
    }

    function togglePaymentMethod() {
        const addMoneyType = document.getElementById('add_money_type').value;
        const paymentMethodDiv = document.getElementById('payment_method_div');
        
        if (addMoneyType === 'sucursal') {
            paymentMethodDiv.style.display = 'block';
        } else {
            paymentMethodDiv.style.display = 'none';
        }
    }

    // Initialize
    if (accountSelect.value) {
        updateBalance();
    }

    // Add event listener for add money type change
    const addMoneyType = document.getElementById('add_money_type');
    if (addMoneyType) {
        addMoneyType.addEventListener('change', togglePaymentMethod);
    }

    // Add event listeners for form submissions
    const forms = [addMoneyForm, withdrawForm, transferForm, payBillsForm, requestMoneyForm, rechargeForm];
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const action = this.getAttribute('action');

            fetch(action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateBalance();
                    hideModal(this);
                    // You can add a success message here
                } else {
                    // Handle error, show error message
                    console.error('Error:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});</script>
</body>
</html>