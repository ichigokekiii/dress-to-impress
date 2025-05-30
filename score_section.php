<?php
// Score Table
?>
<input type="text" class="form-control search-box" placeholder="Search Scores..." onkeyup="searchTable('scoreTable', this.value)">

<?php
$query = "SELECT s.*, j.judge_name, c.contestant_name, cr.criteria_name 
          FROM score_table s
          LEFT JOIN judge_table j ON s.fk_score_judge = j.judge_id
          LEFT JOIN contestant_table c ON s.fk_score_contestant = c.contestant_id
          LEFT JOIN criteria_table cr ON s.fk_score_criteria = cr.criteria_id
          ORDER BY j.judge_name, c.contestant_name";
$query_run = $conn->query($query);
?>
<table class="table table-bordered" id="scoreTable">
    <thead>
        <tr>
            <th>Judge</th>
            <th>Contestant</th>
            <th>Criteria</th>
            <th>Score</th>
            <th style="width: 15%;">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if ($query_run) {
        while ($row = mysqli_fetch_array($query_run)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['judge_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['contestant_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['criteria_name']) . "</td>";
            echo "<td>" . $row['score_value'] . "</td>";
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