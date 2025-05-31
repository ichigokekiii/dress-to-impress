<?php
// Add Judge Modal
?>
<div class="modal fade" id="addJudgeModal" tabindex="-1" aria-labelledby="addJudgeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addJudgeModalLabel">Add Judge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="judge_table_query.php" method="POST">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="judge_name" class="form-label">Judge Name</label>
                            <input type="text" class="form-control" id="judge_name" name="judge_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="contact_information" class="form-label">Contact Information</label>
                            <input type="text" class="form-control" id="contact_information" name="contact_information" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="save_judge">Save Judge</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Judge Modal -->
<div class="modal fade" id="editJudgeModal" tabindex="-1" aria-labelledby="editJudgeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editJudgeModalLabel">Edit Judge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="judge_table_query.php" method="POST">
                    <input type="hidden" id="edit_judge_id" name="judge_id">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_judge_name" class="form-label">Judge Name</label>
                            <input type="text" class="form-control" id="edit_judge_name" name="judge_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_contact_information" class="form-label">Contact Information</label>
                            <input type="text" class="form-control" id="edit_contact_information" name="contact_information" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="update_judge">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Judges Table -->
<input type="text" class="form-control search-box" placeholder="Search Judge..." onkeyup="searchTable('judgeTable', this.value)">

<?php
$query = "SELECT * FROM judge_table";
$query_run = $conn->query($query);
?>
<table class="table table-bordered" id="judgeTable">
    <thead>
        <th>Judge Id</th>
        <th>Judge Name</th>
        <th>Contact Information</th>
        <th style="width: 15%;">Action</th>
    </thead>
    <tbody>
    <?php
    if ($query_run) {
        while ($row = mysqli_fetch_array($query_run)) {
            echo "<tr>";
            echo "<td>" . $row['judge_id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['judge_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['contact_information']) . "</td>";
            echo "<td>";
            echo "<a href='#' class='btn btn-success btn-sm me-1'
                    data-bs-toggle='modal'
                    data-bs-target='#editJudgeModal'
                    data-id='" . $row['judge_id'] . "'
                    data-name='" . htmlspecialchars($row['judge_name'], ENT_QUOTES) . "'
                    data-info='" . htmlspecialchars($row['contact_information'], ENT_QUOTES) . "'
                    onclick='populateEditJudgeModal(this)'>
                    Edit
                </a>";
            echo "<a href='#' class='btn btn-danger btn-sm' onclick='confirmDeleteJudge(" . $row['judge_id'] . ")'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
    }
    ?>
    </tbody>
</table> 