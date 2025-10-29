<?php
require_once __DIR__ . '/../conn/dbconn.php';

// ✅ Fetch all recyclable categories
$categories = [];
$catQuery = "SELECT id, RM_name FROM recyclable ORDER BY id ASC";
$catResult = mysqli_query($conn, $catQuery);
while ($catRow = mysqli_fetch_assoc($catResult)) {
  $categories[$catRow['id']] = $catRow['RM_name'];
}

// ✅ Pagination setup
$limit = 5; // records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// ✅ Sorting
$sort = $_GET['sort'] ?? 'date_desc';
$orderBy = "r.date DESC, r.id DESC"; // default
$sortCategory = null;

if (preg_match('/^cat_(\d+)_(asc|desc)$/', $sort, $matches)) {
  // Sorting by category column
  $sortCategory = (int)$matches[1];
  $direction = strtoupper($matches[2]);
  $orderBy = "COALESCE(SUM(CASE WHEN ri.recyclable_id = $sortCategory THEN ri.quantity ELSE 0 END),0) $direction";
} else {
  switch ($sort) {
    case 'date_asc':
      $orderBy = "r.date ASC, r.id ASC";
      break;
    case 'date_desc':
      $orderBy = "r.date DESC, r.id DESC";
      break;
    case 'name_asc':
      $orderBy = "r.record_name ASC";
      break;
    case 'name_desc':
      $orderBy = "r.record_name DESC";
      break;
  }
}

// ✅ Count total records for pagination
$countSql = "SELECT COUNT(*) AS total FROM records";
$countResult = mysqli_query($conn, $countSql);
$totalRecords = mysqli_fetch_assoc($countResult)['total'] ?? 0;
$totalPages = ceil($totalRecords / $limit);

if ($sortCategory) {
  // ✅ Sort by specific category total
  $recordSql = "
    SELECT r.id, r.date, r.record_name, r.rec_img,
           COALESCE(catTotals.total_qty, 0) AS cat_total
    FROM records r
    LEFT JOIN (
      SELECT record_id, SUM(quantity) AS total_qty
      FROM record_items
      WHERE recyclable_id = $sortCategory
      GROUP BY record_id
    ) AS catTotals ON r.id = catTotals.record_id
    ORDER BY cat_total $direction, r.date DESC
    LIMIT $limit OFFSET $offset
  ";
} else {
  // ✅ Default sorting (date/name)
  $recordSql = "
    SELECT r.id, r.date, r.record_name, r.rec_img
    FROM records r
    ORDER BY $orderBy
    LIMIT $limit OFFSET $offset
  ";
}

$recordResult = mysqli_query($conn, $recordSql);

$recordIds = [];
$records = [];
while ($row = mysqli_fetch_assoc($recordResult)) {
  $recordIds[] = $row['id'];
  $records[$row['id']] = [
    'date' => $row['date'],
    'name' => $row['record_name'],
    'rec_img' => $row['rec_img'],
    'items' => array_fill_keys(array_keys($categories), ['qty' => 0, 'unit' => ''])
  ];
}

// ✅ Step 2: Fetch items for these records
if (!empty($recordIds)) {
  $itemSql = "
    SELECT record_id, recyclable_id, quantity, unit
    FROM record_items
    WHERE record_id IN (" . implode(',', $recordIds) . ")
  ";
  $itemResult = mysqli_query($conn, $itemSql);

  while ($item = mysqli_fetch_assoc($itemResult)) {
    $rid = $item['record_id'];
    $cat = $item['recyclable_id'];
    if (isset($records[$rid]['items'][$cat])) {
      $records[$rid]['items'][$cat] = [
        'qty' => $item['quantity'],
        'unit' => $item['unit']
      ];
    }
  }
}
