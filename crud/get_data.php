
<?php

header("Content-Type: application/json");

require_once "connection.php";

try {

    // Mengambil ID jika tersedia
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if ($id !== null) {

        // Mengambil satu aktivitas berdasarkan ID
        $sql = "SELECT * FROM activities WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        $data = $stmt->fetch();

        if (!$data) {
            http_response_code(404);

            echo json_encode([
                "success" => false,
                "message" => "Data aktivitas tidak ditemukan"
            ]);
            exit;
        }

    } else {

        // Mengambil seluruh aktivitas
        $sql = "SELECT * FROM activities ORDER BY id ASC";
        $stmt = $conn->query($sql);

        $data = $stmt->fetchAll();

    }

    echo json_encode([
        "success" => true,
        "message" => "Data aktivitas berhasil dimuat",
        "data" => $data
    ], JSON_PRETTY_PRINT);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Terjadi kesalahan saat mengambil data"
    ]);

}

?>
