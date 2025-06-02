<?php
// Score Table Section
?>
<div class="modal fade" id="addScoreModal" tabindex="-1" aria-labelledby="addScoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addScoreModalLabel">Add Score</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addScoreForm" onsubmit="return submitAddScoreForm(event)">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="judge" class="form-label">Judge</label>
                            <select class="form-select" id="judge" name="fk_score_judge" required>
                                <?php
                                $judge_query = "SELECT judge_id, judge_name FROM judge_table ORDER BY judge_name";
                                $judge_result = $conn->query($judge_query);
                                while ($judge = $judge_result->fetch_assoc()) {
                                    echo "<option value='" . $judge['judge_id'] . "'>" . htmlspecialchars($judge['judge_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="contestant" class="form-label">Contestant</label>
                            <select class="form-select" id="contestant" name="fk_score_contestant" required>
                                <?php
                                $contestant_query = "SELECT contestant_id, contestant_name, contestant_number FROM contestant_table ORDER BY contestant_number";
                                $contestant_result = $conn->query($contestant_query);
                                while ($contestant = $contestant_result->fetch_assoc()) {
                                    echo "<option value='" . $contestant['contestant_id'] . "'>#" . $contestant['contestant_number'] . " - " . htmlspecialchars($contestant['contestant_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="criteria" class="form-label">Criteria</label>
                            <select class="form-select" id="criteria" name="fk_score_criteria" required>
                                <?php
                                $criteria_query = "SELECT criteria_id, criteria_name FROM criteria_table ORDER BY criteria_name";
                                $criteria_result = $conn->query($criteria_query);
                                while ($criteria = $criteria_result->fetch_assoc()) {
                                    echo "<option value='" . $criteria['criteria_id'] . "'>" . htmlspecialchars($criteria['criteria_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="score_value" class="form-label">Score</label>
                            <input type="number" class="form-control" id="score_value" name="score_value" min="1" max="100" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Score</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<input type="text" class="form-control search-box" placeholder="Search Scores..." onkeyup="searchTable('scoreTable', this.value)">

<?php
$query = "SELECT s.*, j.judge_name, c.contestant_name, c.contestant_number, cr.criteria_name, cr.max_score 
          FROM score_table s
          LEFT JOIN judge_table j ON s.fk_score_judge = j.judge_id
          LEFT JOIN contestant_table c ON s.fk_score_contestant = c.contestant_id
          LEFT JOIN criteria_table cr ON s.fk_score_criteria = cr.criteria_id
          ORDER BY j.judge_name, c.contestant_number";
$query_run = $conn->query($query);
?>
<table class="table table-bordered" id="scoreTable">
    <thead>
        <tr>
            <th>Judge</th>
            <th>Contestant</th>
            <th>Criteria</th>
            <th>Score</th>
            <th>Max Score</th>
            <th>Remarks</th>
            <th style="width: 15%;">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if ($query_run) {
        while ($row = mysqli_fetch_array($query_run)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['judge_name']) . "</td>";
            echo "<td>#" . $row['contestant_number'] . " - " . htmlspecialchars($row['contestant_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['criteria_name']) . "</td>";
            echo "<td>" . $row['score_value'] . "</td>";
            echo "<td>" . $row['max_score'] . "</td>";
            echo "<td>" . htmlspecialchars($row['remarks'] ?? '') . "</td>";
            echo "<td>";
            echo "<button type='button' class='btn btn-success btn-sm me-1'
                    data-bs-toggle='modal'
                    data-bs-target='#editScoreModal'
                    data-id='" . $row['score_id'] . "'
                    data-judge='" . $row['fk_score_judge'] . "'
                    data-contestant='" . $row['fk_score_contestant'] . "'
                    data-criteria='" . $row['fk_score_criteria'] . "'
                    data-score='" . $row['score_value'] . "'
                    data-remarks='" . htmlspecialchars($row['remarks'] ?? '', ENT_QUOTES) . "'
                    onclick='populateEditScoreModal(this)'>
                    Edit
                </button>";
            echo "<button type='button' class='btn btn-danger btn-sm' onclick='confirmDeleteScore(" . $row['score_id'] . ")'>Delete</button>";
            echo "</td>";
            echo "</tr>";
        }
    }
    ?>
    </tbody>
</table>

<!-- Edit Score Modal -->
<div class="modal fade" id="editScoreModal" tabindex="-1" aria-labelledby="editScoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editScoreModalLabel">Edit Score</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editScoreForm" onsubmit="return submitEditScoreForm(event)">
                    <input type="hidden" id="edit_score_id" name="score_id">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_judge" class="form-label">Judge</label>
                            <select class="form-select" id="edit_judge" name="fk_score_judge" required>
                                <?php
                                $judge_result->data_seek(0);
                                while ($judge = $judge_result->fetch_assoc()) {
                                    echo "<option value='" . $judge['judge_id'] . "'>" . htmlspecialchars($judge['judge_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_contestant" class="form-label">Contestant</label>
                            <select class="form-select" id="edit_contestant" name="fk_score_contestant" required>
                                <?php
                                $contestant_result->data_seek(0);
                                while ($contestant = $contestant_result->fetch_assoc()) {
                                    echo "<option value='" . $contestant['contestant_id'] . "'>#" . $contestant['contestant_number'] . " - " . htmlspecialchars($contestant['contestant_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_criteria" class="form-label">Criteria</label>
                            <select class="form-select" id="edit_criteria" name="fk_score_criteria" required>
                                <?php
                                $criteria_result->data_seek(0);
                                while ($criteria = $criteria_result->fetch_assoc()) {
                                    echo "<option value='" . $criteria['criteria_id'] . "'>" . htmlspecialchars($criteria['criteria_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_score_value" class="form-label">Score</label>
                            <input type="number" class="form-control" id="edit_score_value" name="score_value" min="1" max="100" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="edit_remarks" name="remarks" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Score</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function submitAddScoreForm(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('addScoreForm'));
    formData.append('save_score', '1');

    fetch('score_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Clear form
        document.getElementById('addScoreForm').reset();
        // Hide modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('addScoreModal'));
        modal.hide();
        // Show message
        Swal.fire({
            title: data.status === 'success' ? 'Success!' : 'Error!',
            text: data.message,
            icon: data.status
        }).then((result) => {
            if (data.status === 'success') {
                location.reload();
            }
        });
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to add score. Please try again.',
            icon: 'error'
        });
    });

    return false;
}

function submitEditScoreForm(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('editScoreForm'));
    formData.append('update_score', '1');

    fetch('score_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Hide modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('editScoreModal'));
        modal.hide();
        // Show message
        Swal.fire({
            title: data.status === 'success' ? 'Success!' : 'Error!',
            text: data.message,
            icon: data.status
        }).then((result) => {
            if (data.status === 'success') {
                location.reload();
            }
        });
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to update score. Please try again.',
            icon: 'error'
        });
    });

    return false;
}

function confirmDeleteScore(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('score_handler.php?delete_score=' + id)
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: data.status === 'success' ? 'Deleted!' : 'Error!',
                    text: data.message,
                    icon: data.status
                }).then(() => {
                    if (data.status === 'success') {
                        location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to delete score. Please try again.',
                    icon: 'error'
                });
            });
        }
    });
}

function populateEditScoreModal(element) {
    // Get the modal element
    const modal = document.getElementById('editScoreModal');
    
    // Get all the values from data attributes
    const id = element.getAttribute('data-id');
    const judge = element.getAttribute('data-judge');
    const contestant = element.getAttribute('data-contestant');
    const criteria = element.getAttribute('data-criteria');
    const score = element.getAttribute('data-score');
    const remarks = element.getAttribute('data-remarks');
    
    // Set the values in the form fields
    modal.querySelector('#edit_score_id').value = id;
    modal.querySelector('#edit_judge').value = judge;
    modal.querySelector('#edit_contestant').value = contestant;
    modal.querySelector('#edit_criteria').value = criteria;
    modal.querySelector('#edit_score_value').value = score;
    modal.querySelector('#edit_remarks').value = remarks;
}
</script> 