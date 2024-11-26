document.addEventListener('DOMContentLoaded', function() {
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
});