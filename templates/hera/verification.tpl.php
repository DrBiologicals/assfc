<?php
$pageNavi = 'Verification';
include 'header.php';
?>

<?php require_once 'navbar.php'; ?>

<div id="main">
    <?php if ($_GET['mode'] == 'main') : ?>
    
        <?php require_once 'verification.main.tpl.php'; ?>

    <?php elseif ($_GET['mode'] == 'umodule') : ?>

        <?php require_once 'verification.umodule.tpl.php'; ?>

    <?php elseif ($_GET['mode'] == 'section') : ?>

        <?php require_once 'admin.section.tpl.php'; ?>

    <?php elseif ($_GET['mode'] == 'uquestion') : ?>

        <?php require_once 'verification.uquestion.tpl.php'; ?>

    <?php elseif ($_GET['mode'] == 'question') : ?>

        <?php require_once 'admin.question.tpl.php'; ?>

    <?php endif; ?>      
</div>    

<?php include 'footer.php'; ?>