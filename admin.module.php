<?php

if ($_GET['action'] == 'overview') {

    //getting module data from database
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
        $STH = $DBH->prepare("SELECT * FROM " . $dbPrefix . "module ORDER BY module_sort");
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);
        //fetch data
        $i = 0;
        while ($row = $STH->fetch()) {
            $array[$i] = $row;
            $i++;
        }
        //close db connection
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

if ($_GET['action'] == 'newmodule') {
    if (isset($_POST['create'])) {
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
                $STH = $DBH->prepare("SELECT MAX(module_sort) AS 'max' FROM " . $dbPrefix . "module");
                //execute statement
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
                //prepare statement
                $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "module
                                (module_name, module_description, module_sort, module_active)
                                VALUES (?, ?, ?, ?)");

                //bind variables
                $STH->bindParam(1, $_POST['name']);
                $STH->bindParam(2, $_POST['description']);
                $STH->bindParam(3, $sort);
                $STH->bindParam(4, $active);

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
            $nextpage = $httpprefix . $website . 'admin.php?mode=module&action=overview';
            header("Location: " . $nextpage);
            exit;
        }
    }
} elseif ($_GET['action'] == 'editmodule') {
    //getting module data from database
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
        $STH = $DBH->prepare("SELECT * FROM " . $dbPrefix . "module WHERE module_id = ?");

        $STH->bindParam(1, $_GET['moduleid']);
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);
        //fetch data
        $moduledata = $STH->fetch();
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
        //everything went right - write data to database
        if ($modError == FALSE && isset($_POST['edit'])) {
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
                //prepare statement
                $STH = $DBH->prepare("
                                UPDATE " . $dbPrefix . "module 
                                SET module_name = ?, module_description = ?, module_active = ? 
                                WHERE module_id = ?");

                //bind variables
                $STH->bindParam(1, $_POST['name']);
                $STH->bindParam(2, $_POST['description']);
                $STH->bindParam(3, $active);
                $STH->bindParam(4, $_GET['moduleid']);

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
            $nextpage = $httpprefix . $website . 'admin.php?mode=module&action=overview';
            header("Location: " . $nextpage);
            exit;
        }
    }
} elseif ($_GET['action'] == 'activatemodule') {
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
        $STH = $DBH->prepare("UPDATE " . $dbPrefix . "module 
                SET module_active = ? WHERE module_id = ?");
        //bind variables
        $STH->bindParam(1, $_GET['active']);
        $STH->bindParam(2, $_GET['id']);
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
    $nextpage = $httpprefix . $website . 'admin.php?mode=module&action=overview';
    header("Location: " . $nextpage);
    exit;
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
            $STH = $DBH->prepare("SELECT module_id, module_sort 
                    FROM " . $dbPrefix . "module 
                    WHERE module_sort = (
                        SELECT module_sort 
                        FROM " . $dbPrefix . "module 
                        WHERE module_id = ?)-1");
            $STH->bindParam(1, $_GET['id']);
            $STH->execute();
            //set fetch mode
            $STH->setFetchMode(PDO::FETCH_OBJ);

            //fetch data
            $row = $STH->fetch();

            $moduleSort = $row->module_sort;
            $moduleID = $row->module_id;

            //check if module is on place 1
            if (isset($row->module_id)) {
                //sort the modules
                try {
                    $STH = $DBH->prepare("UPDATE " . $dbPrefix . "module 
                        SET module_sort = ? WHERE module_id = ?");
                    $newsort = $moduleSort + 1;
                    $STH->bindParam(1, $newsort);
                    $STH->bindParam(2, $moduleID);
                    $STH->execute();
                    $STH->bindParam(1, $moduleSort);
                    $STH->bindParam(2, $_GET['id']);
                    $STH->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
            }
        }
        if ($_GET['dir'] == 'down') {
            //get id from +1 module
            $STH = $DBH->prepare("SELECT module_id, module_sort 
                    FROM " . $dbPrefix . "module 
                    WHERE module_sort = (
                        SELECT module_sort 
                        FROM " . $dbPrefix . "module 
                        WHERE module_id = ?)+1");
            $STH->bindParam(1, $_GET['id']);
            $STH->execute();
            echo 'ok';
            //set fetch mode
            $STH->setFetchMode(PDO::FETCH_OBJ);

            //fetch data
            $row = $STH->fetch();

            $moduleSort = $row->module_sort;
            $moduleID = $row->module_id;

            //check if module is on last place
            if (isset($row->module_id)) {
                //sort the modules
                try {
                    $STH = $DBH->prepare("UPDATE " . $dbPrefix . "module 
                        SET module_sort = ? WHERE module_id = ?");
                    echo 'try1';
                    $newsort = $moduleSort - 1;
                    $STH->bindParam(1, $newsort);
                    $STH->bindParam(2, $moduleID);
                    $STH->execute();
                    echo 'trz2';
                    $STH->bindParam(1, $moduleSort);
                    $STH->bindParam(2, $_GET['id']);
                    $STH->execute();
                    echo 'ok';
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
    $nextpage = $httpprefix . $website . 'admin.php?mode=module&action=overview';
    header("Location: " . $nextpage);
    exit;
} elseif ($_GET['action'] == 'deletemodule') {

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
            SELECT module_id, module_name
            FROM " . $dbPrefix . "module
            WHERE module_id = ?");

        $STH->bindParam(1, $_GET['moduleid']);
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);
        //fetch data
        $moduledeletedata = $STH->fetch();
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
        $nextpage = $httpprefix . $website . 'admin.php?mode=module&action=overview';
        header("Location: " . $nextpage);
        exit;
    }
    if (isset($_POST['delete'])) {
        $modError = FALSE;
        //first of all: a check if there is a section
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
            SELECT m.module_id, m.module_name, s.section_id, s.section_name
            FROM " . $dbPrefix . "section AS s
            INNER JOIN " . $dbPrefix . "module_section AS ms ON s.section_id = ms.section_id
            INNER JOIN " . $dbPrefix . "module AS m ON ms.module_id = m.module_id
            WHERE ms.module_id = ?");

            $STH->bindParam(1, $_GET['moduleid']);
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
            $modErrorModule = 'Deletion not possible. You can not delete a Module where are still Sections in it.';
        }
        if ($modError == FALSE) {
            //delte from database
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
                $STH = $DBH->prepare("DELETE FROM " . $dbPrefix . "module WHERE module_id = ?");

                $STH->bindParam(1, $_GET['moduleid']);
                //execute statement
                $STH->execute();

                //sort modules again
                $STH = $DBH->prepare("SELECT module_id FROM " . $dbPrefix . "module ORDER BY module_sort");
                $STH->execute();
                //set fetch mode
                $STH->setFetchMode(PDO::FETCH_OBJ);
                //fetch data
                $i = 0;
                while ($row = $STH->fetch()) {
                    $sorter[$i] = $row;
                    $i++;
                }
                $STH = $DBH->prepare("UPDATE " . $dbPrefix . "module SET module_sort = ? WHERE module_id = ?");
                for ($i = 0; $i < count($sorter); $i++) {
                    $newsort = $i+1;
                    $STH->bindParam(1, $newsort);
                    $STH->bindParam(2, $sorter[$i]->module_id);
                    $STH->execute();
                }

                //close db connection
                $DBH->commit();
                $DBH = null;
            } catch (PDOException $e) {
                $DBH->rollBack();
                echo $e->getMessage();
            }
            if ($sslonly == TRUE) {
                $httpprefix = 'https://';
            } else {
                $httpprefix = 'http://';
            }
            $nextpage = $httpprefix . $website . 'admin.php?mode=module&action=overview';
            header("Location: " . $nextpage);
            exit;
        }
    }
}
?>
