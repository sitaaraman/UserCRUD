<?php
include "config.php";

/* ================= DELETE CONFIRM ================= */
if (isset($_POST['confirm_delete'])) {
    $id = (int)$_POST['id'];

    // remove profile pic file
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT profile_pic FROM users WHERE id=$id"));
    if (!empty($r['profile_pic']) && file_exists("uploads/" . $r['profile_pic'])) {
        unlink("uploads/" . $r['profile_pic']);
    }

    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: users.php");
    exit;
}

/* ================= BULK DELETE ================= */
if (isset($_POST['bulk_delete']) && !empty($_POST['ids'])) {
    foreach ($_POST['ids'] as $id) {
        $id = (int)$id;
        $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT profile_pic FROM users WHERE id=$id"));
        if (!empty($r['profile_pic']) && file_exists("uploads/" . $r['profile_pic'])) {
            unlink("uploads/" . $r['profile_pic']);
        }
    }
    $ids = implode(",", array_map('intval', $_POST['ids']));
    mysqli_query($conn, "DELETE FROM users WHERE id IN ($ids)");
    header("Location: users.php");
    exit;
}

/* ================= ADD / UPDATE USER ================= */
if (isset($_POST['save'])) {

    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $contact  = mysqli_real_escape_string($conn, $_POST['contact_no']);
    $category = (int)$_POST['category_id'];
    $hobbies  = isset($_POST['hobby_id']) ? implode(",", $_POST['hobby_id']) : "";

    /* ---- PROFILE PIC ---- */
    $profile_pic = $_POST['old_profile_pic'] ?? "";

    if (!empty($_FILES['profile_pic']['name'])) {

        // delete old pic if exists
        if (!empty($profile_pic) && file_exists("uploads/" . $profile_pic)) {
            unlink("uploads/" . $profile_pic);
        }

        $profile_pic = time() . "_" . basename($_FILES['profile_pic']['name']);
        move_uploaded_file(
            $_FILES['profile_pic']['tmp_name'],
            "uploads/" . $profile_pic
        );
    }

    if (!empty($_POST['id'])) {
        $id = (int)$_POST['id'];

        mysqli_query($conn, "
            UPDATE users SET
                name='$name',
                contact_no='$contact',
                hobby_id='$hobbies',
                category_id='$category',
                profile_pic='$profile_pic'
            WHERE id=$id
        ");
    } else {
        mysqli_query($conn, "
            INSERT INTO users
                (name, contact_no, hobby_id, category_id, profile_pic)
            VALUES
                ('$name','$contact','$hobbies','$category','$profile_pic')
        ");
    }

    header("Location: users.php");
    exit;
}

/* ================= EDIT FETCH ================= */
$edit = [
    'id' => '',
    'name' => '',
    'contact_no' => '',
    'hobby_id' => '',
    'category_id' => '',
    'profile_pic' => ''
];

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM users WHERE id=$id")
    );
}

/* ================= FETCH DATA ================= */
$users = mysqli_query($conn, "
    SELECT users.*, category.category
    FROM users
    LEFT JOIN category ON category.id = users.category_id
");

$hobbyData = [];
$res = mysqli_query($conn, "SELECT * FROM hobbies");
while ($r = mysqli_fetch_assoc($res)) {
    $hobbyData[$r['id']] = $r['hobby'];
}

$catRes = mysqli_query($conn, "SELECT * FROM category");
?>

<!DOCTYPE html>
<html>

<head>
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">

        <!-- CONFIRM DELETE -->
        <?php if (isset($_GET['confirm'])) { ?>
            <div class="alert alert-danger">
                Are you sure you want to delete this user?
                <form method="post" class="d-inline">
                    <input type="hidden" name="id" value="<?= (int)$_GET['confirm'] ?>">
                    <button name="confirm_delete" class="btn btn-danger btn-sm">Yes</button>
                    <a href="users.php" class="btn btn-secondary btn-sm">No</a>
                </form>
            </div>
        <?php } ?>

        <!-- USER TABLE -->
        <form method="post">
            <div class="card mb-4 shadow">
                <div class="card-header bg-dark text-white">User List</div>
                <div class="card-body">

                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th>Select</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Hobbies</th>
                                <th>Category</th>
                                <th>Profile</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php while ($u = mysqli_fetch_assoc($users)) { ?>
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="<?= $u['id'] ?>"></td>
                                    <td><?= $u['name'] ?></td>
                                    <td><?= $u['contact_no'] ?></td>
                                    <td>
                                        <?php
                                        $ids = explode(",", $u['hobby_id']);
                                        $names = [];

                                        foreach ($ids as $i) {
                                            if (isset($hobbyData[$i])) {
                                                $names[] = $hobbyData[$i];
                                            }
                                        }

                                        echo implode(", ", $names);
                                        ?>
                                    </td>
                                    <td><?= $u['category'] ?></td>
                                    <td>
                                        <?php if ($u['profile_pic']) { ?>
                                            <img src="uploads/<?= $u['profile_pic'] ?>" width="60" class="rounded">
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a href="?edit=<?= $u['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="?confirm=<?= $u['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <button name="bulk_delete" class="btn btn-danger">Bulk Delete</button>

                </div>
            </div>
        </form>

        <!-- ADD / EDIT FORM -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <?= $edit['id'] ? 'Edit User' : 'Add User' ?>
            </div>
            <div class="card-body">

                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                    <input type="hidden" name="old_profile_pic" value="<?= $edit['profile_pic'] ?>">

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" value="<?= $edit['name'] ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Contact</label>
                        <input type="text" name="contact_no" value="<?= $edit['contact_no'] ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Hobbies</label>
                        <div class="row">
                            <?php foreach ($hobbyData as $id => $h) { ?>
                                <div class="col-md-3">
                                    <input type="checkbox" name="hobby_id[]" value="<?= $id ?>"
                                        <?= in_array($id, explode(",", $edit['hobby_id'])) ? 'checked' : '' ?>>
                                    <?= $h ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select</option>
                            <?php while ($c = mysqli_fetch_assoc($catRes)) { ?>
                                <option value="<?= $c['id'] ?>" <?= ($edit['category_id'] == $c['id']) ? 'selected' : '' ?>>
                                    <?= $c['category'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Profile Picture</label>
                        <input type="file" name="profile_pic" class="form-control">
                        <?php if ($edit['profile_pic']) { ?>
                            <img src="uploads/<?= $edit['profile_pic'] ?>" width="80" class="mt-2 rounded border">
                        <?php } ?>
                    </div>

                    <button name="save" class="btn btn-success">Save User</button>
                    <a href="users.php" class="btn btn-secondary">Reset</a>

                </form>
            </div>
        </div>

    </div>

</body>

</html>