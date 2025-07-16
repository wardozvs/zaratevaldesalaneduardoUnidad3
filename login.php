<?php require_once('db-connection.php') ?>
<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["firstname"] = $user["first_name"];
        $_SESSION["lastname"] = $user["last_name"];

        // Redirect based on role
        if ($_SESSION["role"] === "admin") {
            header("Location: admin-dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid email or password.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gray-50 flex items-center justify-center">

<div class="bg-white max-w-sm w-full rounded-lg shadow-md">
    <div class="p-6 border-b space-y-1">
        <h2 class="text-2xl font-bold">Login</h2>
        <p class="text-sm text-gray-600">Enter your email below to login to your account</p>
    </div>

    <div class="p-6">
        <form class="space-y-4" method="post">
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" placeholder="m@example.com" required
                       class="w-full border border-gray-300 rounded-md p-2"/>
            </div>

            <div class="space-y-2 relative">
                <div class="flex items-center">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <a href="#" class="ml-auto inline-block text-sm underline text-blue-600">Forgot your
                        password?</a>
                </div>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                           class="w-full border border-gray-300 rounded-md p-2 pr-10"/>
                    <button type="button" onclick="togglePassword()"
                            class="absolute bottom-0 right-0 h-full px-3 py-2 text-gray-500 hover:text-gray-700">
                        👁️
                    </button>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700 transition">Login
            </button>
        </form>

        <div class="mt-4 text-center text-sm">
            Don’t have an account? <a href="register.php" class="underline text-blue-600">Sign up</a>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById("password");
        input.type = input.type === "password" ? "text" : "password";
    }
</script>
</body>

</html>