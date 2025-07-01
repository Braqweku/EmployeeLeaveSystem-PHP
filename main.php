<?php
session_start();
error_reporting(0);

// Check if the user is already logged in
if (isset($_SESSION['emplogin'])) {
    // If logged in, destroy session and force logout
    session_unset();
    session_destroy();
}

// Start a new session after destroying the previous one
session_start();

include('includes/dbconn.php');

// Set the session timeout duration (e.g., 600 seconds for 10 minutes)
$timeout_duration = 300;

// Check for session timeout on each page load
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
    // If the session has timed out, destroy it and redirect to login
    session_unset();
    session_destroy();
    echo "<script type='text/javascript'>alert('Session expired, please login again.');</script>";
    echo "<script type='text/javascript'> document.location = 'index.php'; </script>";
    exit;
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

if (isset($_POST['signin'])) {
    $uname = $_POST['username'];
    $password = md5($_POST['password']);
    $sql = "SELECT EmailId, Password, Status, id FROM tblemployees WHERE EmailId=:uname and Password=:password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uname', $uname, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            $status = $result->Status;
            $_SESSION['eid'] = $result->id;
        }
        if ($status == 0) {
            $msg = "In-Active Account. Please contact your administrator!";
        } else {
            $_SESSION['emplogin'] = $_POST['username'];
            // Set last activity time on successful login
            $_SESSION['last_activity'] = time();
            echo "<script type='text/javascript'> document.location = 'employees/leave.php'; </script>";
        }
    } else {
        echo "<script>alert('Sorry, Invalid Details.');</script>";
    }
}
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Employee Leave Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/metisMenu.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.min.css">
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <link rel="stylesheet" href="assets/css/typography.css">
    <link rel="stylesheet" href="assets/css/default-css.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <script src="assets/js/vendor/modernizr-2.8.3.min.js"></script>
</head>
<body>
    <style>
        /* CSS styling */
.button-container {
  display: flex;
  gap: 10px; /* Space between buttons */
}
.login-form-container {
    width: 60%; /* Adjust width of the form */
}


.button-container a {
  text-decoration: none;
}

#form_submit {
  padding: 10px 15px; /* Button padding */
  width: 80px; /* Adjust width as needed */
  background-color:#8655fc; /* Button color */
  color: white; /* Text color */
  border: none;
  border-radius: 5px; /* Rounded corners */
  cursor: pointer;
  font-size: 14px;
  text-align: center;
  transition: background-color 0.3s; /* Hover effect */
}

#form_submit:hover {
  background-color: #45a049; /* Darker color on hover */
}
</style>

    <div id="preloader">
        <div class="loader"></div>
    </div>
   
    <div class="login-area login-s2">
        <div class="container">
            <div class="login-box ptb--100">
                <form method="POST" name="signin">
                    <div class="login-form-head">
                        <p>Employee Leave Management System</p>
                        <h4>Employee Login Panel</h4>
                        <a href="admin/index.php"><button id="form_submit" type="button" name="Admin"> Admin</button></a>
                        <a href="hr/index.php"><button id="form_submit" type="button" name="HR"> HR</button></a>
                        <a href="hod/index.php"><button id="form_submit" type="button" name="HOD"> HOD </button></a>
                        <?php if ($msg) { ?><div class="errorWrap"><strong>Error</strong> : <?php echo htmlentities($msg); ?> </div><?php } ?>
                    </div>
                    <div class="login-form-body">
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" id="username" name="username" autocomplete="off" required>
                            <i class="ti-email"></i>
                            <div class="text-danger"></div>
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" id="password" name="password" autocomplete="off" required>
                            <i class="ti-lock"></i>
                            <div class="text-danger"></div>
                        </div>
                        <div class="row mb-4 rmber-area">
                            <div class="col-6">
                                <div class="custom-control custom-checkbox mr-sm-2">
                                    <input type="checkbox" class="custom-control-input" id="customControlAutosizing">
                                    <label class="custom-control-label" for="customControlAutosizing">Remember Me</label>
                                </div>
                            </div>
                            <div class="col-6 text-right">
                                <a href="password-recovery.php">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit" name="signin">Login</i></button>
                        </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/vendor/jquery-2.2.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/metisMenu.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.slicknav.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>
</html>
