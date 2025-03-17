

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Canceled</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- For icons -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .header {
            font-size: 2.5rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .order-info {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 20px;
        }
        .btn {
            padding: 12px 25px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #c82333;
        }
        .footer {
            font-size: 1rem;
            margin-top: 40px;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="icon">
        <i class="fas fa-times-circle"></i>
    </div>
    <div class="header">
        OOPPPSSS!
    </div>
    <div class="order-info">
        <p>Something went wrong.</p>
        <p>If you have any questions, please contact support.</p>
        <p>We hope to see you again soon!</p>
    </div>
    <a href="index.php" class="btn">Go to Homepage</a>
    <a href="cart.php" class="btn" style="margin-left: 10px;">Return to Cart</a>
</div>

</body>
</html>
