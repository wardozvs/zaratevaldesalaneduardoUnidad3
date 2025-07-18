<?php 
session_start();

if ($_SESSION["role"] !== "admin") {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50">

<header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-700"><?php echo htmlspecialchars($_SESSION["firstname"] . ' ' . $_SESSION["lastname"]); ?></span>
                </div>
                <button class="text-sm border border-gray-300 rounded-md px-3 py-1 hover:bg-gray-100">Logout</button>
            </div>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

        <div class="bg-white rounded-lg shadow-md p-6 md:col-span-2">
            <h2 class="text-xl font-semibold">Welcome back<?php echo htmlspecialchars($_SESSION["firstname"] . ' ' . $_SESSION["lastname"]); ?></h2>
            <p class="text-sm text-gray-600">Here's an overview of your account</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium">Available Balance</h3>
            </div>
            <div class="text-2xl font-bold">$1250.75</div>
            <p class="text-xs text-gray-500">Current account balance</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 md:col-span-2 lg:col-span-3">
            <h3 class="text-lg font-semibold mb-1">Deposit Money</h3>
            <p class="text-sm text-gray-600 mb-4">Add funds to your account using Stripe</p>
            <a href="deposit.html"
               class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                ➕ Deposit Funds
            </a>
        </div>

    </div>
</main>
</body>
</html>
