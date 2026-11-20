
<?php

header("Content-Type: application/json");

require_once "connection.php";

try {

    // Memastikan request menggunakan DELETE
    if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
        http_response_code(405);

        echo json_encode([
            "success" => false,
            "message" => "Method tidak diizinkan. Gunakan DELETE."
        ]);

        exit;
    }

    // Mengambil ID dari URL
    $id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

    // Validasi ID
    if ($id <= 0) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "ID aktivitas wajib diisi"
        ]);

        exit;
    }

    // Memeriksa apakah aktivitas tersedia
    $check = $conn->prepare(
        "SELECT id, title FROM activities WHERE id = :id"
    );

    $check->execute(["id" => $id]);

    $activity = $check->fetch();

    if (!$activity) {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Aktivitas tidak ditemukan"
        ]);

        exit;
    }

    // Menghapus aktivitas
    $sql = "DELETE FROM activities WHERE id = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute(["id" => $id]);

    echo json_encode([
        "success" => true,
        "message" => "Aktivitas berhasil dihapus",
        "data" => [
            "id" => $id,
            "title" => $activity["title"]
        ]
    ], JSON_PRETTY_PRINT);
} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Gagal menghapus aktivitas. Periksa relasi data."
    ]);
}

?>
