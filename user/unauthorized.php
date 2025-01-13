<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Unauthorized Access</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            max-width: 500px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .error-icon {
            font-size: 50px;
            color: #dc3545;
        }
    </style>
</head>
<body>

<div class="container">
    <i class="fas fa-exclamation-triangle error-icon"></i>
    <h2 class="mt-3 text-danger">Unauthorized Access</h2>
    <p class="lead">You do not have permission to access this page.</p>
    
    <!-- Check if a session message exists -->
    <?php if (isset($_SESSION['auth_error'])): ?>
        <div class="alert alert-warning">
            <?= htmlspecialchars($_SESSION['auth_error']); ?>
        </div>
        <?php unset($_SESSION['auth_error']); ?> <!-- Clear message after displaying -->
    <?php endif; ?>

    <div class="d-grid gap-2 mt-4">
        <a href="login.php" class="btn btn-primary">Log In</a>
        <a href="index.php" class="btn btn-outline-secondary">Return to Home</a>
    </div>
</div>

</body>
</html>
<?php
// Close the database connection
mysqli_close($con);
?>