<?php

//getting subsections and sections from database
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

    //make number for module_sort
    $STH = $DBH->prepare("
            SELECT s.section_id, s.section_name, ss.subsection_id, ss.subsection_name, ss.subsection_description, sns.subsection_active 
            FROM " . $dbPrefix . "subsection AS ss
            INNER JOIN " . $dbPrefix . "section_subsection AS sns ON sns.subsection_id = ss.subsection_id
            INNER JOIN " . $dbPrefix . "section AS s ON s.section_id = sns.section_id
            WHERE s.section_id = ?
            ORDER BY sns.subsection_sort");
    //execute statement
    $STH->bindParam(1, $_GET['sectionid']);

    $STH->execute();
    //set fetch mode
    $STH->setFetchMode(PDO::FETCH_OBJ);
    //fetch data
    $i = 0;
    while ($row = $STH->fetch()) {
        $array[$i] = $row;
        $i++;
    }

    //if there is no subsection, we need the name
    if (!isset($array[0]->subsection_name)) {
        $STH = $DBH->prepare("
            SELECT s.section_id, s.section_name 
            FROM " . $dbPrefix . "section AS s
            WHERE s.section_id = ?");
        $STH->bindParam(1, $_GET['sectionid']);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $row = $STH->fetch();
        $sectionID = $row->section_id;
        $sectionName = $row->section_name;
    } else {
        $sectionID = $array[0]->section_id;
        $sectionName = $array[0]->section_name;
    }
    //close db connection
    $DBH = null;
} catch (PDOException $e) {
    echo $e->getMessage();
}
//If no Sectionid is provided by get, we net a list with names
if (!isset($_GET['sectionid'])) {


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

        //get sections
        $STH2 = $DBH->prepare("
            SELECT s.section_id, s.section_name
            FROM " . $dbPrefix . "section s
            INNER JOIN " . $dbPrefix . "module_section AS ms ON ms.section_id = s.section_id
            WHERE ms.module_id = ?
            ORDER BY ms.section_sort");

        //set fetch mode
        $STH2->setFetchMode(PDO::FETCH_OBJ);

        //fetch data
        $i = 0;
        while ($row = $STH->fetch()) {
            $STH2->bindParam(1, $row->module_id);
            $STH2->execute();
            $j = 0;
            while ($row2 = $STH2->fetch()) {
                $sections[$i][$j] = $row2;
                $j++;
            }
            $modules[$i] = $row;
            $i++;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
if ($_GET['action'] == 'newsubsection') {
    if (isset($_POST['create'])) {
        $modError = FALSE;
        //check input data
        //checking module name
        if ($_POST['name'] == '') {
            $modError = TRUE;
            $modErrorName = 'Required field.';
        } elseif (strlen($_POST['name']) > 255) {
            $modError = TRUE;
            $modErrorName = 'Only 255 characters allowed as section name. You have used ' . strlen($_POST['name']) . '.';
        }
        //checking module description
        if (strlen($_POST['description']) > 65535) {
            $modError = TRUE;
            $modErrorDescription = 'Only 65535 characters allowed as section description. You have used ' . strlen($_POST['description']) . '.';
        }
        //everything went right - write data to database
        if ($modError == FALSE && isset($_POST['create'])) {
            //add to database
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

                //make number for module_sort
                $STH = $DBH->prepare("SELECT MAX(subsection_sort) AS 'max' FROM " . $dbPrefix . "section_subsection WHERE section_id = ?");
                //execute statement
                $STH->bindParam(1, $_GET['sectionid']);
                $STH->execute();
                //set fetch mode
                $STH->setFetchMode(PDO::FETCH_OBJ);
                //fetch data
                $row = $STH->fetch();

                if (isset($row->max)) {
                    $sort = $row->max + 1;
                } else {
                    $sort = 1;
                }

                //make number for module_active
                if ($_POST['active'] == on) {
                    $active = 1;
                } else {
                    $active = 0;
                }

                //insert data into database
                //there are 2 tables to edit, is is IMPORTANT to use commit here
                $DBH->beginTransaction();
                //prepare statement
                $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "subsection
                                (subsection_name, subsection_description)
                                VALUES (?, ?)");
                //bind variables
                $STH->bindParam(1, $_POST['name']);
                $STH->bindParam(2, $_POST['description']);

                //execute statement
                $STH->execute();

                $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "section_subsection
                                (section_id, subsection_id, subsection_sort, subsection_active)
                                VALUES (?, ?, ?, ?)");
                $sectionid = $DBH->lastInsertId();
                $STH->bindParam(1, $_GET['sectionid']);
                $STH->bindParam(2, $sectionid);
                $STH->bindParam(3, $sort);
                $STH->bindParam(4, $active);
                //execute statement
                $STH->execute();
                //commit statements
                $DBH->commit();
                //close db connection
                $DBH = null;
            } catch (PDOException $e) {
                $DBH->rollBack();
                echo $e->getMessage();
            }

            //redirect to overviewpage
            if ($sslonly == TRUE) {
                $httpprefix = 'https://';
            } else {
                $httpprefix = 'http://';
            }
            $nextpage = $httpprefix . $website . 'admin.php?mode=subsection&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'];
            header("Location: " . $nextpage);
            exit;
        }
    }
} elseif ($_GET['action'] == 'connectsubsection') {
    //get modules + sections
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
            SELECT section_id, section_name 
            FROM " . $dbPrefix . "section
            WHERE section_id <> ?
            AND section_id IN (SELECT section_id FROM " . $dbPrefix . "section_subsection)");
        //execute statement
        $STH->bindParam(1, $_GET['sectionid']);

        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);


        $STH2 = $DBH->prepare("
            SELECT ss.subsection_id, ss.subsection_name, ss.subsection_description 
            FROM " . $dbPrefix . "subsection ss
            INNER JOIN " . $dbPrefix . "section_subsection AS sns ON sns.subsection_id = ss.subsection_id
            WHERE sns.section_id = ?
            AND ss.subsection_id NOT IN (SELECT subsection_id FROM " . $dbPrefix . "section_subsection WHERE section_id = ?)");
        //set fetch mode
        $STH2->setFetchMode(PDO::FETCH_OBJ);

        //fetch data
        $i = 0;
        while ($row = $STH->fetch()) {
            $STH2->bindParam(1, $row->section_id);
            $STH2->bindParam(2, $_GET['sectionid']);
            $STH2->execute();
            $j = 0;
            while ($row2 = $STH2->fetch()) {
                //check if this subsection is already in another section
                $isin = FALSE;
                for ($k = 0; $k < count($subsectionidtemp); $k++) {
                    if ($subsectionidtemp[$k] == $row2->subsection_id) {
                        $isin = TRUE;
                    }
                }
                if ($isin == FALSE) {
                    $subsections[$i][$j] = $row2;
                    $subsectionidtemp[] = $row2->subsection_id;
                    $j++;
                }
            }
            $sections[$i] = $row;
            $i++;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    if (isset($_POST['connect'])) {
        $checkarray = $_POST[check];
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

            //make number for module_sort
            $STH = $DBH->prepare("SELECT MAX(subsection_sort) AS 'max' FROM " . $dbPrefix . "section_subsection WHERE section_id = ?");
            $STH->bindParam(1, $_GET['sectionid']);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_OBJ);
            //fetch data
            $row = $STH->fetch();

            if (isset($row->max)) {
                $sort = $row->max + 1;
            } else {
                $sort = 1;
            }

            //connect Section to Module
            $STH = $DBH->prepare("
            INSERT INTO  " . $dbPrefix . "section_subsection
            (section_id, subsection_id, subsection_sort, subsection_active)
                                VALUES (?, ?, ?, ?)");
            //execute statement
            $active = '0';
            for ($i = 0; $i < count($checkarray); $i++) {
                $STH->bindParam(1, $_GET['sectionid']);
                $STH->bindParam(2, $checkarray[$i]);
                $STH->bindParam(3, $sort);
                $STH->bindParam(4, $active);
                $STH->execute();
                $sort++;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        //redirect to overviewpage
        if ($sslonly == TRUE) {
            $httpprefix = 'https://';
        } else {
            $httpprefix = 'http://';
        }
        $nextpage = $httpprefix . $website . 'admin.php?mode=subsection&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'];
        header("Location: " . $nextpage);
        exit;
    }
} elseif ($_GET['action'] == 'activatesubsection') {
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

        //make number for module_sort
        $STH = $DBH->prepare("UPDATE " . $dbPrefix . "section_subsection 
                SET subsection_active = ? WHERE section_id = ? AND subsection_id = ?");
        //bind variables
        $STH->bindParam(1, $_GET['active']);
        $STH->bindParam(2, $_GET['sectionid']);
        $STH->bindParam(3, $_GET['subsectionid']);
        //execute statement
        $STH->execute();
        //close db connection
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
    $nextpage = $httpprefix . $website . 'admin.php?mode=subsection&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'];
    header("Location: " . $nextpage);
    exit;
} elseif ($_GET['action'] == 'editsubsection') {

    //check if section is used in another module
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

        //prepare statement
        $STH = $DBH->prepare("
            SELECT s.section_id, s.section_name 
            FROM " . $dbPrefix . "section AS s
            INNER JOIN " . $dbPrefix . "section_subsection AS sns ON sns.section_id = s.section_id
            WHERE sns.subsection_id = ?
            AND s.section_id <> ?");

        $STH->bindParam(1, $_GET['subsectionid']);
        $STH->bindParam(2, $_GET['sectionid']);
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);
        //fetch data
        $i = 0;
        while ($row = $STH->fetch()) {
            $sections[$i] = $row;
            $i++;
        }
        if (isset($sections[0])) {
            $moresections = TRUE;
        }
        //close db connection
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    //getting section data from database
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

        //prepare statement
        $STH = $DBH->prepare("
            SELECT ss.subsection_id, ss.subsection_name, ss.subsection_description, sns.subsection_active 
            FROM " . $dbPrefix . "subsection AS ss
            INNER JOIN " . $dbPrefix . "section_subsection AS sns ON sns.subsection_id = ss.subsection_id
            WHERE ss.subsection_id = ?");

        $STH->bindParam(1, $_GET['subsectionid']);
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);
        //fetch data
        $subsectiondata = $STH->fetch();
        //close db connection
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    if (isset($_POST['edit'])) {
        $modError = FALSE;

        //check input data
        //checking module name
        if ($_POST['name'] == '') {
            $modError = TRUE;
            $modErrorName = 'Required field.';
        } elseif (strlen($_POST['name']) > 255) {
            $modError = TRUE;
            $modErrorName = 'Only 255 characters allowed as module name. You have used ' . strlen($_POST['name']) . '.';
        }
        //checking module description
        if (strlen($_POST['description']) > 65535) {
            $modError = TRUE;
            $modErrorDescription = 'Only 65535 characters allowed as module Description. You have used ' . strlen($_POST['description']) . '.';
        }
        //if seperation is needed
        if ($_POST['separate'] == on && $modError == FALSE && isset($_POST['edit'])) {

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

                //make number for module_sort
                $STH = $DBH->prepare("SELECT MAX(subsection_sort) AS 'max' FROM " . $dbPrefix . "section_subsection WHERE section_id = ?");
                //execute statement
                $STH->bindParam(1, $_GET['sectionid']);
                $STH->execute();
                //set fetch mode
                $STH->setFetchMode(PDO::FETCH_OBJ);
                //fetch data
                $row = $STH->fetch();

                if (isset($row->max)) {
                    $sort = $row->max + 1;
                } else {
                    $sort = 1;
                }

                //make number for module_active
                if ($_POST['active'] == on) {
                    $active = 1;
                } else {
                    $active = 0;
                }

                //insert data into database
                //there are 2 tables to edit, is is IMPORTANT to use commit here
                $DBH->beginTransaction();
                //prepare statement
                $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "subsection
                                (subsection_name, subsection_description)
                                VALUES (?, ?)");
                //bind variables
                $STH->bindParam(1, $_POST['name']);
                $STH->bindParam(2, $_POST['description']);

                //execute statement
                $STH->execute();

                $STH = $DBH->prepare("
                                UPDATE " . $dbPrefix . "section_subsection
                                SET subsection_id = ?, subsection_active = ?
                                WHERE subsection_id = ?
                                AND section_id = ?");
                $sectionid = $DBH->lastInsertId();
                $STH->bindParam(1, $sectionid);
                $STH->bindParam(2, $active);
                $STH->bindParam(3, $_GET['subsectionid']);
                $STH->bindParam(4, $_GET['sectionid']);
                //execute statement
                $STH->execute();
                //commit statements
                $DBH->commit();
                //close db connection
                $DBH = null;
            } catch (PDOException $e) {
                $DBH->rollBack();
                echo $e->getMessage();
            }

            //redirect to overviewpage
            if ($sslonly == TRUE) {
                $httpprefix = 'https://';
            } else {
                $httpprefix = 'http://';
            }
            $nextpage = $httpprefix . $website . 'admin.php?mode=subsection&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'];
            header("Location: " . $nextpage);
            exit;
        }
        //everything went right - write data to database
        elseif ($modError == FALSE && isset($_POST['edit'])) {
            //make number for module_active
            if ($_POST['active'] == on) {
                $active = 1;
            } else {
                $active = 0;
            }
            //add to database
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

                //insert data into database
                $DBH->beginTransaction();
                //prepare statement
                $STH = $DBH->prepare("
                                UPDATE " . $dbPrefix . "subsection
                                SET subsection_name = ?, subsection_description = ? 
                                WHERE subsection_id = ?");

                //bind variables
                $STH->bindParam(1, $_POST['name']);
                $STH->bindParam(2, $_POST['description']);
                $STH->bindParam(3, $_GET['subsectionid']);
                //execute statement
                echo $_POST['description'];
                $STH->execute();
                echo 'test2';
                $STH = $DBH->prepare("
                                UPDATE " . $dbPrefix . "section_subsection
                                SET subsection_active = ? 
                                WHERE subsection_id = ?
                                AND section_id = ?");

                $STH->bindParam(1, $active);
                $STH->bindParam(2, $_GET['subsectionid']);
                $STH->bindParam(3, $_GET['sectionid']);

                //execute statement
                $STH->execute();
                //close db connection
                //commit statements
                $DBH->commit();
                $DBH = null;
            } catch (PDOException $e) {
                $DBH->rollBack();
                echo $e->getMessage();
            }

            //redirect to overviewpage
            if ($sslonly == TRUE) {
                $httpprefix = 'https://';
            } else {
                $httpprefix = 'http://';
            }
            $nextpage = $httpprefix . $website . 'admin.php?mode=subsection&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'];
            header("Location: " . $nextpage);
            exit;
        }
    }
} elseif ($_GET['action'] == 'sort') {
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
        if ($_GET['dir'] == 'up') {

            //get id from +1 module
            $STH = $DBH->prepare("SELECT subsection_id, subsection_sort 
                    FROM " . $dbPrefix . "section_subsection 
                    WHERE section_id = ?
                    AND subsection_sort = (                 
                        SELECT subsection_sort 
                        FROM " . $dbPrefix . "section_subsection 
                        WHERE subsection_id = ?
                        AND section_id = ?)-1");
            $STH->bindParam(1, $_GET['sectionid']);
            $STH->bindParam(2, $_GET['subsectionid']);
            $STH->bindParam(3, $_GET['sectionid']);
            $STH->execute();
            //set fetch mode
            $STH->setFetchMode(PDO::FETCH_OBJ);

            //fetch data
            $row = $STH->fetch();

            $subsectionSort = $row->subsection_sort;
            $subsectionID = $row->subsection_id;

            //check if module is on place 1
            if (isset($row->subsection_id)) {
                //sort the modules
                try {
                    $STH = $DBH->prepare("UPDATE " . $dbPrefix . "section_subsection 
                        SET subsection_sort = ? WHERE subsection_id = ? AND section_id = ?");
                    $newsort = $subsectionSort + 1;
                    $STH->bindParam(1, $newsort);
                    $STH->bindParam(2, $subsectionID);
                    $STH->bindParam(3, $_GET['sectionid']);
                    $STH->execute();
                    $STH->bindParam(1, $subsectionSort);
                    $STH->bindParam(2, $_GET['subsectionid']);
                    $STH->bindParam(3, $_GET['sectionid']);
                    $STH->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
            }
        }
        if ($_GET['dir'] == 'down') {
            //get id from -1 module
            $STH = $DBH->prepare("SELECT subsection_id, subsection_sort 
                    FROM " . $dbPrefix . "section_subsection 
                    WHERE section_id = ?
                    AND subsection_sort = (
                        SELECT subsection_sort 
                        FROM " . $dbPrefix . "section_subsection 
                        WHERE subsection_id = ?
                        AND section_id = ?)+1");
            $STH->bindParam(1, $_GET['sectionid']);
            $STH->bindParam(2, $_GET['subsectionid']);
            $STH->bindParam(3, $_GET['sectionid']);
            $STH->execute();
            //set fetch mode
            $STH->setFetchMode(PDO::FETCH_OBJ);

            //fetch data
            $row = $STH->fetch();

            $subsectionSort = $row->subsection_sort;
            $subsectionID = $row->subsection_id;

            //check if module is on last place
            if (isset($row->subsection_id)) {
                //sort the modules
                try {
                    $STH = $DBH->prepare("UPDATE " . $dbPrefix . "section_subsection 
                        SET subsection_sort = ? WHERE subsection_id = ? AND section_id = ?");
                    $newsort = $subsectionSort - 1;
                    $STH->bindParam(1, $newsort);
                    $STH->bindParam(2, $subsectionID);
                    $STH->bindParam(3, $_GET['sectionid']);
                    $STH->execute();
                    $STH->bindParam(1, $subsectionSort);
                    $STH->bindParam(2, $_GET['subsectionid']);
                    $STH->bindParam(3, $_GET['sectionid']);
                    $STH->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
            }
        }
        //close db connection
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
    $nextpage = $httpprefix . $website . 'admin.php?mode=subsection&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'];
    header("Location: " . $nextpage);
    exit;
} elseif ($_GET['action'] == 'deletesubsection') {
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

        //prepare statement
        $STH = $DBH->prepare("
            SELECT subsection_id, subsection_name
            FROM " . $dbPrefix . "subsection
            WHERE subsection_id = ?");

        $STH->bindParam(1, $_GET['subsectionid']);
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);
        //fetch data
        $deletesubsectiondata = $STH->fetch();
        //close db connection
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    if (isset($_POST['abort'])) {
        if ($sslonly == TRUE) {
            $httpprefix = 'https://';
        } else {
            $httpprefix = 'http://';
        }
        $nextpage = $httpprefix . $website . 'admin.php?mode=subsection&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'];
        header("Location: " . $nextpage);
        exit;
    }
    if (isset($_POST['delete'])) {
        $modError = FALSE;
        //first of all: a check if there is a subsection
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

            //prepare statement
            $STH = $DBH->prepare("
            SELECT ss.subsection_id, ss.subsection_name, q.question_id, q.question_name
            FROM " . $dbPrefix . "question AS q
            INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.question_id = q.question_id
            INNER JOIN " . $dbPrefix . "subsection AS ss ON ss.subsection_id = sq.subsection_id
            WHERE sq.subsection_id = ?");

            $STH->bindParam(1, $_GET['subsectionid']);
            //execute statement
            $STH->execute();
            //set fetch mode
            $STH->setFetchMode(PDO::FETCH_OBJ);
            //fetch data
            $i = 0;
            while ($row = $STH->fetch()) {
                $check[$i] = $row;
                $i++;
            }
            //close db connection
            $DBH = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        if (isset($check)) {
            $modError = TRUE;
            $modErrorModule = 'Deletion not possible. You can not delete a Subsection where are still Questions in it.';
        }
        if ($modError == FALSE) {

            //two ways are possible.
            //1: section is used in other modules -> delete entry in module_section
            //2: section is not used in other module -> delete entry in section
            //check which way is needed:
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

                //prepare statement
                $STH = $DBH->prepare("
                    SELECT section_id 
                    FROM " . $dbPrefix . "section_subsection
                    WHERE subsection_id = ?
                    AND section_id <> ?");

                $STH->bindParam(1, $_GET['subsectionid']);
                $STH->bindParam(2, $_GET['sectionid']);
                //execute statement
                $STH->execute();
                //set fetch mode
                $STH->setFetchMode(PDO::FETCH_OBJ);
                //fetch data
                $i = 0;
                while ($row = $STH->fetch()) {
                    $subsections[$i] = $row;
                    $i++;
                }
                if (isset($subsections[0])) {
                    $moresubsections = TRUE;
                }
                //close db connection
                $DBH = null;
            } catch (PDOException $e) {
                echo $e->getMessage();
            }


            if ($moresubsections == TRUE) {


                //way 1: delete entry in module_section
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
                    $DBH->beginTransaction();
                    //prepare statement
                    $STH = $DBH->prepare("DELETE FROM " . $dbPrefix . "section_subsection WHERE subsection_id = ? AND section_id = ?");

                    $STH->bindParam(1, $_GET['subsectionid']);
                    $STH->bindParam(2, $_GET['sectionid']);
                    //execute statement
                    $STH->execute();

                    //sort modules again
                    $STH = $DBH->prepare("SELECT subsection_id FROM " . $dbPrefix . "section_subsection WHERE section_id = ? ORDER BY subsection_sort");
                    $STH->bindParam(1, $_GET['sectionid']);
                    $STH->execute();
                    //set fetch mode
                    $STH->setFetchMode(PDO::FETCH_OBJ);
                    //fetch data
                    $i = 0;
                    while ($row = $STH->fetch()) {
                        $sorter[$i] = $row;
                        $i++;
                    }
                    $STH = $DBH->prepare("UPDATE " . $dbPrefix . "section_subsection SET subsection_sort = ? WHERE subsection_id = ? AND section_id = ?");
                    for ($i = 0; $i < count($sorter); $i++) {
                        $newsort = $i + 1;
                        $STH->bindParam(1, $newsort);
                        $STH->bindParam(2, $sorter[$i]->subsection_id);
                        $STH->bindParam(3, $_GET['sectionid']);
                        $STH->execute();
                    }

                    //close db connection
                    $DBH->commit();
                    $DBH = null;
                } catch (PDOException $e) {
                    $DBH->rollBack();
                    echo $e->getMessage();
                }
            } else {


                //way 2: delete entry in module_section
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
                    $DBH->beginTransaction();
                    //prepare statement
                    $STH = $DBH->prepare("DELETE FROM " . $dbPrefix . "subsection WHERE subsection_id = ?");

                    $STH->bindParam(1, $_GET['subsectionid']);
                    //execute statement
                    $STH->execute();

                    //sort modules again
                    $STH = $DBH->prepare("SELECT subsection_id FROM " . $dbPrefix . "section_subsection WHERE section_id = ? ORDER BY subsection_sort");
                    $STH->bindParam(1, $_GET['sectionid']);
                    $STH->execute();
                    //set fetch mode
                    $STH->setFetchMode(PDO::FETCH_OBJ);
                    //fetch data
                    $i = 0;
                    while ($row = $STH->fetch()) {
                        $sorter[$i] = $row;
                        $i++;
                    }
                    $STH = $DBH->prepare("UPDATE " . $dbPrefix . "section_subsection SET subsection_sort = ? WHERE subsection_id = ? AND section_id = ?");
                    for ($i = 0; $i < count($sorter); $i++) {
                        $newsort = $i + 1;
                        $STH->bindParam(1, $newsort);
                        $STH->bindParam(2, $sorter[$i]->subsection_id);
                        $STH->bindParam(3, $_GET['sectionid']);
                        $STH->execute();
                    }

                    //close db connection
                    $DBH->commit();
                    $DBH = null;
                } catch (PDOException $e) {
                    $DBH->rollBack();
                    echo $e->getMessage();
                }
            }


            if ($sslonly == TRUE) {
                $httpprefix = 'https://';
            } else {
                $httpprefix = 'http://';
            }
            $nextpage = $httpprefix . $website . 'admin.php?mode=subsection&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'];
            header("Location: " . $nextpage);
            exit;
        }
    }
}
?>
