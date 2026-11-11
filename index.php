
<?php

require_once "config.php";

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $uname = trim($_POST['uname'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($uname) || empty($password)) {

        $errors[] = "Please enter your username and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT * FROM users WHERE username = ? LIMIT 1"
        );

        $stmt->bind_param("s", $uname);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== "approved") {

                $errors[] = "Your account has not been approved by the admin.";

            } else {

                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit;

            }

        } else {

            $errors[] = "Invalid username or password.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Inventory System</title>
</head>
<body>
    <h1>Inventory System</h1>
    <h2>Sign In To Your Account</h2>
    <br>

    <?php foreach ($errors as $e): ?>
        <p style="color: red;">
            <?= h($e) ?>
        </p>

    <?php endforeach; ?>

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

        <input type="submit" value="Sign In">

    </form>
    <br><br>
    <p>
        No Account?
        <a href="register.php">Register</a>
    </p>

</body>
</html>
