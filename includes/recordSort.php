<?php
// ✅ Pagination settings
$limit = 5; // 5 records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// ✅ Count total records
$countSql = "SELECT COUNT(*) AS total FROM records r";
$countResult = mysqli_query($conn, $countSql);
$countRow = mysqli_fetch_assoc($countResult);
$totalRecords = $countRow['total'];
$totalPages = ceil($totalRecords / $limit);

if (isset($_POST['userid'])) {
  $userid = mysqli_real_escape_string($conn, $_POST['userid']);
}
// ✅ Pagination settings
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// ✅ Sorting persistence (keep last choice across pages)
if (isset($_POST['sort'])) {
  $_SESSION['sort'] = $_POST['sort'];
}
$sort = $_SESSION['sort'] ?? 'date_desc';

// ✅ Sorting logic
switch ($sort) {
  case 'date_asc':
    $orderBy = "r.date ASC, r.id ASC";
    break;
  case 'name_asc':
    $orderBy = "r.record_name ASC";
    break;
  case 'name_desc':
    $orderBy = "r.record_name DESC";
    break;
  default: // newest first
    $orderBy = "r.date DESC, r.id DESC";
    break;
}

// ✅ Step 1: Get record IDs for this page with sorting
$idSql = "SELECT id 
          FROM records r
          ORDER BY $orderBy
          LIMIT $limit OFFSET $offset";
$idResult = mysqli_query($conn, $idSql);

$recordIds = [];
while ($idRow = mysqli_fetch_assoc($idResult)) {
  $recordIds[] = $idRow['id'];
}

// ✅ Step 2: Fetch full record details
$records = [];
if (!empty($recordIds)) {
  $ids = implode(",", $recordIds);

  $sql = "SELECT r.id, r.date, r.record_name, r.rec_img,
                 ri.recyclable_id, ri.quantity, ri.unit
          FROM records r
          LEFT JOIN record_items ri ON r.id = ri.record_id
          WHERE r.id IN ($ids)
          ORDER BY $orderBy";
  $result = mysqli_query($conn, $sql);

  while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];

    if (!isset($records[$id])) {
      $records[$id] = [
        'date' => $row['date'],
        'name' => $row['record_name'],
        'items' => array_fill_keys(array_keys($categories), ['qty' => 0, 'unit' => '']),
        'rec_img' => $row['rec_img']
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
