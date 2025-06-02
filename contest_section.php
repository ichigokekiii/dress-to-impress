<?php
// Contest Table Section
?>
<div class="modal fade" id="addContestModal" tabindex="-1" aria-labelledby="addContestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addContestModalLabel">Add Contest</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addContestForm" onsubmit="return submitAddContestForm(event)">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="contest_name" class="form-label">Contest Name</label>
                            <input type="text" class="form-control" id="contest_name" name="contest_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="contest_date" class="form-label">Contest Date</label>
                            <input type="date" class="form-control" id="contest_date" name="contest_date" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Contest</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Contest Modal -->
<div class="modal fade" id="editContestModal" tabindex="-1" aria-labelledby="editContestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editContestModalLabel">Edit Contest</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editContestForm" onsubmit="return submitEditContestForm(event)">
                    <input type="hidden" id="edit_contest_id" name="contest_id">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_contest_name" class="form-label">Contest Name</label>
                            <input type="text" class="form-control" id="edit_contest_name" name="contest_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_contest_date" class="form-label">Contest Date</label>
                            <input type="date" class="form-control" id="edit_contest_date" name="contest_date" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="edit_location" name="location" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Contest</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Contests Table -->
<input type="text" class="form-control search-box" placeholder="Search Contests..." onkeyup="searchTable('contestTable', this.value)">

<?php
$query = "SELECT * FROM contest_table ORDER BY contest_date DESC";
$query_run = $conn->query($query);
?>
<table class="table table-bordered" id="contestTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Contest Name</th>
            <th>Date</th>
            <th>Location</th>
            <th style="width: 15%;">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if ($query_run) {
        while ($row = mysqli_fetch_array($query_run)) {
            echo "<tr>";
            echo "<td>" . $row['contest_id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['contest_name']) . "</td>";
            echo "<td>" . date('M d, Y', strtotime($row['contest_date'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
            echo "<td>";
            echo "<button type='button' class='btn btn-success btn-sm me-1'
                    data-bs-toggle='modal'
                    data-bs-target='#editContestModal'
                    data-id='" . $row['contest_id'] . "'
                    data-name='" . htmlspecialchars($row['contest_name'], ENT_QUOTES) . "'
                    data-date='" . $row['contest_date'] . "'
                    data-location='" . htmlspecialchars($row['location'], ENT_QUOTES) . "'
                    onclick='populateEditContestModal(this)'>
                    Edit
                </button>";
            echo "<button type='button' class='btn btn-danger btn-sm' onclick='confirmDeleteContest(" . $row['contest_id'] . ")'>Delete</button>";
            echo "</td>";
            echo "</tr>";
        }
    }
    ?>
    </tbody>
</table>

<script>
function submitAddContestForm(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('addContestForm'));
    formData.append('save_contest', '1');

    fetch('contest_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Clear form
        document.getElementById('addContestForm').reset();
        // Hide modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('addContestModal'));
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
            text: 'Failed to add contest. Please try again.',
            icon: 'error'
        });
    });

    return false;
}

function submitEditContestForm(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('editContestForm'));
    formData.append('update_contest', '1');

    fetch('contest_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Hide modal
        $('#editContestModal').modal('hide');
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
            text: 'Failed to update contest. Please try again.',
            icon: 'error'
        });
    });

    return false;
}

function confirmDeleteContest(id) {
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
            fetch('contest_handler.php?delete_contest=' + id)
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
                    text: 'Failed to delete contest. Please try again.',
                    icon: 'error'
                });
            });
        }
    });
}

function populateEditContestModal(element) {
    // Get the modal element
    const modal = document.getElementById('editContestModal');
    
    // Get all the values from data attributes
    const id = element.getAttribute('data-id');
    const name = element.getAttribute('data-name');
    const date = element.getAttribute('data-date');
    const location = element.getAttribute('data-location');
    
    // Set the values in the form fields
    modal.querySelector('#edit_contest_id').value = id;
    modal.querySelector('#edit_contest_name').value = name;
    modal.querySelector('#edit_contest_date').value = date;
    modal.querySelector('#edit_location').value = location;
}
</script> 