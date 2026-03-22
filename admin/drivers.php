<?php include 'check.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="icon" href="../src/images/Favicon.png">
    <title>AdminLTE 3 | Drivers</title>
    <?php include 'includes/css.php'; ?>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php include 'includes/navbar.php'; ?>

        <?php
        include 'includes/aside.php';
        active('drivers', 'drivers');
        ?>

        <div class="content-wrapper">
            <?php
            $arr = array(
                ["title" => "Home", "url" => "/"],
                ["title" => "Drivers", "url" => "#"],
            );
            pagePath('Drivers', $arr);
            ?>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php print_r($query->executeQuery('SELECT * FROM accounts WHERE role = "driver"')->num_rows) ?></h3>
                                    <p>Drivers</p>
                                </div>
                                <div class="icon"><i class="ion ion-android-car"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Driver List</h3>
                                </div>
                                <div class="card-body">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Number</th>
                                                <th>Email</th>
                                                <th>Username</th>
                                                <th>Area</th>
                                                <th>Vehicle</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $drivers = $query->executeQuery('SELECT a.id, a.name, a.number, a.email, a.username, a.status AS account_status, dp.area, dp.vehicle, dp.status AS profile_status FROM accounts a JOIN driver_profiles dp ON a.id = dp.account_id');
                                            foreach ($drivers as $row) {
                                                echo '<tr>';
                                                echo '<td>' . $row['name'] . '</td>';
                                                echo '<td>' . $row['number'] . '</td>';
                                                echo '<td>' . $row['email'] . '</td>';
                                                echo '<td>' . $row['username'] . '</td>';
                                                echo '<td>' . $row['area'] . '</td>';
                                                echo '<td>' . ($row['vehicle'] ?? '-') . '</td>';
                                                echo '<td>';
                                                if ($row['profile_status'] === 'active') {
                                                    echo '<button class="btn btn-success" onclick="changeStatus(' . $row['id'] . ', \'blocked\', \'driver\')">Active</button>';
                                                } elseif ($row['profile_status'] === 'pending') {
                                                    echo '<button class="btn btn-warning" onclick="changeStatus(' . $row['id'] . ', \'active\', \'driver\')">Approve</button>';
                                                    echo ' <button class="btn btn-danger" onclick="changeStatus(' . $row['id'] . ', \'blocked\', \'driver\')">Block</button>';
                                                } else {
                                                    echo '<button class="btn btn-danger" onclick="changeStatus(' . $row['id'] . ', \'active\', \'driver\')">Blocked</button>';
                                                }
                                                echo '</td>';
                                                echo '</tr>';
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Name</th>
                                                <th>Number</th>
                                                <th>Email</th>
                                                <th>Username</th>
                                                <th>Area</th>
                                                <th>Vehicle</th>
                                                <th>Status</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include 'includes/footer.php'; ?>
    </div>

    <script src="../src/js/jquery.min.js"></script>
    <script src="../src/js/adminlte.js"></script>
    <script src="../src/js/bootstrap.bundle.min.js"></script>
    <script src="../src/js/jquery.dataTables.min.js"></script>
    <script src="../src/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(function () {
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });

        function changeStatus(userId, newStatus, role) {
            window.location.href = "change_status.php?userId=" + userId + "&newStatus=" + newStatus + "&userrole=" + role;
        }
    </script>
</body>

</html>
