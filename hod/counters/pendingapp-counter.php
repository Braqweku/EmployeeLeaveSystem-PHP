<?php
    include '../../includes/dbconn.php';

    $sql = "SELECT id from tblleaves WHERE HODStatus = '0'";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);
    $leavtypcount=$query->rowCount();

    echo htmlentities($leavtypcount);

?>   