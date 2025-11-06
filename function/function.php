<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
session_start();
require_once __DIR__ . '/../conn/dbconn.php';


// log in 
if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $passWord = mysqli_real_escape_string($conn, $_POST['passWord']);

    $query = "SELECT * FROM account WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if ($row['passWord'] === $passWord) {

            // ✅ Block unapproved users (only for regular users)
            if ($row['role'] === 'user' && $row['status'] !== 'approved') {
                $_SESSION['login_error'] = "Your account is not approved yet. Please wait for admin verification.";
                header("Location: ../login.php");
                exit();
            }
            if ($row['role'] === 'admin' && $row['status'] !== 'approved') {
                $_SESSION['login_error'] = "Your account is not approved yet. Please wait for SuperAdmin verification.";
                header("Location: ../login.php");
                exit();
            }

            // ✅ Save user details in session
            $_SESSION['userid'] = $row['userid'];
            $_SESSION['email']  = $row['email'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == "admin") {
                header("Location: ../admin/pages/dashboard.php?userid={$row['userid']}");
                exit();
            }
            if ($row['role'] == "user") {
                header("Location: ../user/pages/user_announcement.php?userid={$row['userid']}");
                exit();
            }
            if ($row['role'] == "superAdmin") {
                header("Location: ../superAdmin/pages/dashboard.php?userid={$row['userid']}");
                exit();
            }
        } else {
            // ❌ Incorrect password
            $_SESSION['login_error'] = "Email or password didn't match.";
            header("Location: ../login.php");
            exit();
        }
    } else {
        // ❌ No user found
        $_SESSION['login_error'] = "Unregistered account.";
        header("Location: ../login.php");
        exit();
    }
}


