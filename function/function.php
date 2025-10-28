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
            // ✅ Save user details in session
            $_SESSION['userid'] = $row['userid'];
            $_SESSION['email']  = $row['email'];
            $_SESSION['role'] = $row['role'];

            if($row['role'] == "admin") {
                header("Location: ../admin/pages/dashboard.php?userid={$row['userid']}");
                exit();
            } 
            if($row['role'] == "user") {
                header("Location: ../user/pages/user_announcement.php?userid={$row['userid']}");
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

// 🔹 SIGN UP
if (isset($_POST['signup'])) {
    $userName   = mysqli_real_escape_string($conn, $_POST['userName']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $passWord   = mysqli_real_escape_string($conn, $_POST['passWord']);
    $rePassword = mysqli_real_escape_string($conn, $_POST['rePassword']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $purok = mysqli_real_escape_string($conn, $_POST['purok']);

    // 🧩 Check password match
    if ($passWord !== $rePassword) {
        $_SESSION['registerError'] = "Passwords do not match.";
        header("Location: /capstoneweb/signUp.php");
        exit();
    }

    // 🧩 Check if email already exists
    $checkEmail = "SELECT * FROM account WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        $_SESSION['registerError'] = "Email already exists.";
        header("Location: /capstoneweb/signUp.php");
        exit();
    }

    // 🧩 Insert new user
    if (!empty($userName) && !empty($email) && !empty($passWord) && !empty($role) && !empty($purok)) {
        $query2 = "INSERT INTO account (userName, passWord, email, role, purok)
                   VALUES ('$userName', '$passWord', '$email', '$role', '$purok')";

        if (mysqli_query($conn, $query2)) {
            $_SESSION['registerSuccess'] = "Account created successfully! You can now log in.";
            header("Location: /capstoneweb/login.php");
            exit();
        } else {
            $_SESSION['registerError'] = "Database error: " . mysqli_error($conn);
            header("Location: /capstoneweb/signUp.php");
            exit();
        }
    } else {
        $_SESSION['registerError'] = "All fields are required.";
        header("Location: /capstoneweb/signUp.php");
        exit();
    }
}


// For Account Editing
if (isset($_POST['submitsetting'])) {
    $userid     = mysqli_real_escape_string($conn, $_POST['userid']);
    $userName   = mysqli_real_escape_string($conn, $_POST['userName']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $passWord   = mysqli_real_escape_string($conn, $_POST['passWord']);
    $rePassword = mysqli_real_escape_string($conn, $_POST['rePassword']);

    // ✅ Password check
    if ($passWord !== $rePassword) {
        echo "<script>alert('Passwords do not match! Please try again.'); history.back();</script>";
        exit();
    }

    // ✅ Update user info (excluding image first)
    $updateInfo = "UPDATE account 
                   SET userName = '$userName', email = '$email', passWord = '$passWord' 
                   WHERE userid = '$userid'";
    mysqli_query($conn, $updateInfo);

    // ✅ Handle profile image upload
    if (!empty($_FILES['userimg']['name'])) {
        $userimg = $_FILES['userimg']['name'];
        $tmpName = $_FILES['userimg']['tmp_name'];
        $targetDir = "/capstoneweb/image/";
        $targetFile = $targetDir . basename($userimg);
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validate image file type (optional but safer)
        $allowedTypes = ['jpg', 'jpeg', 'png'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($tmpName, $targetFile)) {
                $updateImage = "UPDATE account SET userimg = '$userimg' WHERE userid = '$userid'";
                mysqli_query($conn, $updateImage);
            }
        } else {
            echo "<script>alert('Invalid image type. Only JPG and PNG are allowed.'); history.back();</script>";
            exit();
        }
    }

    // ✅ Fetch user role
    function getUserInfo($conn, $userid) {
        $query = "SELECT role FROM account WHERE userid = '$userid'";
        $result = mysqli_query($conn, $query);
        return mysqli_fetch_assoc($result);
    }

    $user = getUserInfo($conn, $_SESSION['userid']);
    $previousPage = $_SERVER['HTTP_REFERER'] ?? '';

    // ✅ Redirect based on role
    if ($user) {
        if ($user['role'] === 'admin') {
            if (strpos($previousPage, 'accsetting.php') !== false || empty($previousPage)) {
                $previousPage = '/capstoneweb/admin/pages/dashboard.php';
            }
        } elseif ($user['role'] === 'user') {
            if (strpos($previousPage, 'accsetting.php') !== false || empty($previousPage)) {
                $previousPage = '/capstoneweb/user/pages/user_announcement.php';
            }
        } else {
            echo "<script>alert('Error: user role not found');</script>";
            exit();
        }

        echo "<script>
            alert('Account updated successfully!');
            window.location.href = '$previousPage';
        </script>";
        exit();
    } else {
        echo "<script>alert('Error: unable to fetch user info.'); history.back();</script>";
        exit();
    }
}



// Back-End for Announcement
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
        header("Location: ../pages/announcement.php?userid={$userid}");
        exit();
    } else {
        echo "<script>alert('Adding announcement failed.');</script>";
    }
}

