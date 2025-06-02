<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connection.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$upload_dir = 'uploads';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}


function handleFileUpload($file, $prefix) {
    global $upload_dir;
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $file['tmp_name'];
        $name = basename($file['name']);
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        

        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($extension, $allowed)) {
            return null;
        }
        

        $filename = $prefix . '_' . uniqid() . '.' . $extension;
        $filepath = $upload_dir . '/' . $filename;
        

        if (move_uploaded_file($tmp_name, $filepath)) {
            return $filename;
        }
    }
    return null;
}

if (isset($_POST['update_contestant'])) {
    $contestant_id = $conn->real_escape_string($_POST['contestant_id']);
    $contestant_name = $conn->real_escape_string($_POST['contestant_name']);
    $contestant_number = $conn->real_escape_string($_POST['contestant_number']);
    $fk_contestant_contest = $conn->real_escape_string($_POST['contest']);
    $fk_contestant_category = $conn->real_escape_string($_POST['category']);
    $title = $conn->real_escape_string($_POST['title']);
    $bio = $conn->real_escape_string($_POST['bio']);
    $gender = $conn->real_escape_string($_POST['gender']);

    
    $profile_image = null;
    $expanded_image = null;
    $voting_image = null;
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $profile_image = handleFileUpload($_FILES['profile_image'], 'profile');
    }
    if (isset($_FILES['expanded_image']) && $_FILES['expanded_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $expanded_image = handleFileUpload($_FILES['expanded_image'], 'expanded');
    }
    if (isset($_FILES['voting_image']) && $_FILES['voting_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $voting_image = handleFileUpload($_FILES['voting_image'], 'voting');
    }
    
   
    $image_updates = "";
    if ($profile_image) {
        $image_updates .= ", profile_image = '$profile_image'";
    }
    if ($expanded_image) {
        $image_updates .= ", expanded_image = '$expanded_image'";
    }
    if ($voting_image) {
        $image_updates .= ", voting_image = '$voting_image'";
    }

    $update_query = "UPDATE contestant_table SET 
        contestant_name = '$contestant_name',
        contestant_number = '$contestant_number',
        fk_contestant_contest = '$fk_contestant_contest',
        fk_contestant_category = '$fk_contestant_category',
        title = '$title',
        bio = '$bio',
        gender = '$gender'
        $image_updates
        WHERE contestant_id = '$contestant_id'";

    if ($conn->query($update_query)) {
      
        if (isset($_SESSION['users_id'])) {
            $action = $conn->real_escape_string("Updated contestant '$contestant_name' (#$contestant_number)");
            $log_query = "INSERT INTO logs_table (action, log_time, fk_logs_users) VALUES ('$action', NOW(), " . $_SESSION['users_id'] . ")";
            $conn->query($log_query);
        }

     
        $redirect_page = ($_SESSION['userType'] === 'Admin') ? 'admin_dashboard.php' : 'organizer.php';
        header("Location: $redirect_page?page=contestants&contestant_success=updated");
        exit();
    } else {
        $redirect_page = ($_SESSION['userType'] === 'Admin') ? 'admin_dashboard.php' : 'organizer.php';
        header("Location: $redirect_page?error=updatefail");
        exit();
    }
}

if (isset($_POST['save_contestant'])) {
    $contestant_name = $conn->real_escape_string($_POST['contestant_name']);
    $contestant_number = $conn->real_escape_string($_POST['contestant_number']);
    $fk_contestant_contest = $conn->real_escape_string($_POST['contest']);
    $fk_contestant_category = $conn->real_escape_string($_POST['category']);
    $title = $conn->real_escape_string($_POST['title']);
    $bio = $conn->real_escape_string($_POST['bio']);
    $gender = $conn->real_escape_string($_POST['gender']);
    
    $profile_image = null;
    $expanded_image = null;
    $voting_image = null;
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $profile_image = handleFileUpload($_FILES['profile_image'], 'profile');
    }
    if (isset($_FILES['expanded_image']) && $_FILES['expanded_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $expanded_image = handleFileUpload($_FILES['expanded_image'], 'expanded');
    }
    if (isset($_FILES['voting_image']) && $_FILES['voting_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $voting_image = handleFileUpload($_FILES['voting_image'], 'voting');
    }


    $check_query = "SELECT COUNT(*) as count FROM contestant_table 
                   WHERE contestant_number = '$contestant_number' 
                   AND fk_contestant_contest = '$fk_contestant_contest'";
    $result = $conn->query($check_query);
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $redirect_page = ($_SESSION['userType'] === 'Admin') ? 'admin_dashboard.php' : 'organizer.php';
        header("Location: $redirect_page?page=contestants&contestant_error=duplicate");
        exit();
    }

    $insert_query = "INSERT INTO contestant_table 
        (contestant_name, contestant_number, fk_contestant_contest, fk_contestant_category, title, bio, gender, profile_image, expanded_image, voting_image) 
        VALUES 
        ('$contestant_name', '$contestant_number', '$fk_contestant_contest', '$fk_contestant_category', '$title', '$bio', '$gender', " 
        . ($profile_image ? "'$profile_image'" : "NULL") . ", " 
        . ($expanded_image ? "'$expanded_image'" : "NULL") . ", "
        . ($voting_image ? "'$voting_image'" : "NULL") . ")";

    if ($conn->query($insert_query)) {
    
        if (isset($_SESSION['users_id'])) {
            $action = $conn->real_escape_string("Added contestant '$contestant_name' (#$contestant_number)");
            $log_query = "INSERT INTO logs_table (action, log_time, fk_logs_users) VALUES ('$action', NOW(), " . $_SESSION['users_id'] . ")";
            $conn->query($log_query);
        }

    
        $redirect_page = ($_SESSION['userType'] === 'Admin') ? 'admin_dashboard.php' : 'organizer.php';
        header("Location: $redirect_page?page=contestants&contestant_success=added");
        exit();
    } else {
        $redirect_page = ($_SESSION['userType'] === 'Admin') ? 'admin_dashboard.php' : 'organizer.php';
        header("Location: $redirect_page?contestant_error=insertfail");
        exit();
    }
}

// DELETE
if (isset($_GET['id'])) {
    $contestant_id = intval($_GET['id']);
    
    // Check for related records in score_table
    $check_query = "SELECT COUNT(*) as count FROM score_table WHERE fk_score_contestant = $contestant_id";
    $check_result = $conn->query($check_query);
    $row = $check_result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $_SESSION['error'] = "Cannot delete contestant: There are scores associated with this contestant";
        header("Location: admin_dashboard.php?page=contestants");
        exit();
    }

    try {
        // Start transaction
        $conn->begin_transaction();

        // Delete the contestant
        $query = "DELETE FROM contestant_table WHERE contestant_id = $contestant_id";
        if ($conn->query($query)) {
            // Get the maximum ID after deletion
            $max_id_query = "SELECT MAX(contestant_id) as max_id FROM contestant_table";
            $result = $conn->query($max_id_query);
            $max_id = ($result->fetch_assoc())['max_id'] ?? 0;
            
            // Reset auto-increment to max_id + 1
            $reset_auto_increment = "ALTER TABLE contestant_table AUTO_INCREMENT = " . ($max_id + 1);
            $conn->query($reset_auto_increment);

            // Commit transaction
            $conn->commit();
            
            $_SESSION['success'] = "Contestant deleted successfully!";
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = "Error deleting contestant: " . $e->getMessage();
    }
    
    header("Location: admin_dashboard.php?page=contestants");
    exit();
}

// Success and error messages
if (isset($_GET['contestant_success'])) {
    $page_to_show = 'contestants';

    if ($_GET['contestant_success'] == 'added') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Success!',
                    text: 'Contestant added successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    } elseif ($_GET['contestant_success'] == 'updated') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Updated!',
                    text: 'Contestant updated successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    } elseif ($_GET['contestant_success'] == 'deleted') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Contestant has been deleted.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    }
} elseif (isset($_GET['contestant_error'])) {
    $page_to_show = 'contestants';

    if ($_GET['contestant_error'] == 'duplicate') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Duplicate Number!',
                    text: 'The contestant number already exists for this contest.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            }
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contestant</title>
</head>
<body>
    <div class="modal fade" id="editContestantModal" tabindex="-1" aria-labelledby="editContestantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editContestantModalLabel">Edit Contestant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- CRITICAL FIX: Added enctype="multipart/form-data" for file uploads -->
                    <form action="contestant_table_query.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="edit_contestant_id" name="contestant_id">
                        
                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_contestant_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="edit_contestant_name" name="contestant_name" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_contestant_number" class="form-label">Number</label>
                                <input type="number" class="form-control" id="edit_contestant_number" name="contestant_number" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_contest" class="form-label">Contest</label>
                                <select class="form-select" id="edit_contest" name="contest" required>
                                    <?php
                                    $contest_query = "SELECT contest_id, contest_name FROM contest_table ORDER BY contest_name";
                                    $contest_result = $conn->query($contest_query);
                                    while ($contest = $contest_result->fetch_assoc()) {
                                        echo "<option value='" . $contest['contest_id'] . "'>" . htmlspecialchars($contest['contest_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_category" class="form-label">Category</label>
                                <select class="form-select" id="edit_category" name="category" required>
                                    <?php
                                    $category_query = "SELECT category_id, category_name FROM category_table ORDER BY category_name";
                                    $category_result = $conn->query($category_query);
                                    while ($category = $category_result->fetch_assoc()) {
                                        echo "<option value='" . $category['category_id'] . "'>" . htmlspecialchars($category['category_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="edit_title" name="title" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="edit_bio" name="bio" rows="4" required></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_gender" class="form-label">Gender</label>
                                <select class="form-select" id="edit_gender" name="gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>

                        <!-- Image Upload Section -->
                        <div class="row mb-3">
                            <div class="col">
                                <h6 class="fw-bold">Images</h6>
                                <div class="border p-3 rounded">
                                    <div class="mb-3">
                                        <label for="edit_profile_image" class="form-label">Profile Image</label>
                                        <input type="file" class="form-control" id="edit_profile_image" name="profile_image" accept="image/*">
                                        <small class="text-muted">Recommended size: 200x200 pixels</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_expanded_image" class="form-label">Expanded Image</label>
                                        <input type="file" class="form-control" id="edit_expanded_image" name="expanded_image" accept="image/*">
                                        <small class="text-muted">Recommended size: 400x300 pixels</small>
                                    </div>
                                    
                                    <div class="mb-0">
                                        <label for="edit_voting_image" class="form-label">Voting Image</label>
                                        <input type="file" class="form-control" id="edit_voting_image" name="voting_image" accept="image/*">
                                        <small class="text-muted">Recommended size: 800x1000 pixels (Full body photo)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="update_contestant">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Contestant Modal (if you need it) -->
    <div class="modal fade" id="addContestantModal" tabindex="-1" aria-labelledby="addContestantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addContestantModalLabel">Add New Contestant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- CRITICAL FIX: Added enctype="multipart/form-data" for file uploads -->
                    <form action="contestant_table_query.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="row mb-3">
                            <div class="col">
                                <label for="contestant_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="contestant_name" name="contestant_name" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="contestant_number" class="form-label">Number</label>
                                <input type="number" class="form-control" id="contestant_number" name="contestant_number" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="contest" class="form-label">Contest</label>
                                <select class="form-select" id="contest" name="contest" required>
                                    <?php
                                    $contest_query = "SELECT contest_id, contest_name FROM contest_table ORDER BY contest_name";
                                    $contest_result = $conn->query($contest_query);
                                    while ($contest = $contest_result->fetch_assoc()) {
                                        echo "<option value='" . $contest['contest_id'] . "'>" . htmlspecialchars($contest['contest_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category" required>
                                    <?php
                                    $category_query = "SELECT category_id, category_name FROM category_table ORDER BY category_name";
                                    $category_result = $conn->query($category_query);
                                    while ($category = $category_result->fetch_assoc()) {
                                        echo "<option value='" . $category['category_id'] . "'>" . htmlspecialchars($category['category_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4" required></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>

                        <!-- Image Upload Section -->
                        <div class="row mb-3">
                            <div class="col">
                                <h6 class="fw-bold">Images</h6>
                                <div class="border p-3 rounded">
                                    <div class="mb-3">
                                        <label for="profile_image" class="form-label">Profile Image</label>
                                        <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                        <small class="text-muted">Recommended size: 200x200 pixels</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="expanded_image" class="form-label">Expanded Image</label>
                                        <input type="file" class="form-control" id="expanded_image" name="expanded_image" accept="image/*">
                                        <small class="text-muted">Recommended size: 400x300 pixels</small>
                                    </div>
                                    
                                    <div class="mb-0">
                                        <label for="voting_image" class="form-label">Voting Image</label>
                                        <input type="file" class="form-control" id="voting_image" name="voting_image" accept="image/*">
                                        <small class="text-muted">Recommended size: 800x1000 pixels (Full body photo)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="save_contestant">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>