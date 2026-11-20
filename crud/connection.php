
<?php

$host = "127.0.0.1";
$dbname = "barengin_db";
$username = "root";
$password = "";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $conn->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

    //echo "Connection Successful";

} catch (PDOException $e) {
    http_response_code(500);

    header("Content-Type: application/json");

    echo json_encode([
        "success" => false,
        "message" => "Koneksi database gagal"
    ]);

    exit;
}

?>
