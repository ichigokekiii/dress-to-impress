<?php
// Add Category Modal
?>
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="category_table_query.php" method="POST">
                    <div class="row">
                        <div class="col">
                            <label for="category_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="submit">Save Category</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="category_table_query.php" method="POST">
                    <input type="hidden" id="edit_category_id" name="category_id">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_category_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="update">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Categories Table -->
<input type="text" class="form-control search-box" placeholder="Search Category..." onkeyup="searchTable('categoryTable', this.value)">

<?php
$query = "SELECT * FROM category_table";
$query_run = $conn->query($query);
?>
<table class="table table-bordered" id="categoryTable">
    <thead>
        <th style="width: 10%;">Category Id</th>
        <th>Category Name</th>
        <th style="width: 15%;">Action</th>
    </thead>
    <tbody>
    <?php
    if ($query_run) {
        while ($row = mysqli_fetch_array($query_run)) {
            echo "<tr>";
            echo "<td>" . $row['category_id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
            echo "<td>";
            echo "<a href='#' class='btn btn-success btn-sm me-1'
                    data-bs-toggle='modal'
                    data-bs-target='#editCategoryModal'
                    data-id='" . $row['category_id'] . "'
                    data-name='" . htmlspecialchars($row['category_name'], ENT_QUOTES) . "'
                    onclick='populateEditCategoryModal(this)'>
                    Edit
                </a>";
            echo "<a href='#' class='btn btn-danger btn-sm' onclick='confirmDeleteCategory(" . $row['category_id'] . ")'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
    }
    ?>
    </tbody>
</table> 