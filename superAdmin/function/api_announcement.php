<?php
require '../conn/dbconn.php';

$query = "SELECT announce_id, announce_name, announce_text, announce_date, announce_img 
          FROM announcement 
          ORDER BY announce_date DESC";

$result = mysqli_query($conn, $query);

$response = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = [
            "id"          => $row["announce_id"],     // use correct column
            "title"       => $row["announce_name"],
            "description" => $row["announce_text"],
            "date"        => $row["announce_date"],
            "imageUrl"    => $row["announce_img"] 
                ? "http://10.0.2.2/capstoneWeb/announceImg/" . $row["announce_img"]
                : ""
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
