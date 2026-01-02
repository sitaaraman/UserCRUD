<?php
include "config.php";

/* CONFIRM DELETE */
if (isset($_POST['confirm_delete'])) {
    mysqli_query($conn, "DELETE FROM hobbies WHERE id=" . $_POST['id']);
    header("Location: hobbies.php");
    exit;
}

/* BULK DELETE */
if (isset($_POST['bulk_delete'])) {
    $ids = implode(",", $_POST['ids'] ?? []);
    mysqli_query($conn, "DELETE FROM hobbies WHERE id IN ($ids)");
    header("Location: hobbies.php");
    exit;
}

/* ADD / UPDATE */
if (isset($_POST['save'])) {
    $hobby = mysqli_real_escape_string($conn, $_POST['hobby']);

    if ($_POST['id']) {
        mysqli_query($conn, "UPDATE hobbies SET hobby='$hobby' WHERE id=" . $_POST['id']);
    } else {
        mysqli_query($conn, "INSERT INTO hobbies (hobby) VALUES ('$hobby')");
    }
    header("Location: hobbies.php");
    exit;
}

/* EDIT */
$edit = ['id' => '', 'hobby' => ''];
if (isset($_GET['edit'])) {
    $edit = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM hobbies WHERE id=" . $_GET['edit'])
    );
}

$data = mysqli_query($conn, "SELECT * FROM hobbies");
?>

<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">

        <!-- CONFIRM DELETE MESSAGE -->
        <?php if (isset($_GET['confirm'])) { ?>
            <div class="alert alert-danger">
                Are you sure you want to delete this hobby?
                <form method="post" class="d-inline">
                    <input type="hidden" name="id" value="<?= $_GET['confirm'] ?>">
                    <button name="confirm_delete" class="btn btn-danger btn-sm">Yes</button>
                    <a href="hobbies.php" class="btn btn-secondary btn-sm">No</a>
                </form>
            </div>
        <?php } ?>

        <form method="post">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">Hobby List</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th><input type="checkbox" name="ids[]"></th>
                            <th>Hobby</th>
                            <th>Action</th>
                        </tr>

                        <?php while ($row = mysqli_fetch_assoc($data)) { ?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>"></td>
                                <td><?= $row['hobby'] ?></td>
                                <td>
                                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="?confirm=<?= $row['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>

                    <button name="bulk_delete" class="btn btn-danger">Bulk Delete</button>
                </div>
            </div>
        </form>

        <!-- ADD / EDIT FORM -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <?= $edit['id'] ? 'Edit Hobby' : 'Add Hobby' ?>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                    <input type="text" name="hobby" value="<?= $edit['hobby'] ?>" class="form-control mb-2" required>
                    <button name="save" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>

    </div>
</body>

</html>