if (isset($_POST['signup'])) {
    $userName   = mysqli_real_escape_string($conn, $_POST['userName']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $passWord   = mysqli_real_escape_string($conn, $_POST['passWord']);
    $rePassword = mysqli_real_escape_string($conn, $_POST['rePassword']);
    $role       = mysqli_real_escape_string($conn, $_POST['role']);
    $purok      = isset($_POST['purok']) ? mysqli_real_escape_string($conn, $_POST['purok']) : null;

    // Check required fields
    if (empty($userName) || empty($email) || empty($passWord) || empty($rePassword) || empty($role)) {
        $_SESSION['registerError'] = "All fields are required.";
        header("Location: /capstoneweb/signUp.php");
        exit();
    }

    // Purok required only for users
    if ($role === 'user' && empty($purok)) {
        $_SESSION['registerError'] = "Please select a Purok.";
        header("Location: /capstoneweb/signUp.php");
        exit();
    }

    // Password match
    if ($passWord !== $rePassword) {
        $_SESSION['registerError'] = "Passwords do not match.";
        header("Location: /capstoneweb/signUp.php");
        exit();
    }

    // Check email uniqueness
    $checkEmail = "SELECT * FROM account WHERE email = '$email'";
    $result = $conn->query($checkEmail);
    if ($result->num_rows > 0) {
        $_SESSION['registerError'] = "Email already exists.";
        header("Location: /capstoneweb/signUp.php");
        exit();
    }

    //  Set status
    $status = ($role === 'user' || $role === 'admin') ? 'pending' : 'approved';

    // Insert user
    $query = "INSERT INTO account (userName, passWord, email, role, purok, status)
              VALUES ('$userName', '$passWord', '$email', '$role', '$purok', '$status')";

    if (mysqli_query($conn, $query)) {
        // Get the newly inserted user's ID
        $newUserId = $conn->insert_id;

        // Include QR code library
        require_once __DIR__ . '/../includes/phpqrcode/qrlib.php';

        // Prepare directory for QR codes
        $qrDir = __DIR__ . '/../uploads/qrcodes/';
        if (!is_dir($qrDir)) {
            mkdir($qrDir, 0777, true);
        }

        // ✅ Get your local IP so your phone can access localhost via Wi-Fi
        $localIP = getHostByName(getHostName());

        // ✅ Link that the QR will open when scanned
        $qrURL = "http://{$localIP}/capstoneweb/user/pages/view_user_records.php?userid=" . $newUserId;

        // Create QR code filename and path
        $qrFileName = "qr_" . $newUserId . ".png";
        $qrFilePath = $qrDir . $qrFileName;

        // ✅ Generate QR code that links directly to the user’s records page
        QRcode::png($qrURL, $qrFilePath, QR_ECLEVEL_L, 5);

        // Save filename in database
        $updateQuery = "UPDATE account SET qr_code = ? WHERE userid = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $qrFileName, $newUserId);
        $stmt->execute();
        $stmt->close();


        // Success message
        $_SESSION['registerSuccess'] = ($role === 'user')
            ? "Account created successfully! Please wait for admin approval."
            : "Admin account created successfully!";

        header("Location: /capstoneweb/login.php");
        exit();
    } else {
        $_SESSION['registerError'] = "Database error: " . mysqli_error($conn);
        header("Location: /capstoneweb/signUp.php");
        exit();
    }
}

// 🔹 USER ACCOUNT SETTINGS
if (isset($_POST['submitsetting'])) {
    $userid     = mysqli_real_escape_string($conn, $_POST['userid']);
    $userName   = mysqli_real_escape_string($conn, $_POST['userName']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $passWord   = mysqli_real_escape_string($conn, $_POST['passWord']);
    $rePassword = mysqli_real_escape_string($conn, $_POST['rePassword']);

    // ✅ Password match check
    if ($passWord !== $rePassword) {
        echo "<script>alert('Passwords do not match!'); history.back();</script>";
        exit();
    }

    // ✅ Handle image upload
    $userimg = "";
    if (!empty($_FILES["userimg"]["name"])) {
        $filename = basename($_FILES["userimg"]["name"]);
        $tempname = $_FILES["userimg"]["tmp_name"];
        $folder   = __DIR__ . "/../user/image/" . $filename; // ✅ User image path

        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Only JPG, JPEG, PNG files are allowed.'); history.back();</script>";
            exit();
        }

        if (!move_uploaded_file($tempname, $folder)) {
            echo "<script>alert('Image upload failed.'); history.back();</script>";
            exit();
        }
        $userimg = $filename;
    }

    // ✅ Update user info
    $update = !empty($userimg)
        ? "UPDATE account SET userName='$userName', email='$email', passWord='$passWord', userimg='$userimg' WHERE userid='$userid'"
        : "UPDATE account SET userName='$userName', email='$email', passWord='$passWord' WHERE userid='$userid'";

    if (!mysqli_query($conn, $update)) {
        echo "<script>alert('Error updating account: " . mysqli_error($conn) . "');</script>";
        exit();
    }

    echo "<script>alert('Account updated successfully!'); window.location.href='/capstoneweb/user/pages/user_announcement.php';</script>";
    exit();
}

// 🔹 ADMIN ACCOUNT SETTINGS
if (isset($_POST['adminsetting'])) {
    $userid     = mysqli_real_escape_string($conn, $_POST['userid']);
    $userName   = mysqli_real_escape_string($conn, $_POST['userName']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $passWord   = mysqli_real_escape_string($conn, $_POST['passWord']);
    $rePassword = mysqli_real_escape_string($conn, $_POST['rePassword']);

    // ✅ Password match check
    if ($passWord !== $rePassword) {
        echo "<script>alert('Passwords do not match!'); history.back();</script>";
        exit();
    }

    // ✅ Handle image upload
    $userimg = "";
    if (!empty($_FILES["userimg"]["name"])) {
        $filename = basename($_FILES["userimg"]["name"]);
        $tempname = $_FILES["userimg"]["tmp_name"];
        $folder   = __DIR__ . "/../admin/image/" . $filename; // ✅ Admin image path

        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Only JPG, JPEG, PNG files are allowed.'); history.back();</script>";
            exit();
        }

        if (!move_uploaded_file($tempname, $folder)) {
            echo "<script>alert('Image upload failed.'); history.back();</script>";
            exit();
        }
        $userimg = $filename;
    }

    // ✅ Update admin info
    $update = !empty($userimg)
        ? "UPDATE account SET userName='$userName', email='$email', passWord='$passWord', userimg='$userimg' WHERE userid='$userid'"
        : "UPDATE account SET userName='$userName', email='$email', passWord='$passWord' WHERE userid='$userid'";

    if (!mysqli_query($conn, $update)) {
        echo "<script>alert('Error updating account: " . mysqli_error($conn) . "');</script>";
        exit();
    }

    echo "<script>alert('Account updated successfully!'); window.location.href='/capstoneweb/admin/pages/dashboard.php';</script>";
    exit();
}

if (isset($_POST['submit_announcement'])) {
    $announce_name = mysqli_real_escape_string($conn, $_POST['announce_name']);
    $announce_text = mysqli_real_escape_string($conn, $_POST['announce_text']);
    $announce_date = mysqli_real_escape_string($conn, $_POST['announce_date']);

    $filename = "";
    if (!empty($_FILES["announce_img"]["name"])) {
        $filename = basename($_FILES["announce_img"]["name"]);
        $tempname = $_FILES["announce_img"]["tmp_name"];
        $folder   = "../announceImg/" . $filename;

        if (!move_uploaded_file($tempname, $folder)) {
            echo "<script>alert('Image upload failed.');</script>";
            $filename = "";
        }
    }

    $query = "INSERT INTO announcement ( announce_name, announce_text, announce_date, announce_img)
          VALUES ( '$announce_name', '$announce_text', '$announce_date', '$filename')";

    if (mysqli_query($conn, $query)) {
        header("Location: ../admin/pages/announcement.php?userid={$userid}");
        exit();
    } else {
        echo "<script>alert('Adding announcement failed.');</script>";
    }
}

if (isset($_POST['archive_selected'])) {
    if (!empty($_POST['archive_ids'])) {
        $archive_ids = $_POST['archive_ids'];
        $id_list = implode(",", array_map('intval', $archive_ids));

        $archive_query = "UPDATE announcement SET status = 'Archived' WHERE announce_id IN ($id_list)";
        if (mysqli_query($conn, $archive_query)) {
            $_SESSION['message'] = 'Selected announcements archived successfully.';
        } else {
            $_SESSION['message'] = 'Failed to archive announcements.';
        }
    } else {
        $_SESSION['message'] = 'No announcements selected.';
    }

    $userid = $_SESSION['userid'] ?? 0;
    header("Location: ../admin/pages/announcement.php?userid={$userid}");
    exit();
}

// add material/recyclables
if (isset($_POST['add_material'])) {
    $RM_name = mysqli_real_escape_string($conn, $_POST['RM_name']);
    $points  = mysqli_real_escape_string($conn, $_POST['points'] ?? '');

    $filename = "";
    if (!empty($_FILES["RM_img"]["name"])) {
        $uploadDir = __DIR__ . "/../assets/"; // safer absolute path
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $filename = time() . "_" . basename($_FILES["RM_img"]["name"]);
        $tempname = $_FILES["RM_img"]["tmp_name"];
        $targetFile = $uploadDir . $filename;

        if (!move_uploaded_file($tempname, $targetFile)) {
            echo "<script>alert('Failed to upload image.');</script>";
            $filename = "";
        }
    }

    $insert = "INSERT INTO recyclable (RM_name, RM_img) VALUES ('$RM_name', '$filename')";
    if (mysqli_query($conn, $insert)) {
        $userid = $_SESSION['userid'] ?? 0;
        header("Location: ../admin/pages/recyclables.php?userid={$userid}");
        exit();
    } else {
        echo "<script>alert('Failed to add material: " . mysqli_error($conn) . "');</script>";
    }
}

// add record
if (isset($_POST['submit_redeem'])) {
    // --- Get selected user ---
    $userid = mysqli_real_escape_string($conn, $_POST['user_id']);
    $getUserQuery = "SELECT userName, purok FROM account WHERE userid = '$userid'";
    $getUserResult = mysqli_query($conn, $getUserQuery);
    $userData = mysqli_fetch_assoc($getUserResult);
    $userName = mysqli_real_escape_string($conn, $userData['userName'] ?? 'Unknown');
    $purok = mysqli_real_escape_string($conn, $userData['purok'] ?? '');

    // --- Basic info ---
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $materials = $_POST['materials'] ?? [];

    // --- 1️⃣ Insert record first ---
    $insertRecord = "INSERT INTO records (record_name, user_id, date)
                     VALUES ('$userName', '$userid', '$date')";
    $insertResult = mysqli_query($conn, $insertRecord);

    if (!$insertResult) {
        die("❌ Error inserting record: " . mysqli_error($conn));
    }

    $record_id = mysqli_insert_id($conn);

    // --- 2️⃣ Handle file upload ---
    if (isset($_FILES['rec_img']) && $_FILES['rec_img']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = realpath(__DIR__ . '/../assets/proofs/');

    if ($uploadDir === false) {
        echo "<script>alert('⚠️ Upload folder not found. Please check path.');</script>";
    } else {
        $fileTmpPath = $_FILES['rec_img']['tmp_name'];
        $fileName = time() . "_" . basename($_FILES['rec_img']['name']);
        $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        if (move_uploaded_file($fileTmpPath, $targetPath)) {
            $fileNameDB = mysqli_real_escape_string($conn, $fileName);
            mysqli_query($conn, "UPDATE records SET rec_img = '$fileNameDB' WHERE id = $record_id");
        } else {
            echo "<script>alert('⚠️ move_uploaded_file() failed. Check folder permissions and paths.');</script>";
        }
    }
}


    // --- 3️⃣ Insert recyclable materials ---
    foreach ($materials as $recyclable_id => $data) {
        if (!isset($data['quantity']) || trim($data['quantity']) === '') continue;
        $quantity = (float)$data['quantity'];
        $unit = mysqli_real_escape_string($conn, $data['unit'] ?? 'kg');
        $insertItem = "INSERT INTO record_items (record_id, recyclable_id, quantity, unit)
                       VALUES ($record_id, $recyclable_id, $quantity, '$unit')";
        mysqli_query($conn, $insertItem);
    }

    // --- ✅ Redirect ---
    echo "<script>
        alert('Record and image saved successfully!');
        window.location.href = '{$_SERVER['HTTP_REFERER']}';
    </script>";
    exit();
}

// Reset Button for records
if (isset($_POST['reset_data'])) {
    // Check if there are any records
    $checkRecords = "SELECT COUNT(*) as total FROM records";
    $result = mysqli_query($conn, $checkRecords);
    $row = mysqli_fetch_assoc($result);

    if ($row['total'] > 0) {
        // Delete all rows
        mysqli_query($conn, "DELETE FROM record_items");
        mysqli_query($conn, "DELETE FROM records");

        echo "<script>alert('All redemption records have been reset successfully.');</script>";
    } else {
        echo "<script>alert('No records found to reset.');</script>";
    }

    $userid = $_SESSION['userid'] ?? 0;
    header("Location: ../admin/pages/record.php?userid={$userid}");
    exit();
}

// Notification
if (isset($_POST['submit_pickup'])) {
    $userid     = $_SESSION['userid'] ?? 0;
    $address    = mysqli_real_escape_string($conn, $_POST['address']);
    $pickupDate = mysqli_real_escape_string($conn, $_POST['pickup_date']);
    $pickupTime = mysqli_real_escape_string($conn, $_POST['pickup_time']);

    // Optional: handle image proof (if uploaded)
    $imagePath = "";
    if (!empty($_FILES['pickup_img']['name'])) {
        $filename = time() . "_" . basename($_FILES['pickup_img']['name']);
        $tempname = $_FILES['pickup_img']['tmp_name'];
        $folder   = "../assets/pickups/" . $filename;

        if (move_uploaded_file($tempname, $folder)) {
            $imagePath = $filename;
        }
    }

    $insert = "INSERT INTO notifications (user_id, address, image_path, pickup_date, pickup_time, status) 
               VALUES ('$userid', '$address', '$imagePath', '$pickupDate', '$pickupTime', 'Pending')";

    if (mysqli_query($conn, $insert)) {
        header("Location: ../admin/pages/notification.php?userid=$userid");
        exit();
    } else {
        echo "<script>alert('Failed to submit pickup request.');</script>";
    }
}

// Function to fetch notifications (can be called in notification.php)
function getNotifications($conn, $userid)
{
    $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    return $notifications;
}

// update announcement
if (isset($_POST['update_announcement'])) {
    $id = intval($_POST['announce_id']);
    $name = mysqli_real_escape_string($conn, $_POST['announce_name']);
    $text = mysqli_real_escape_string($conn, $_POST['announce_text']);
    $date = mysqli_real_escape_string($conn, $_POST['announce_date']);

    $img = "";
    if (!empty($_FILES['announce_img']['name'])) {
        $img = basename($_FILES['announce_img']['name']);
        $target = "../announceImg/" . $img;
        move_uploaded_file($_FILES['announce_img']['tmp_name'], $target);

        $sql = "UPDATE announcement 
                SET announce_name='$name', announce_text='$text', announce_date='$date', announce_img='$img'
                WHERE announce_id=$id";
    } else {
        $sql = "UPDATE announcement 
                SET announce_name='$name', announce_text='$text', announce_date='$date'
                WHERE announce_id=$id";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Announcement updated successfully.'); window.location.href='../admin/pages/announcement.php?userid={$_SESSION['userid']}';</script>";
    } else {
        echo "<script>alert('Update failed.'); window.history.back();</script>";
    }
}

if (isset($_POST['submit_redeem'])) {
    session_start();

    // Get selected user from dropdown
    $userid = mysqli_real_escape_string($conn, $_POST['user_id']);

    // Get admin ID (logged in)
    $admin_id = $_SESSION['userid'] ?? 0;

    // Other fields
    $record_name = mysqli_real_escape_string($conn, $_POST['record_name']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $materials = $_POST['materials'] ?? [];

    // Insert record (link to selected user)
    $insertRecord = "INSERT INTO records (record_name, date, user_id, encoded_by) 
                     VALUES ('$record_name', '$date', '$userid', '$admin_id')";
    mysqli_query($conn, $insertRecord);
    $record_id = mysqli_insert_id($conn);

    // Handle optional image
    if (!empty($_FILES["rec_img"]["name"])) {
        $filename = time() . "_" . basename($_FILES["rec_img"]["name"]);
        $tempname = $_FILES["rec_img"]["tmp_name"];
        $folder = "../assets/proofs/" . $filename;

        if (move_uploaded_file($tempname, $folder)) {
            mysqli_query($conn, "UPDATE records SET rec_img = '$filename' WHERE id = $record_id");
        }
    }

    // Insert recyclable materials
    foreach ($materials as $recyclable_id => $data) {
        $quantity = (int)($data['quantity'] ?? 0);
        $unit = mysqli_real_escape_string($conn, $data['unit'] ?? 'kg');

        if ($quantity > 0) {
            $insertItem = "INSERT INTO record_items (record_id, recyclable_id, quantity, unit) 
                           VALUES ($record_id, $recyclable_id, $quantity, '$unit')";
            mysqli_query($conn, $insertItem);
        }
    }

    header("Location: ../admin/pages/record.php?userid={$userid}");
    exit();
}

// submitting rewards to database
// submitting rewards to database
if (isset($_POST['submit_rewards'])) {
    session_start(); // ensure session is started
    $role = $_SESSION['role'] ?? null;
    $userid = $_SESSION['userid'] ?? null;

    if (!$role || !$userid) {
        header("Location: ../login.php");
        exit;
    }

    // Get inputs (HTML required ensures not empty)
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
    $product_points = intval($_POST['product_points']);
    $product_date = !empty($_POST['product_date']) ? $_POST['product_date'] : date('Y-m-d');

    // Handle image upload (optional)
    $product_img = "";
    if (!empty($_FILES['product_img']['name'])) {
        $filename = time() . "_" . basename($_FILES['product_img']['name']);
        $tempname = $_FILES['product_img']['tmp_name'];
        $folder = "../uploads/productImg/" . $filename;

        if (!file_exists("../uploads/productImg/")) {
            mkdir("../uploads/productImg/", 0777, true);
        }
        move_uploaded_file($tempname, $folder);
        $product_img = $filename;
    }

    // Insert into database
    $insert = "INSERT INTO rewards (product_name, product_description, product_points, product_date, product_img)
               VALUES ('$product_name', '$product_description', '$product_points', '$product_date', '$product_img')";

    if (mysqli_query($conn, $insert)) {
        $_SESSION['reward_success'] = "Reward added successfully!";
    } else {
        $_SESSION['reward_errors'] = ["Failed to add reward. Please try again."];
        error_log("Database error: " . mysqli_error($conn));
    }

    // Role-based redirect
    if ($role === 'admin') {
        $redirect_url = "../admin/pages/reward.php?userid={$userid}";
    } elseif ($role === 'superAdmin') {
        $redirect_url = "../superAdmin/pages/reward.php?userid={$userid}";
    } else {
        $redirect_url = "../login.php";
    }

    header("Location: $redirect_url");
    exit;
}


// Reset Button for rewards
if (isset($_POST['reset_rewards'])) {
    // Delete all rows
    $deleteQuery = "DELETE FROM rewards";
    if (mysqli_query($conn, $deleteQuery)) {
        echo "<script>alert('All rewards have been reset successfully.');</script>";
    } else {
        echo "<script>alert('Failed to reset rewards. Please try again.');</script>";
    }

    $userid = $_SESSION['userid'] ?? 0;
    header("Location: ../admin/pages/reward.php?userid={$userid}");
    exit();
}

// Handle reward update
if (isset($_POST['update_reward'])) {
    $id = intval($_POST['reward_id']);
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['product_description']);
    $points = intval($_POST['product_points']);
    $date = mysqli_real_escape_string($conn, $_POST['product_date']);

    $img = "";
    if (!empty($_FILES['product_img']['name'])) {
        $img = basename($_FILES['product_img']['name']);
        $target = "../admin/productImg/" . $img;
        move_uploaded_file($_FILES['product_img']['tmp_name'], $target);

        $sql = "UPDATE rewards 
                SET product_name='$name', product_description='$description', product_points=$points, product_date='$date', product_img='$img'
                WHERE reward_id=$id";
    } else {
        $sql = "UPDATE rewards 
                SET product_name='$name', product_description='$description', product_points=$points, product_date='$date'
                WHERE reward_id=$id";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Reward updated successfully.'); window.location.href='../admin/pages/reward.php?userid={$_SESSION['userid']}';</script>";
    } else {
        echo "<script>alert('Update failed.'); window.history.back();</script>";
    }
}

if (isset($_POST['approve_user'])) {
    $userId = intval($_POST['userid']);

    $sql = "UPDATE account SET status = 'approved' WHERE userid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ User account approved successfully.";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "❌ Error approving account: " . $stmt->error;
        $_SESSION['alert_type'] = "danger";
    }

    $stmt->close();
    header("Location: ../admin/pages/admin_accveri.php");
    exit();
}

