<?php
// Add Contestant Modal
?>
<div class="modal fade" id="addContestantModal" tabindex="-1" aria-labelledby="addContestantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addContestantModalLabel">Add Contestant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="contestant_table_query.php" method="POST">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="contestant_name" class="form-label">Contestant Name</label>
                            <input type="text" class="form-control" id="contestant_name" name="contestant_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="contestant_number" class="form-label">Contestant Number</label>
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
                    <div class="row mt-4">
                        <div class="col text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="save_contestant">Save Contestant</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Contestant Modal -->
<div class="modal fade" id="editContestantModal" tabindex="-1" aria-labelledby="editContestantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editContestantModalLabel">Edit Contestant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="contestant_table_query.php" method="POST">
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

<!-- Contestants Table -->
<input type="text" class="form-control search-box" placeholder="Search Contestants..." onkeyup="searchTable('contestantTable', this.value)">

<?php
$query = "SELECT c.*, ct.contest_name, cat.category_name 
         FROM contestant_table c
         LEFT JOIN contest_table ct ON c.fk_contestant_contest = ct.contest_id
         LEFT JOIN category_table cat ON c.fk_contestant_category = cat.category_id
         ORDER BY ct.contest_name, c.contestant_number";
$query_run = $conn->query($query);
?>
<table class="table table-bordered" id="contestantTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Contest</th>
            <th>Number</th>
            <th>Name</th>
            <th>Category</th>
            <th>Title</th>
            <th>Bio</th>
            <th>Gender</th>
            <th style="width: 15%;">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if ($query_run) {
        while ($row = mysqli_fetch_array($query_run)) {
            echo "<tr>";
            echo "<td>" . $row['contestant_id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['contest_name']) . "</td>";
            echo "<td>" . $row['contestant_number'] . "</td>";
            echo "<td>" . htmlspecialchars($row['contestant_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['bio']) . "</td>";
            echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
            echo "<td>";
            echo "<a href='#' class='btn btn-success btn-sm me-1'
                    data-bs-toggle='modal'
                    data-bs-target='#editContestantModal'
                    data-id='" . $row['contestant_id'] . "'
                    data-name='" . htmlspecialchars($row['contestant_name'], ENT_QUOTES) . "'
                    data-number='" . $row['contestant_number'] . "'
                    data-contest='" . $row['fk_contestant_contest'] . "'
                    data-category='" . $row['fk_contestant_category'] . "'
                    data-title='" . htmlspecialchars($row['title'], ENT_QUOTES) . "'
                    data-bio='" . htmlspecialchars($row['bio'], ENT_QUOTES) . "'
                    data-gender='" . htmlspecialchars($row['gender'], ENT_QUOTES) . "'
                    onclick='populateEditModal(this)'>
                    Edit
                </a>";
            echo "<a href='#' class='btn btn-danger btn-sm' onclick='confirmDeleteContestant(" . $row['contestant_id'] . ")'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
    }
    ?>
    </tbody>
</table> 