<?php include 'check.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="icon" href="../src/images/Favicon.png">
    <title>AdminLTE 3 | Sellers</title>
    <?php include 'includes/css.php'; ?>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php include 'includes/navbar.php'; ?>

        <?php
        include 'includes/aside.php';
        active('users', 'sellers');
        ?>

        <div class="content-wrapper">

            <?php
            $arr = array(
                ["title" => "Home", "url" => "/"],
                ["title" => "Sellers", "url" => "#"],
            );
            pagePath('Sellers', $arr);
            ?>

            <?php
            $stats = $query->executeQuery("SELECT 
                (SELECT COUNT(*) FROM accounts WHERE role='seller') AS sellers,
                (SELECT COUNT(*) FROM accounts WHERE role='user') AS users,
                (SELECT COUNT(*) FROM accounts WHERE role='driver') AS drivers,
                (SELECT COUNT(*) FROM products) AS products,
                (SELECT COUNT(*) FROM categories) AS categories,
                (SELECT COUNT(*) FROM orders) AS orders_total,
                (SELECT SUM(total_current) FROM orders) AS revenue_total,
                (SELECT COUNT(*) FROM orders WHERE status='awaiting_driver' OR status='assigned') AS orders_in_delivery,
                (SELECT COUNT(*) FROM orders WHERE status='delivered') AS orders_delivered
            ")->fetch_assoc();
            ?>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?= (int)$stats['sellers']; ?></h3>
                                    <p>Sellers</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <span class="small-box-footer" style="cursor:default;">&nbsp;</span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?= (int)$stats['users']; ?></h3>
                                    <p>Users</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person"></i>
                                </div>
                                <span class="small-box-footer" style="cursor:default;">&nbsp;</span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?= (int)$stats['orders_total']; ?></h3>
                                    <p>Total Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-clipboard"></i>
                                </div>
                                <a href="./users.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>N$<?= number_format((float)$stats['revenue_total'], 2); ?></h3>
                                    <p>Total Revenue</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-social-usd"></i>
                                </div>
                                <a href="./users.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-secondary">
                                <div class="inner">
                                    <h3><?= (int)$stats['drivers']; ?></h3>
                                    <p>Drivers</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-android-car"></i>
                                </div>
                                <a href="./drivers.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?= (int)$stats['products']; ?></h3>
                                    <p>Products</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-cube"></i>
                                </div>
                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?= (int)$stats['orders_delivered']; ?></h3>
                                    <p>Delivered Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-checkmark"></i>
                                </div>
                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?= (int)$stats['orders_in_delivery']; ?></h3>
                                    <p>Out for Delivery</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-android-bus"></i>
                                </div>
                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Seller List</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Number</th>
                                            <th>Email(s)</th>
                                            <th>Username</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $data = $query->select('accounts', 'id, name, number, email, username, status', "where role = 'seller'");

                                        foreach ($data as $row) {
                                            echo '<tr>';
                                            echo '<td>' . $row['name'] . '</td>';
                                            echo '<td>' . $row['number'] . '</td>';
                                            echo '<td>' . $row['email'] . '</td>';
                                            echo '<td>' . $row['username'] . '</td>';
                                            echo '<td>';
                                            if ($row['status'] == 'active') {
                                                echo '<button class="btn btn-success" onclick="changeStatus(' . $row['id'] . ', \'blocked\')">Active</button>';
                                            } else {
                                                echo '<button class="btn btn-danger" onclick="changeStatus(' . $row['id'] . ', \'active\')">Blocked</button>';
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
                                            <th>Email(s)</th>
                                            <th>Username</th>
                                            <th>Status</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
            </section>
        </div>

        <!-- Main Footer -->
        <?php include 'includes/footer.php'; ?>
    </div>

    <!-- SCRIPTS -->
    <script src="../src/js/jquery.min.js"></script>
    <script src="../src/js/adminlte.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../src/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
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
    </script>

    <script>
        function changeStatus(userId, newStatus) {
            window.location.href = "change_status.php?userId=" + userId + "&newStatus=" + newStatus + "&userrole=user";
        }
    </script>

</body>

</html>