if (isset($_POST['superadmin_approve_user'])) {
    $userId = intval($_POST['userid']);

    $sql = "UPDATE account SET status = 'approved' WHERE userid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ User account approved successfully.";
        $_SESSION['alert_type'] = "success";
    } else {
        $_SESSION['message'] = "❌ Error approving account: " . $stmt->error;
        $_SESSION['alert_type'] = "danger";
    }

    $stmt->close();
    header("Location: ../superAdmin/pages/superadmin_accveri.php");
    exit();
}


// user account verification "REJECT"
if (isset($_POST['reject_user'])) {
    $userId = intval($_POST['userid']);

    $sql = "UPDATE account SET status = 'rejected' WHERE userid = $userId";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "User Account Rejected Successfully";
    } else {
        $_SESSION['message'] = "Error Rejected Account" . mysqli_error($conn);
    }

    header("Location:../admin/pages/admin_accveri.php?userid={$userid}");
    exit();
}

if (isset($_POST['superadmin_reject_user'])) {
    $userId = intval($_POST['userid']);

    $sql = "UPDATE account SET status = 'rejected' WHERE userid = $userId";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "User Account Rejected Successfully";
    } else {
        $_SESSION['message'] = "Error Rejected Account" . mysqli_error($conn);
    }

    header("Location:../superAdmin/pages/superadmin_accveri.php?userid={$userid}");
    exit();
}

