<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Server Time</title>
    <!-- Add Bootstrap CSS link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Server Time</h2>

        <div class="alert alert-info" role="alert">
            <?php
            // Set the server timezone to Asia/Kuala_Lumpur or your desired timezone
            date_default_timezone_set('Asia/Kuala_Lumpur');

            // Get the current server time in the format "Y-m-d H:i:s"
            $currentDateTime = date('Y-m-d H:i:s');
            echo "Server Time: $currentDateTime";
            ?>
        </div>

        <div class="d-grid gap-2 mt-3">
            <a href="login.html" class="btn btn-primary">Go Back</a>
        </div>
    </div>
    <!-- Add Bootstrap JavaScript link -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
