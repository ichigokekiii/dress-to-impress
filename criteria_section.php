<?php
// Criteria Table
?>
<input type="text" class="form-control search-box" placeholder="Search Criteria..." onkeyup="searchTable('criteriaTable', this.value)">

<?php
$query = "SELECT * FROM criteria_table ORDER BY criteria_name";
$query_run = $conn->query($query);
?>
<table class="table table-bordered" id="criteriaTable">
    <thead>
        <tr>
            <th>Criteria Name</th>
            <th>Weight</th>
            <th style="width: 15%;">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if ($query_run) {
        while ($row = mysqli_fetch_array($query_run)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['criteria_name']) . "</td>";
            echo "<td>" . $row['weight'] . "</td>";
            echo "<td>";
            echo "<a href='#' class='btn btn-success btn-sm me-1'>Edit</a>";
            echo "<a href='#' class='btn btn-danger btn-sm'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
    }
    ?>
    </tbody>
</table> 