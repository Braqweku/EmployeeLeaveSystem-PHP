<?php
session_start();
error_reporting(0);
include('../includes/dbconn.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    // HOD Action Logic
    if (isset($_POST['hod_action'])) {
        $leaveid = intval($_POST['leaveid']);
        $hod_status = intval($_POST['hod_status']);
        
        $sql = "UPDATE tblleaves SET HOD_Status = :hod_status WHERE id = :leaveid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':hod_status', $hod_status, PDO::PARAM_INT);
        $query->bindParam(':leaveid', $leaveid, PDO::PARAM_INT);
        $query->execute();

        $msg = $hod_status == 1 ? "Leave request approved by HOD" : "Leave request disagreed by HOD";
    }

    // Admin Action Logic
    if (isset($_POST['admin_action'])) {
        $leaveid = intval($_POST['leaveid']);
        $admin_status = intval($_POST['admin_status']);
        
        $sql = "UPDATE tblleaves SET Status = :admin_status WHERE id = :leaveid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':admin_status', $admin_status, PDO::PARAM_INT);
        $query->bindParam(':leaveid', $leaveid, PDO::PARAM_INT);
        $query->execute();

        $msg = $admin_status == 1 ? "Leave request approved by Admin" : "Leave request declined by Admin";
    }
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>HOD Panel - Employee Leave</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Add stylesheets -->
</head>
<body>
    <div class="main-content-inner">
        <!-- HOD Approval Section -->
        <div class="row">
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">HOD Actions</h4>
                        <?php if ($msg) { ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <strong>Info: </strong><?php echo htmlentities($msg); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php } ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered text-center">
                                <thead>
                                    <tr>
                                        <td>S.N</td>
                                        <td>Employee ID</td>
                                        <td>Full Name</td>
                                        <td>Leave Type</td>
                                        <td>Applied On</td>
                                        <td>HOD Status</td>
                                        <td>Actions</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $sql = "SELECT tblleaves.id as lid, tblemployees.FirstName, tblemployees.LastName, tblemployees.EmpId, 
                                            tblleaves.LeaveType, tblleaves.PostingDate, tblleaves.HOD_Status 
                                            FROM tblleaves 
                                            JOIN tblemployees ON tblleaves.empid = tblemployees.id 
                                            WHERE tblleaves.Status = 0 
                                            ORDER BY tblleaves.id DESC";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    $cnt = 1;

                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $result) { 
                                    ?>
                                    <tr>
                                        <td><?php echo htmlentities($cnt); ?></td>
                                        <td><?php echo htmlentities($result->EmpId); ?></td>
                                        <td><?php echo htmlentities($result->FirstName . " " . $result->LastName); ?></td>
                                        <td><?php echo htmlentities($result->LeaveType); ?></td>
                                        <td><?php echo htmlentities($result->PostingDate); ?></td>
                                        <td>
                                            <?php
                                            if ($result->HOD_Status == 0) {
                                                echo '<span style="color: blue">Pending</span>';
                                            } elseif ($result->HOD_Status == 1) {
                                                echo '<span style="color: green">Approved</span>';
                                            } else {
                                                echo '<span style="color: red">Disagreed</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($result->HOD_Status == 0) { ?>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="leaveid" value="<?php echo htmlentities($result->lid); ?>">
                                                    <button type="submit" name="hod_action" value="1" class="btn btn-success btn-sm">Approve</button>
                                                    <button type="submit" name="hod_action" value="2" class="btn btn-danger btn-sm">Disagree</button>
                                                </form>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php $cnt++; }} ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Approval Section -->
        <div class="row">
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Admin Actions</h4>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered text-center">
                                <thead>
                                    <tr>
                                        <td>S.N</td>
                                        <td>Employee ID</td>
                                        <td>Full Name</td>
                                        <td>Leave Type</td>
                                        <td>Applied On</td>
                                        <td>HOD Status</td>
                                        <td>Actions</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $sql = "SELECT tblleaves.id as lid, tblemployees.FirstName, tblemployees.LastName, tblemployees.EmpId, 
                                            tblleaves.LeaveType, tblleaves.PostingDate, tblleaves.HOD_Status 
                                            FROM tblleaves 
                                            JOIN tblemployees ON tblleaves.empid = tblemployees.id 
                                            WHERE tblleaves.HOD_Status = 1 AND tblleaves.Status = 0 
                                            ORDER BY tblleaves.id DESC";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    $cnt = 1;

                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $result) { 
                                    ?>
                                    <tr>
                                        <td><?php echo htmlentities($cnt); ?></td>
                                        <td><?php echo htmlentities($result->EmpId); ?></td>
                                        <td><?php echo htmlentities($result->FirstName . " " . $result->LastName); ?></td>
                                        <td><?php echo htmlentities($result->LeaveType); ?></td>
                                        <td><?php echo htmlentities($result->PostingDate); ?></td>
                                        <td>
                                            <span style="color: green">Approved by HOD</span>
                                        </td>
                                        <td>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="leaveid" value="<?php echo htmlentities($result->lid); ?>">
                                                <button type="submit" name="admin_action" value="1" class="btn btn-success btn-sm">Approve</button>
                                                <button type="submit" name="admin_action" value="2" class="btn btn-danger btn-sm">Decline</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php $cnt++; }} ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php } ?>
