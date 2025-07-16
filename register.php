<?php require_once('db-connection.php') ?>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $firstName = $_POST["first_name"];
    $lastName = $_POST["last_name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $role = 'user';

    $sql = "INSERT INTO users (username, first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $firstName, $lastName, $email, $password, $role);

    if ($stmt->execute()) {
        $success = "Account created successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gray-50 flex items-center justify-center">

<div class="bg-white max-w-sm w-full rounded-lg shadow-md">
    <div class="p-6 border-b space-y-1">
        <h2 class="text-xl font-bold">Sign Up</h2>
        <p class="text-sm text-gray-600">Enter your information to create an account</p>
    </div>

    <div class="p-6">
        <form method="POST" class="space-y-4">
            <div class="space-y-2">
                <label for="username" class="block text-sm font-medium">Username</label>
                <input name="username" required class="w-full border border-gray-300 rounded-md p-2"/>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="first_name" class="block text-sm font-medium">First name</label>
                    <input name="first_name" required class="w-full border border-gray-300 rounded-md p-2"/>
                </div>
                <div class="space-y-2">
                    <label for="last_name" class="block text-sm font-medium">Last name</label>
                    <input name="last_name" required class="w-full border border-gray-300 rounded-md p-2"/>
                </div>
            </div>

            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium">Email</label>
                <input type="email" name="email" required class="w-full border border-gray-300 rounded-md p-2"/>
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium">Password</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded-md p-2"/>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700 transition">
                Create an account
            </button>
        </form>


        <div class="mt-4 text-center text-sm">
            Already have an account? <a href="login.php" class="underline text-blue-600">Sign in</a>
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