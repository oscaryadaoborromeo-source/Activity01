
<?php
require_once "config.php";

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$mess = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $uname = trim($_POST['uname'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (empty($uname)) {
        $errors[] = "Username is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if ($password !== $password2) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE username = ?"
        );

        $check->bind_param("s", $uname);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $errors[] = "Username already exists.";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users (username, password, status, role)
                 VALUES (?, ?, 'pending', 'user')"
            );

            $stmt->bind_param(
                "ss",
                $uname,
                $hashedPassword
            );

            if ($stmt->execute()) {

                $mess = "Registered successfully! Please wait for admin approval of your account.";

            } else {

                $errors[] = "Something went wrong. Please try again later.";

            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | Inventory System</title>
</head>

<body>

    <h1>Inventory System</h1>
    <h2>Create your Account</h2>

    <br>

    <?php foreach ($errors as $e): ?>
        <p style="color: red;">
            <?= h($e) ?>
        </p>
    <?php endforeach; ?>

    <?php if (!empty($mess)): ?>
        <p style="color: blue;">
            <?= h($mess) ?>
        </p>
    <?php endif; ?>

    <form action="" method="POST">

        <label for="uname">Username:</label>
        <input
            type="text"
            name="uname"
            id="uname"
            placeholder="Enter username"
            required
            autofocus
        >

        <br><br>

        <label for="password">Password:</label>
        <input
            type="password"
            name="password"
            id="password"
            placeholder="Enter password"
            required
        >

        <br><br>

        <label for="password2">Re-enter Password:</label>
        <input
            type="password"
            name="password2"
            id="password2"
            placeholder="Re-enter password"
            required
        >

        <br><br>

        <input type="submit" value="Register">

    </form>

    <br><br>

    <p>
        Already have an account?
        <a href="index.php">Login</a>
    </p>

</body>
</html>