// Handle reward approval
if (isset($_POST['approve_reward'])) {
    $userid = intval($_POST['user_id']);
    $reward_id = intval($_POST['reward_id']);
    $role = $_SESSION['role'];

    // Update the reward status
    $query = "UPDATE user_rewards SET status = 'Approved' WHERE user_id = '$userid' AND reward_id='$reward_id'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['notif_success'] = "Reward successfully approved!";
    } else {
        $_SESSION['notif_error'] = "Failed to approve reward. Please try again.";
    }

    // Role-based redirect path
    if ($role === 'admin') {
        header("Location: ../admin/pages/notification.php?userid={$userid}");
    } elseif ($role === 'superAdmin') {
        header("Location: ../superAdmin/pages/notification.php?userid={$userid}");
    } else {
        header("Location: ../login.php");
    }
    exit;
}

function getUserQRCode($conn, $userid)
{
    $sql = "SELECT qr_code FROM account WHERE userid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && !empty($row['qr_code'])) {
        $qrFile = trim($row['qr_code']);

        // Web-accessible path (for <img src="">)
        $qrPath = "/capstoneweb/uploads/qrcodes/" . $qrFile;

        // Absolute file path (for PHP file_exists)
        $absolutePath = "http://192.168.0.208/capstoneweb/uploads/qrcodes/" . $qrFile;

        if (file_exists($absolutePath)) {
            return $qrPath; // Return the web path so browser can load it
        }
    }

    return null; // Not found
}
