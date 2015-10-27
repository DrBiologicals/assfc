<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $pageTitle . ' - ' . $pageNavi; ?></title>
        <style type="text/css">
            @import url(<?php echo $templateDir . 'style.css'; ?>);
        </style>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    </head>
    <body>
        <div id="overall">
            <div id="head">
                <div id="headline"><h1><?php echo $pageNavi; ?></h1></div>
                <div id="loginbox">
                    <?php if ($login == TRUE) : ?>
                        <span class="smalltext">Hello <a href="account.php?mode=overview"><?php echo $globalfirstname.' '.$globallastname ?></a>. <a href="login.php?mode=logout">Logout</a></span>
                    <?php else : ?>
                        <span class="smalltext">Hello Guest. You can <a href="login.php?mode=login">Login</a> to yor account or <a href="login.php?mode=register">Register</a> a new one.</span>
                    <?php endif; ?>
                </div>
                <div id="logo"><a href="index.php"><img src="<?php echo $templateDir;?>images/logo.png" alt="Link to the Indexpage" height="100" width="345" style="border:0;"></a></div>
            </div>
            <div id="wrapper">