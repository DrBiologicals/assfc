<?php
$pageNavi = 'Create Reports';
include 'header.php';
?>

<?php include 'navbar.php'; ?>


<div id="main">
    <h2>Create Reports</h2>
    <?php if ($globalusergroup == 1) : ?>
        <form action="report.php" method="post" >
            Please enter the USER-ID of the user you want to create the report: <br><br>
            <input name="userid" type="text" style="width: 100px">
            <input value="Create Report" type="submit" name="create_report">
            <br>
            <span class="error"><?php echo $reporterror; ?></span>
        </form>
    <?php else : ?>
    	<form action="report.php" method="post" >
            Please enter the USER-ID of the user you want to create the report: <br><br>
            <input name="userid" type="text" style="width: 100px">
            <input value="Create Report" type="submit" name="create_report">
            <br>
            <span class="error"><?php echo $reporterror; ?></span>
        </form>
    <?php endif; ?>
</div>    

<?php include 'footer.php'; ?>
