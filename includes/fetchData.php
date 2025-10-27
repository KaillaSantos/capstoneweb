<?php
require_once __DIR__ . '/../conn/dbconn.php';


// ✅ Fetch recyclable categories
$categories = [];
$catQuery = "SELECT id, RM_name FROM recyclable ORDER BY id ASC";
$catResult = mysqli_query($conn, $catQuery);
while ($catRow = mysqli_fetch_assoc($catResult)) {
  $categories[$catRow['id']] = $catRow['RM_name'];
}

// ✅ Pagination
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// ✅ Count total records
$countSql = "SELECT COUNT(*) AS total FROM records";
$countResult = mysqli_query($conn, $countSql);
$countRow = mysqli_fetch_assoc($countResult);
$totalRecords = $countRow['total'];
$totalPages = ceil($totalRecords / $limit);

// ✅ Sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';
$sortOptions = [
  'date_asc' => 'r.date ASC, r.id ASC',
  'date_desc' => 'r.date DESC, r.id DESC',
  'name_asc' => 'r.record_name ASC',
  'name_desc' => 'r.record_name DESC'
];

// Add dynamic sorting for each recyclable category
foreach ($categories as $catId => $catName) {
  $key = 'cat_' . $catId . '_asc';
  $sortOptions[$key] = "SUM(CASE WHEN ri.recyclable_id = $catId THEN ri.quantity ELSE 0 END) ASC";
  $key = 'cat_' . $catId . '_desc';
  $sortOptions[$key] = "SUM(CASE WHEN ri.recyclable_id = $catId THEN ri.quantity ELSE 0 END) DESC";
}

$orderBy = $sortOptions[$sort] ?? 'r.date DESC, r.id DESC';

// ✅ Get IDs (pagination)
$idSql = "SELECT r.id FROM records r ORDER BY $orderBy LIMIT $limit OFFSET $offset";
$idResult = mysqli_query($conn, $idSql);
$recordIds = [];
while ($idRow = mysqli_fetch_assoc($idResult)) {
  $recordIds[] = $idRow['id'];
}

// ✅ Fetch records
$records = [];
if (!empty($recordIds)) {
  $ids = implode(",", $recordIds);
  $sql = "
    SELECT r.id, r.date, r.record_name, r.rec_img, ri.recyclable_id, ri.quantity, ri.unit
    FROM records r
    LEFT JOIN record_items ri ON r.id = ri.record_id
    WHERE r.id IN ($ids)
    ORDER BY $orderBy
  ";
  $result = mysqli_query($conn, $sql);

  while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    if (!isset($records[$id])) {
      $records[$id] = [
        'date' => $row['date'],
        'name' => $row['record_name'],
        'rec_img' => $row['rec_img'],
        'items' => array_fill_keys(array_keys($categories), ['qty' => 0, 'unit' => ''])
      ];
    }

    if ($row['recyclable_id']) {
      $records[$id]['items'][$row['recyclable_id']] = [
        'qty' => $row['quantity'],
        'unit' => $row['unit']
      ];
    }
  }
}
?>
