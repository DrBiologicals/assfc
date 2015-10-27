<?php

//getting modules, sections and execution classes
try {
    $DBH = new PDO(
                    "mysql:host=$dbHost;dbname=$dbName",
                    $dbUser,
                    $dbPasswd,
                    array(
                        PDO::ATTR_ERRMODE => $errormode,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));
    //getting modules
    $STH = $DBH->prepare("
            SELECT  m.module_id, m.module_name, m.module_description
            FROM " . $dbPrefix . "module AS m
            WHERE m.module_active = 1
            ORDER BY m.module_sort");
    $STH->execute();
    $STH->setFetchMode(PDO::FETCH_OBJ);

    //getting sections
    $STH2 = $DBH->prepare("
            SELECT  s.section_id, s.section_name, s.section_description
            FROM " . $dbPrefix . "section AS s
            NATURAL JOIN " . $dbPrefix . "module_section AS ms
            WHERE ms.section_active = 1
            AND ms.module_id = ?
            ORDER BY ms.section_sort");
    //fetch data
    $i = 0;
    while ($row = $STH->fetch()) {
        $modules[$i] = $row;
        $STH2->bindParam(1, $modules[$i]->module_id);
        $STH2->execute();
        $STH2->setFetchMode(PDO::FETCH_OBJ);
        $j = 0;
        while ($row2 = $STH2->fetch()) {
            $sections[$i][$j] = $row2;
            $j++;
        }
        $i++;
    }
    //getting ex classes
    $STH = $DBH->prepare("
            SELECT  exclass_id, exclass_name
            FROM " . $dbPrefix . "exclass
            ORDER BY exclass_sort");
    $STH->execute();
    $STH->setFetchMode(PDO::FETCH_OBJ);

    $i = 0;
    while ($row = $STH->fetch()) {
        $exclasses[$i] = $row;
        $i++;
    }
    $DBH = null;
} catch (PDOException $e) {
    echo $e->getMessage();
}

//get settings for return
try {
    $DBH = new PDO(
                    "mysql:host=$dbHost;dbname=$dbName",
                    $dbUser,
                    $dbPasswd,
                    array(
                        PDO::ATTR_ERRMODE => $errormode,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));
    //getting execution class from user
    $STH = $DBH->prepare("SELECT  exclass_id FROM " . $dbPrefix . "user WHERE user_id = ?");
    $STH->bindParam(1, $globalid);
    $STH->execute();
    $STH->setFetchMode(PDO::FETCH_OBJ);
    $userexclass = $STH->fetch()->exclass_id;

    //getting module settings from user
    $STH = $DBH->prepare("SELECT  module_id FROM " . $dbPrefix . "user_module WHERE user_id = ?");
    $STH->bindParam(1, $globalid);
    $STH->execute();
    $STH->setFetchMode(PDO::FETCH_OBJ);

    $i = 0;
    while ($row = $STH->fetch()) {
        $modulesettings[$i] = $row;
        $i++;
    }
    $DBH = null;
} catch (PDOException $e) {
    echo $e->getMessage();
}

if (isset($_POST['next'])) {
    //insert data into database
    try {
        $DBH = new PDO(
                        "mysql:host=$dbHost;dbname=$dbName",
                        $dbUser,
                        $dbPasswd,
                        array(
                            PDO::ATTR_ERRMODE => $errormode,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));
        //insert execution class
        $STH = $DBH->prepare("
            UPDATE " . $dbPrefix . "user 
                SET exclass_id = ? WHERE user_id = ?");
        $STH->bindParam(1, $_POST['execution']);
        $STH->bindParam(2, $globalid);
        $STH->execute();

        //delete / insert module selection
        $STH = $DBH->prepare("DELETE FROM " . $dbPrefix . "user_module WHERE user_id = ?");
        $STH->bindParam(1, $globalid);
        $STH->execute();

        $STH = $DBH->prepare("INSERT INTO " . $dbPrefix . "user_module (user_id, module_id) VALUES (?, ?)");
        for ($i = 0; $i < count($_POST['modulescheckbox']); $i++) {
            $STH->bindParam(1, $globalid);
            $STH->bindParam(2, $_POST['modulescheckbox'][$i]);
            $STH->execute();
        }

        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    //redirect to overviewpage
            if ($sslonly == TRUE) {
                $httpprefix = 'https://';
            } else {
                $httpprefix = 'http://';
            }
            $nextpage = $httpprefix . $website . 'verification.php?mode=main&action=success';
            header("Location: " . $nextpage);
            exit;
}
?>