// <!-- add material -->
if (isset($_POST['add_material'])) {
    $RM_name = mysqli_real_escape_string($conn, $_POST['RM_name']);
    $points  = mysqli_real_escape_string($conn, $_POST['points']);

    // Handle image upload
    $filename = "";
    if (!empty($_FILES["RM_img"]["name"])) {
        $filename = time() . "_" . basename($_FILES["RM_img"]["name"]); // unique filename
        $tempname = $_FILES["RM_img"]["tmp_name"];
        $folder   = "../assets/" . $filename;

        if (!move_uploaded_file($tempname, $folder)) {
            $filename = ""; // fallback if upload fails
        }
    }

    $insert = "INSERT INTO recyclable (RM_name, RM_img) 
               VALUES ('$RM_name', '$filename')";

    if (mysqli_query($conn, $insert)) {

        $userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
        header("Location: ../pages/recyclables.php?userid={$userid}");
        exit();
    } else {
        echo "<script>alert('Failed to add material.');</script>";
    }
}

if (isset($_POST['submit_redeem'])) {
    require_once __DIR__ . '/../conn/dbconn.php'; // Make sure the DB connection is included
    session_start();

    //  Get the logged-in user's ID from session
    $userid = mysqli_real_escape_string($conn, $_POST['user_id']); // user selected by admin


    // Sanitize inputs
    $record_name = mysqli_real_escape_string($conn, $_POST['record_name']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $materials = $_POST['materials'] ?? [];

    // Insert record into records table
    $insertRecord = "INSERT INTO records (record_name, date, user_id) 
                     VALUES ('$record_name', '$date', '$userid')";
    $insertResult = mysqli_query($conn, $insertRecord);

    if (!$insertResult) {
        die("❌ Failed to insert record: " . mysqli_error($conn));
    }

    $record_id = mysqli_insert_id($conn);

    // Handle image upload (optional)
    if (!empty($_FILES["rec_img"]["name"])) {
        $filename = time() . "_" . basename($_FILES["rec_img"]["name"]);
        $tempname = $_FILES["rec_img"]["tmp_name"];
        $folder = "../assets/proofs/" . $filename;

        if (move_uploaded_file($tempname, $folder)) {
            $updateImg = "UPDATE records SET rec_img = '$filename' WHERE id = $record_id";
            mysqli_query($conn, $updateImg);
        }
    }

    // Insert recyclable materials into record_items
    foreach ($materials as $recyclable_id => $data) {
        $quantity = (float)($data['quantity'] ?? 0);
        $unit = mysqli_real_escape_string($conn, $data['unit'] ?? 'kg');

        if ($quantity > 0) {
            $insertItem = "INSERT INTO record_items (record_id, recyclable_id, quantity, unit) 
                           VALUES ($record_id, $recyclable_id, $quantity, '$unit')";
            mysqli_query($conn, $insertItem);
        }
    }

    // Redirect after success
    header("Location: ../pages/record.php?userid={$userid}");
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
    header("Location: ../pages/record.php?userid={$userid}");
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
        header("Location: ../pages/notification.php?userid=$userid");
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
        echo "<script>alert('Announcement updated successfully.'); window.location.href='../pages/announcement.php?userid={$_SESSION['userid']}';</script>";
    } else {
        echo "<script>alert('Update failed.'); window.history.back();</script>";
    }
}

if (isset($_POST['submit_redeem'])) {
    require_once __DIR__ . '/../conn/dbconn.php';
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

    header("Location: ../pages/record.php?userid={$userid}");
    exit();
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
    header("Location: ../pages/reward.php?userid={$userid}");
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
        $target = "../productImg/" . $img;
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
        echo "<script>alert('Reward updated successfully.'); window.location.href='../pages/reward.php?userid={$_SESSION['userid']}';</script>";
    } else {
        echo "<script>alert('Update failed.'); window.history.back();</script>";
    }
}
