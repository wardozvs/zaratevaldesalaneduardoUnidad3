<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Deposit</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50">

  <header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex items-center py-4">
        <a href="dashboard.html" class="flex items-center text-sm border border-gray-300 rounded-md px-3 py-1 hover:bg-gray-100 mr-4">← Back to Dashboard</a>
        <h1 class="text-2xl font-bold text-gray-900">Deposit Funds</h1>
      </div>
    </div>
  </header>

  <main class="max-w-md mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-lg font-semibold mb-2">💳 Add Money to Account</h2>
      <p class="text-sm text-gray-600 mb-4">Enter the amount you want to deposit using Stripe</p>

      <form onsubmit="return simulateDeposit(event)" class="space-y-6">
        <div class="space-y-2">
          <label for="amount" class="block text-sm font-medium">Amount (USD)</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
            <input type="number" id="amount" step="0.01" min="1" max="10000" required class="w-full border border-gray-300 rounded-md p-2 pl-8" placeholder="0.00">
          </div>
        </div>

        <div class="space-y-4 p-4 border rounded-lg bg-gray-50">
          <h3 class="font-medium text-sm">Payment Information</h3>
          <div class="space-y-3">
            <div>
              <label for="card-number" class="text-xs block">Card Number</label>
              <input id="card-number" placeholder="1234 1234 1234 1234" class="w-full border rounded p-2 text-sm"/>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label for="expiry" class="text-xs block">MM/YY</label>
                <input id="expiry" placeholder="12/34" class="w-full border rounded p-2 text-sm"/>
              </div>
              <div>
                <label for="cvc" class="text-xs block">CVC</label>
                <input id="cvc" placeholder="123" class="w-full border rounded p-2 text-sm"/>
              </div>
            </div>
          </div>
        </div>

        <button type="submit" id="deposit-btn" class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700 transition">
          Deposit
        </button>
      </form>

      <div class="mt-4 text-xs text-gray-500 text-center">
        Powered by Stripe. Your payment information is secure.
      </div>
    </div>
  </main>

  <script>
    function simulateDeposit(e) {
      e.preventDefault();
      const btn = document.getElementById("deposit-btn");
      const amount = parseFloat(document.getElementById("amount").value);

      if (!amount || amount <= 0) {
        alert("Please enter a valid amount.");
        return false;
      }

      btn.disabled = true;
      btn.innerText = "Processing...";

      setTimeout(() => {
        btn.disabled = false;
        btn.innerText = `Deposit $${amount.toFixed(2)}`;
        alert(`Deposit of $${amount.toFixed(2)} processed successfully!`);
      }, 2000);
    }
  </script>
</body>
</html>
