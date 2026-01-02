<?php
include "config.php";

/* CONFIRM DELETE */
if (isset($_POST['confirm_delete'])) {
    $id = $_POST['id'];
    mysqli_query($conn, "DELETE FROM category WHERE id=$id");
    header("Location: categories.php");
    exit;
}

/* BULK DELETE */
if (isset($_POST['bulk_delete']) && !empty($_POST['ids'])) {
    $ids = implode(",", $_POST['ids']);
    mysqli_query($conn, "DELETE FROM category WHERE id IN ($ids)");
    header("Location: categories.php");
    exit;
}

/* ADD / UPDATE CATEGORY */
if (isset($_POST['save'])) {
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    if (!empty($_POST['id'])) {
        $id = $_POST['id'];
        mysqli_query($conn, "UPDATE category SET category='$category' WHERE id=$id");
    } else {
        mysqli_query($conn, "INSERT INTO category (category) VALUES ('$category')");
    }

    header("Location: categories.php");
    exit;
}

/* EDIT FETCH */
$edit = ['id' => '', 'category' => ''];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM category WHERE id=$id")
    );
}

/* FETCH ALL CATEGORIES */
$data = mysqli_query($conn, "SELECT * FROM category");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Category Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

    <!-- CONFIRM DELETE -->
    <?php if (isset($_GET['confirm'])) { ?>
        <div class="alert alert-danger">
            Are you sure you want to delete this category?
            <form method="post" class="d-inline">
                <input type="hidden" name="id" value="<?= $_GET['confirm']; ?>">
                <button name="confirm_delete" class="btn btn-danger btn-sm">Yes</button>
                <a href="categories.php" class="btn btn-secondary btn-sm">No</a>
            </form>
        </div>
    <?php } ?>

    <!-- CATEGORY LIST -->
    <form method="post">
        <div class="card mb-4 shadow">
            <div class="card-header bg-dark text-white">
                Category List
            </div>

            <div class="card-body">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th width="50">
                                Select
                            </th>
                            <th>Category Name</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($data)) { ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="ids[]" value="<?= $row['id']; ?>">
                                </td>
                                <td><?= $row['category']; ?></td>
                                <td>
                                    <a href="?edit=<?= $row['id']; ?>" class="btn btn-warning btn-sm">
                                        Edit
                                    </a>
                                    <a href="?confirm=<?= $row['id']; ?>" class="btn btn-danger btn-sm">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <button name="bulk_delete" class="btn btn-danger">
                    Bulk Delete
                </button>
            </div>
        </div>
    </form>

    <!-- ADD / EDIT FORM -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <?= $edit['id'] ? 'Edit Category' : 'Add Category'; ?>
        </div>

        <div class="card-body">
            <form method="post">
                <input type="hidden" name="id" value="<?= $edit['id']; ?>">

                <div class="mb-3">
                    <label class="form-label">Category Name</label>
                    <input type="text"
                           name="category"
                           value="<?= $edit['category']; ?>"
                           class="form-control"
                           required>
                </div>

                <button name="save" class="btn btn-success">
                    Save Category
                </button>
                <a href="categories.php" class="btn btn-secondary">
                    Reset
                </a>
            </form>
        </div>
    </div>

</div>

</body>
</html>
