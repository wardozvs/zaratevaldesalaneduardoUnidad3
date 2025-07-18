<?php
require_once('db-connection.php');
session_start();

$token = $_GET['token'] ?? '';
$valid_token = false;
$email = '';
$error = '';
$success = '';

if ($token) {
    // Se verifica el token en la base de datos
    $sql = "SELECT email FROM password_resets WHERE reset_code = ? AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $valid_token = true;
        $email = $result->fetch_assoc()['email'];
    }
    $stmt->close();
}

// Manejar solicitud de restablecimiento de contraseña (cuando no se proporciona token)
if ($_SERVER["REQUEST_METHOD"] === "POST" && !$token) {
    $request_email = $_POST["email"] ?? '';
    
    if (empty($request_email)) {
        $error = "Por favor, ingresa tu email.";
    } elseif (!filter_var($request_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Por favor, ingresa un email válido.";
    } else {
        // Verificar si el email existe en la tabla de usuarios
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $request_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Generar token de restablecimiento
            $reset_token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Eliminar cualquier token de restablecimiento existente para este email
            $sql = "DELETE FROM password_resets WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $request_email);
            $stmt->execute();
            
            // Insertar nuevo token de restablecimiento
            $sql = "INSERT INTO password_resets (email, reset_code, expires_at) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $request_email, $reset_token, $expires_at);
            
            if ($stmt->execute()) {
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?token=" . $reset_token;
                $success = "Se ha generado un enlace de restablecimiento. </strong><br>
                           <a href='" . $reset_link . "' class='text-blue-600 underline'>" . $reset_link . "</a>";
            } else {
                $error = "Error al generar el enlace de restablecimiento. Por favor, inténtalo de nuevo.";
            }
        } else {
            // Mostrar mensaje de éxito incluso si el email no existe (mejor práctica de seguridad)
            $success = "Si el email existe en nuestro sistema, recibirás un enlace de restablecimiento.";
        }
        $stmt->close();
    }
}

// Manejar restablecimiento de contraseña (cuando se proporciona token)
if ($_SERVER["REQUEST_METHOD"] === "POST" && $valid_token) {
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    
    if ($new_password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($new_password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Se actualiza la contraseña en la base de datos
        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $email);
        
        if ($stmt->execute()) {
            // Se elimina el token utilizado
            $sql = "DELETE FROM password_resets WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            $success = "La contraseña ha sido actualizada exitosamente.";
        } else {
            $error = "Error al restablecer la contraseña. Por favor, inténtalo de nuevo.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Restablecer Contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
<div class="bg-white max-w-sm w-full rounded-lg shadow-md">
    <div class="p-6 border-b space-y-1">
        <h2 class="text-2xl font-bold">Restablecer Contraseña</h2>
        <p class="text-sm text-gray-600">
            <?php if ($token && $valid_token): ?>
                Ingresa tu nueva contraseña a continuación
            <?php elseif ($token && !$valid_token): ?>
                Enlace de restablecimiento inválido o expirado
            <?php else: ?>
                Ingresa tu email para recibir un enlace de restablecimiento
            <?php endif; ?>
        </p>
    </div>
    <div class="p-6">
        <?php if ($token && !$valid_token): ?>
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                Este enlace de restablecimiento de contraseña es inválido o ha expirado. Por favor, solicita uno nuevo.
            </div>
            <div class="text-center">
                <a href="forgot-password.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Solicitar Nuevo Enlace de Restablecimiento
                </a>
            </div>
        <?php elseif ($token && $valid_token): ?>
            <?php if (!empty($error)): ?>
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    <?php echo $success; ?>
                </div>
                <div class="text-center">
                    <a href="login.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        Ir a Login
                    </a>
                </div>
            <?php else: ?>
                <form class="space-y-4" method="post">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="space-y-2">
                        <label for="new_password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                        <div class="relative">
                            <input type="password" id="new_password" name="new_password" required
                                   class="w-full border border-gray-300 rounded-md p-2 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                            <button type="button" onclick="togglePassword('new_password')"
                                    class="absolute bottom-0 right-0 h-full px-3 py-2 text-gray-500 hover:text-gray-700">
                                👁️
                            </button>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                        <div class="relative">
                            <input type="password" id="confirm_password" name="confirm_password" required
                                   class="w-full border border-gray-300 rounded-md p-2 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                            <button type="button" onclick="togglePassword('confirm_password')"
                                    class="absolute bottom-0 right-0 h-full px-3 py-2 text-gray-500 hover:text-gray-700">
                                👁️
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit"
                            class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700 transition duration-200">
                        Restablecer Contraseña
                    </button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <!-- Formulario de solicitud de email (cuando no se proporciona token) -->
            <?php if (!empty($error)): ?>
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($success)): ?>
                <form class="space-y-4" method="post">
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" required
                               class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ingresa tu email"/>
                    </div>
                    
                    <button type="submit"
                            class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700 transition duration-200">
                        Enviar Enlace de Restablecimiento
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="mt-4 text-center text-sm">
            <a href="login.php" class="underline text-blue-600 hover:text-blue-800">Volver a Login</a>
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        input.type = input.type === "password" ? "text" : "password";
    }
</script>
</body>
</html>
