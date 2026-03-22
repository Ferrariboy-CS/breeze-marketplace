<?php

class Database
{
    private $conn;

    public function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "marketplace";

        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    function validate($value)
    {
        $value = trim($value);
        $value = stripslashes($value);
        $value = htmlspecialchars($value);
        $value = mysqli_real_escape_string($this->conn, $value);
        return $value;
    }

    public function executeQuery($sql)
    {
        $result = $this->conn->query($sql);
        if ($result === false) {
            die("ERROR: " . $this->conn->error);
        }
        return $result;
    }

    public function select($table, $columns = "*", $condition = "")
    {
        $sql = "SELECT $columns FROM $table $condition";
        return $this->executeQuery($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function insert($table, $data)
    {
        $keys = implode(', ', array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $sql = "INSERT INTO $table ($keys) VALUES ($values)";
        return $this->executeQuery($sql);
    }

    public function update($table, $data, $condition = "")
    {
        $set = '';
        foreach ($data as $key => $value) {
            $set .= "$key = '$value', ";
        }
        $set = rtrim($set, ', ');
        $sql = "UPDATE $table SET $set $condition";
        return $this->executeQuery($sql);
    }

    public function delete($table, $condition = "")
    {
        $sql = "DELETE FROM $table $condition";
        return $this->executeQuery($sql);
    }

    public function hashPassword($password)
    {
        return hash_hmac('sha256', $password, 'iqbolshoh');
    }

    public function authenticate($username, $password, $table)
    {
        $username = $this->validate($username);
        $condition = "WHERE username = '" . $username . "' AND password = '" . $this->hashPassword($password) . "'";
        return $this->select($table, "*", $condition);
    }

    public function registerUser($name, $number, $email, $username, $password, $role)
    {
        $name = $this->validate($name);
        $number = $this->validate($number);
        $email = $this->validate($email);
        $username = $this->validate($username);

        $password_hash = $this->hashPassword($password);

        $data = array(
            'name' => $name,
            'number' => $number,
            'email' => $email,
            'username' => $username,
            'password' => $password_hash,
            'role' => $role,
            'status' => ($role === 'driver') ? 'pending' : 'active'
        );

        $user_id = $this->insert('accounts', $data);

        if ($user_id) {
            return $user_id;
        }
        return false;
    }

    function saveImagesToDatabase($files, $path, $productId)
    {
        if (is_array($files['tmp_name'])) {
            $uploaded_files = array();
            foreach ($files['tmp_name'] as $index => $tmp_name) {
                $file_name = $files['name'][$index];
                $file_info = pathinfo($file_name);
                $file_extension = $file_info['extension'];
                $new_file_name = md5($tmp_name . date("Y-m-d_H-i-s") . rand(1, 9999999) . $productId) . "." . $file_extension;
                if (move_uploaded_file($tmp_name, $path . $new_file_name)) {
                    $uploaded_files[] = $new_file_name;
                    $this->insert('product_images', array('product_id' => $productId, 'image_url' => $new_file_name));
                }
            }
            return $uploaded_files;
        } else {
            $file_name = $files['name'];
            $file_tmp = $files['tmp_name'];

            $file_info = pathinfo($file_name);
            $file_format = $file_info['extension'];

            $new_file_name = md5($file_tmp . date("Y-m-d_H-i-s") . rand(1, 9999999) . $productId) . "." . $file_format;

            if (move_uploaded_file($file_tmp, $path . $new_file_name)) {
                $this->insert('product_images', array('product_id' => $productId, 'image_url' => $new_file_name));
                return $new_file_name;
            }
            return false;
        }
    }

    function saveImage($file, $path)
    {
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];

        $file_info = pathinfo($file_name);
        $file_format = $file_info['extension'];

        $new_file_name = md5($file_tmp . date("Y-m-d_H-i-s")) . rand(1, 9999999) . "." . $file_format;

        if (move_uploaded_file($file_tmp, $path . $new_file_name)) {
            return $new_file_name;
        }
        return false;
    }

    public function getCategories()
    {
        $categories = array();
        $result = $this->select('categories', 'id, category_name');
        foreach ($result as $row) {
            $categories[$row['id']] = $row['category_name'];
        }
        return $categories;
    }

    public function getProduct($product_id)
    {
        $result = $this->select('products', '*', 'WHERE id = ' . $product_id);
        return $result[0];
    }

    public function getProductImageID($product_id)
    {
        $images = $this->select('product_images', 'id', 'WHERE product_id = ' . $product_id);
        $id = array();
        foreach ($images as $image) {
            $id[] = $image['id'];
        }
        return $id;
    }


    function getProductImage($id)
    {
        global $query;
        $result = $this->select('product_images', 'image_url', 'WHERE id = ' . $id);
        return $result[0]['image_url'];
    }

    public function getCartItems($user_id)
    {
        $sql = "SELECT 
        p.id,
        p.name,
        p.price_current,
        p.price_old,
        c.number_of_products,
        (p.price_current * c.number_of_products) AS total_price
    FROM 
        cart c
    JOIN
        products p ON c.product_id = p.id
    WHERE 
        c.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cartItems = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $cartItems;
    }

    public function getProductImages($product_id)
    {
        $sql = "SELECT image_url FROM product_images WHERE product_id = $product_id";
        $result = $this->executeQuery($sql)->fetch_all(MYSQLI_ASSOC);
        $imageUrls = array();
        foreach ($result as $row) {
            $imageUrls[] = $row['image_url'];
        }
        return $imageUrls;
    }

    public function getWishes($user_id)
    {
        $sql = "SELECT 
        p.name,
        p.price_current,
        p.price_old,
        w.product_id
    FROM 
        wishes w
    JOIN
        products p ON w.product_id = p.id
    WHERE 
        w.user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();

        $wishes = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $wishes;
    }

    public function lastInsertId($table, $data)
    {
        $keys = implode(', ', array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $sql = "INSERT INTO $table ($keys) VALUES ($values)";
        $insert_result = $this->executeQuery($sql);

        if ($insert_result) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    public function findDriverByArea($area)
    {
        $stmt = $this->conn->prepare("SELECT a.id FROM driver_profiles dp JOIN accounts a ON dp.account_id = a.id WHERE dp.status = 'active' AND a.status = 'active' AND a.role = 'driver' AND dp.area = ? LIMIT 1");
        $stmt->bind_param('s', $area);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result ? $result['id'] : null;
    }

    public function createDelivery($orderId, $driverId, $status = 'awaiting_driver')
    {
        $data = array(
            'order_id' => $orderId,
            'driver_id' => $driverId,
            'status' => $status,
            'assigned_at' => ($driverId && $status !== 'awaiting_driver') ? date('Y-m-d H:i:s') : null
        );

        $columns = array();
        $values = array();
        foreach ($data as $key => $value) {
            if ($value === null) {
                $columns[] = $key;
                $values[] = 'NULL';
            } else {
                $columns[] = $key;
                $values[] = "'" . $this->conn->real_escape_string($value) . "'";
            }
        }

        $sql = "INSERT INTO deliveries (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
        return $this->executeQuery($sql);
    }

    public function getDeliveriesByDriver($driverId)
    {
        $stmt = $this->conn->prepare("SELECT d.id AS delivery_id, d.status, d.assigned_at, d.picked_at, d.delivered_at, o.id AS order_id, o.address, o.area, o.total_current, o.total_old, o.status AS order_status, o.created_at FROM deliveries d JOIN orders o ON d.order_id = o.id WHERE d.driver_id = ? ORDER BY d.updated_at DESC");
        $stmt->bind_param('i', $driverId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    public function getDeliveryForDriver($deliveryId, $driverId)
    {
        $stmt = $this->conn->prepare("SELECT d.*, o.user_id FROM deliveries d JOIN orders o ON d.order_id = o.id WHERE d.id = ? AND d.driver_id = ?");
        $stmt->bind_param('ii', $deliveryId, $driverId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result;
    }

    public function updateDeliveryStatus($deliveryId, $driverId, $status)
    {
        $validStatuses = ['accepted', 'picked_up', 'en_route', 'delivered', 'failed'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $timestamps = [
            'accepted' => 'assigned_at',
            'picked_up' => 'picked_at',
            'en_route' => null,
            'delivered' => 'delivered_at',
            'failed' => null
        ];

        $timeColumn = $timestamps[$status];
        $timeValue = $timeColumn ? date('Y-m-d H:i:s') : null;

        $setParts = ["status = '" . $this->conn->real_escape_string($status) . "'"];
        if ($timeColumn) {
            $setParts[] = "$timeColumn = '" . $this->conn->real_escape_string($timeValue) . "'";
        }

        $setClause = implode(', ', $setParts);

        $stmt = $this->conn->prepare("UPDATE deliveries SET $setClause WHERE id = ? AND driver_id = ?");
        $stmt->bind_param('ii', $deliveryId, $driverId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            $orderStatusMap = [
                'accepted' => 'assigned',
                'picked_up' => 'assigned',
                'en_route' => 'assigned',
                'delivered' => 'delivered',
                'failed' => 'failed'
            ];
            $orderStatus = isset($orderStatusMap[$status]) ? $orderStatusMap[$status] : 'assigned';
            $this->executeQuery("UPDATE orders o JOIN deliveries d ON o.id = d.order_id SET o.status = '" . $this->conn->real_escape_string($orderStatus) . "' WHERE d.id = " . (int) $deliveryId);
        }

        return $result;
    }

    public function clearCart($userId)
    {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->close();
    }
}
