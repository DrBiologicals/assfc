<?php
try {
    //connecting to database
    $DBH = new PDO(
                    "mysql:host=$dbHost;dbname=$dbName",
                    $dbUser,
                    $dbPasswd,
                    array(
                        PDO::ATTR_ERRMODE => $errormode,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));

    //get modules
    $STH1 = $DBH->prepare("
            SELECT module_id, module_name 
            FROM " . $dbPrefix . "module
            WHERE module_active = '1'
            ORDER BY module_sort");

    //get sections
    $STH2 = $DBH->prepare("
            SELECT s.section_id, s.section_name 
            FROM " . $dbPrefix . "section AS s
            INNER JOIN " . $dbPrefix . "module_section AS ms ON ms.section_id = s.section_id
            WHERE ms.module_id = ?
            AND ms.section_active = '1'
            ORDER BY ms.section_sort");
    
    //get subsections
    $STH3 = $DBH->prepare("
            SELECT ss.subsection_id, ss.subsection_name 
            FROM " . $dbPrefix . "subsection AS ss
            INNER JOIN " . $dbPrefix . "section_subsection AS sns ON sns.subsection_id = ss.subsection_id
            WHERE sns.section_id = ?
            AND sns.subsection_active = '1'
            ORDER BY sns.subsection_sort");

    //set fetch mode
    $STH1->setFetchMode(PDO::FETCH_OBJ);
    $STH2->setFetchMode(PDO::FETCH_OBJ);
    $STH3->setFetchMode(PDO::FETCH_OBJ);

    $STH1->execute();
    $i = 0;
    while ($row1 = $STH1->fetch()) {
        $usermenuemodules[$i] = $row1;
        $j = 0;
        $STH2->bindParam(1, $row1->module_id);
        $STH2->execute();
        while ($row2 = $STH2->fetch()) {
            $usermenuesections[$i][$j] = $row2;
            $k = 0;
            $STH3->bindParam(1, $row2->section_id);
            $STH3->execute();
            while ($row3 = $STH3->fetch()) {
                $usermenuesubsections[$i][$j][$k] = $row3;
                $k++;
            }
            $j++;
        }
        $i++;
    }
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

function checklist($moduleid, $modulesettings) {
    for ($i = 0; $i < count($modulesettings); $i++) {
        if ($modulesettings[$i]->module_id == $moduleid) {
            return('1');
        }
    }
}
if ($_GET['mode'] == 'module') {
    try {
        //connecting to database
        $DBH = new PDO(
                        "mysql:host=$dbHost;dbname=$dbName",
                        $dbUser,
                        $dbPasswd,
                        array(
                            PDO::ATTR_ERRMODE => $errormode,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));

        //get Modules
        $STH = $DBH->prepare("
            SELECT module_id, module_name 
            FROM " . $dbPrefix . "module
            ORDER BY module_sort");
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);

        //fetch data
        $i = 0;
        while ($row = $STH->fetch()) {
            $menuemodules[$i] = $row;
            $i++;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} elseif ($_GET['mode'] == 'section') {
    try {
        //connecting to database
        $DBH = new PDO(
                        "mysql:host=$dbHost;dbname=$dbName",
                        $dbUser,
                        $dbPasswd,
                        array(
                            PDO::ATTR_ERRMODE => $errormode,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));

        //get Modules
        $STH = $DBH->prepare("
            SELECT module_id, module_name 
            FROM " . $dbPrefix . "module
            ORDER BY module_sort");
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);

        //fetch data
        $i = 0;
        while ($row = $STH->fetch()) {
            $menuemodules[$i] = $row;
            $i++;
        }

        //get Sections
        $STH = $DBH->prepare("
            SELECT s.section_id, s.section_name 
            FROM " . $dbPrefix . "section AS s
            INNER JOIN " . $dbPrefix . "module_section AS ms ON ms.section_id = s.section_id
            WHERE ms.module_id = ?
            ORDER BY ms.section_sort");

        $STH->bindParam(1, $_GET['moduleid']);
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);

        //fetch data
        $j = 0;
        while ($row = $STH->fetch()) {
            $menuesections[$j] = $row;
            $j++;
        }
        
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} elseif ($_GET['mode'] == 'subsection' || $_GET['mode'] == 'question') {
    try {
        //connecting to database
        $DBH = new PDO(
                        "mysql:host=$dbHost;dbname=$dbName",
                        $dbUser,
                        $dbPasswd,
                        array(
                            PDO::ATTR_ERRMODE => $errormode,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));

        //get Modules
        $STH = $DBH->prepare("
            SELECT module_id, module_name 
            FROM " . $dbPrefix . "module
            ORDER BY module_sort");
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);

        //fetch data
        $i = 0;
        while ($row = $STH->fetch()) {
            $menuemodules[$i] = $row;
            $i++;
        }

        //get Sections
        $STH = $DBH->prepare("
            SELECT s.section_id, s.section_name 
            FROM " . $dbPrefix . "section AS s
            INNER JOIN " . $dbPrefix . "module_section AS ms ON ms.section_id = s.section_id
            WHERE ms.module_id = ?
            ORDER BY ms.section_sort");

        $STH->bindParam(1, $_GET['moduleid']);
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);

        //fetch data
        $j = 0;
        while ($row = $STH->fetch()) {
            $menuesections[$j] = $row;
            $j++;
        }

        //get Subsections
        $STH = $DBH->prepare("
            SELECT ss.subsection_id, ss.subsection_name 
            FROM " . $dbPrefix . "subsection AS ss
            INNER JOIN " . $dbPrefix . "section_subsection AS sns ON sns.subsection_id = ss.subsection_id
            WHERE sns.section_id = ?
            ORDER BY sns.subsection_sort");

        $STH->bindParam(1, $_GET['sectionid']);
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);

        //fetch data
        $k = 0;
        while ($row = $STH->fetch()) {
            $menuesubsections[$k] = $row;
            $k++;
        }
        
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

include $templateDir . 'navbar.tpl.php';
include 'localsettings.php';
?>