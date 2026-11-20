
<?php

header("Content-Type: application/json");

require_once "connection.php";

try {

    // Mengambil data JSON dari request
    $input = json_decode(file_get_contents("php://input"), true);


    // UPDATE jika ID dikirimkan
    if (isset($input["id"])) {

        $id = (int) $input["id"];

        // Kolom yang boleh diperbarui
        $allowedFields = [
            "title",
            "description",
            "capacity",
            "budget_amount"
        ];

        $updates = [];
        $params = ["id" => $id];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $input)) {
                $updates[] = "$field = :$field";
                $params[$field] = $input[$field];
            }
        }

        if (empty($updates)) {
            http_response_code(400);

            echo json_encode([
                "success" => false,
                "message" => "Tidak ada data yang diperbarui"
            ]);

            exit;
        }

        $updates[] = "updated_at = NOW()";

        $sql = "UPDATE activities SET "
            . implode(", ", $updates)
            . " WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() === 0) {
            $check = $conn->prepare(
                "SELECT id FROM activities WHERE id = :id"
            );
            $check->execute(["id" => $id]);

            if (!$check->fetch()) {
                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "message" => "Aktivitas tidak ditemukan"
                ]);

                exit;
            }
        }

        echo json_encode([
            "success" => true,
            "message" => "Aktivitas berhasil diperbarui",
            "data" => [
                "id" => $id,
                "updated_fields" => array_keys(
                    array_intersect_key(
                        $input,
                        array_flip($allowedFields)
                    )
                )
            ]
        ], JSON_PRETTY_PRINT);

        exit;
    }


    // Validasi input
    $required = [
        "host_id",
        "category_id",
        "title",
        "description",
        "participant_level",
        "starts_at",
        "ends_at",
        "venue_name",
        "address",
        "city",
        "capacity"
    ];

    foreach ($required as $field) {
        if (!isset($input[$field]) || $input[$field] === "") {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Field $field wajib diisi"
            ]);
            exit;
        }
    }

    // Query INSERT
    $sql = "INSERT INTO activities (
        host_id,
        category_id,
        title,
        description,
        participant_level,
        starts_at,
        ends_at,
        timezone,
        venue_name,
        address,
        area,
        city,
        province,
        country_code,
        latitude,
        longitude,
        capacity,
        budget_amount,
        currency,
        status,
        created_at,
        updated_at
    ) VALUES (
        :host_id,
        :category_id,
        :title,
        :description,
        :participant_level,
        :starts_at,
        :ends_at,
        :timezone,
        :venue_name,
        :address,
        :area,
        :city,
        :province,
        :country_code,
        :latitude,
        :longitude,
        :capacity,
        :budget_amount,
        :currency,
        'draft',
        NOW(),
        NOW()
    )";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        "host_id" => $input["host_id"],
        "category_id" => $input["category_id"],
        "title" => $input["title"],
        "description" => $input["description"],
        "participant_level" => $input["participant_level"],
        "starts_at" => $input["starts_at"],
        "ends_at" => $input["ends_at"],
        "timezone" => $input["timezone"] ?? "Asia/Jakarta",
        "venue_name" => $input["venue_name"],
        "address" => $input["address"],
        "area" => $input["area"] ?? null,
        "city" => $input["city"],
        "province" => $input["province"] ?? null,
        "country_code" => $input["country_code"] ?? "ID",
        "latitude" => $input["latitude"] ?? null,
        "longitude" => $input["longitude"] ?? null,
        "capacity" => $input["capacity"],
        "budget_amount" => $input["budget_amount"] ?? 0,
        "currency" => $input["currency"] ?? "IDR"
    ]);

    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Aktivitas berhasil ditambahkan",
        "data" => [
            "id" => $conn->lastInsertId(),
            "title" => $input["title"]
        ]
    ], JSON_PRETTY_PRINT);
} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Gagal menambahkan aktivitas"
    ]);
}

?>
