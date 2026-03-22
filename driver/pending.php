<?php
include './check.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Pending Approval</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="mb-3">Waiting for Approval</h3>
                        <p class="text-muted">Your driver account is pending admin approval. You will gain access once approved.</p>
                        <a href="../logout/" class="btn btn-outline-secondary">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
