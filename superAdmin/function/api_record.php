<?php
require '../conn/dbconn.php';

// Query: get all records and their items
$query = "SELECT r.id, r.record_name, r.date, 
                 GROUP_CONCAT(CONCAT(ri.quantity, ' ', ri.unit, ' of ', rec.RM_name) SEPARATOR ', ') AS materials
          FROM records r
          LEFT JOIN record_items ri ON r.id = ri.record_id
          LEFT JOIN recyclable rec ON ri.recyclable_id = rec.id
          GROUP BY r.id
          ORDER BY r.date DESC";

$result = mysqli_query($conn, $query);

$response = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = [
            "id"        => $row["id"],
            "date"      => $row["date"],
            "recordName"=> $row["record_name"],
            "materials" => $row["materials"] ?? ""
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
