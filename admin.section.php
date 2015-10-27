<?php

//getting sections and modules data from database
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
            SELECT m.module_id, m.module_name, s.section_id, s.section_name, s.section_description, ms.section_active 
            FROM " . $dbPrefix . "section AS s
            INNER JOIN " . $dbPrefix . "module_section AS ms ON s.section_id = ms.section_id
            INNER JOIN " . $dbPrefix . "module AS m ON ms.module_id = m.module_id
            WHERE ms.module_id = ?
            ORDER BY ms.section_sort");
    //execute statement
    $STH->bindParam(1, $_GET['moduleid']);

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
    if (!isset($array[0]->module_name)) {
        $STH = $DBH->prepare("
            SELECT m.module_id, m.module_name 
            FROM " . $dbPrefix . "module AS m
            WHERE m.module_id = ?");
        $STH->bindParam(1, $_GET['moduleid']);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $row = $STH->fetch();
        $moduleID = $row->module_id;
        $moduleName = $row->module_name;
    } else {
        $moduleID = $array[0]->module_id;
        $moduleName = $array[0]->module_name;
    }
    //close db connection
    $DBH = null;
} catch (PDOException $e) {
    echo $e->getMessage();
}
//If no Moduleid is provided by get, we net a list with names
if (!isset($_GET['moduleid'])) {


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
            $modules[$i] = $row;
            $i++;
        }
        //close db connection
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
if ($_GET['action'] == 'newsection') {
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
                $STH = $DBH->prepare("SELECT MAX(section_sort) AS 'max' FROM " . $dbPrefix . "module_section WHERE module_id = ?");
                //execute statement
                $STH->bindParam(1, $_GET['moduleid']);
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
                                INSERT INTO " . $dbPrefix . "section
                                (section_name, section_description)
                                VALUES (?, ?)");
                //bind variables
                $STH->bindParam(1, $_POST['name']);
                $STH->bindParam(2, $_POST['description']);

                //execute statement
                $STH->execute();

                $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "module_section
                                (module_id, section_id, section_sort, section_active)
                                VALUES (?, ?, ?, ?)");
                $sectionid = $DBH->lastInsertId();
                $STH->bindParam(1, $_GET['moduleid']);
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
            $nextpage = $httpprefix . $website . 'admin.php?mode=section&action=overview&moduleid=' . $_GET['moduleid'];
            header("Location: " . $nextpage);
            exit;
        }
    }
} elseif ($_GET['action'] == 'connectsection') {
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
            SELECT module_id, module_name 
            FROM " . $dbPrefix . "module
            WHERE module_id <> ?
            AND module_id IN (SELECT module_id FROM " . $dbPrefix . "module_section)");
        //execute statement
        $STH->bindParam(1, $_GET['moduleid']);

        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);


        $STH2 = $DBH->prepare("
            SELECT s.section_id, s.section_name, s.section_description 
            FROM " . $dbPrefix . "section s
            INNER JOIN " . $dbPrefix . "module_section AS ms ON s.section_id = ms.section_id
            WHERE ms.module_id = ?
            AND s.section_id NOT IN (SELECT section_id FROM " . $dbPrefix . "module_section WHERE module_id = ?)");
        //set fetch mode
        $STH2->setFetchMode(PDO::FETCH_OBJ);

        //fetch data
        $i = 0;
        while ($row = $STH->fetch()) {
            $STH2->bindParam(1, $row->module_id);
            $STH2->bindParam(2, $_GET['moduleid']);
            $STH2->execute();
            $j = 0;
            while ($row2 = $STH2->fetch()) {
                //check if this section is already in another module
                $isin = FALSE;
                for ($k = 0; $k < count($sectionidtemp); $k++) {
                    if ($sectionidtemp[$k] == $row2->section_id) {
                        $isin = TRUE;
                    }
                }
                if ($isin == FALSE) {
                    $sections[$i][$j] = $row2;
                    $sectionidtemp[] = $row2->section_id;
                    $j++;
                }
            }
            $modules[$i] = $row;
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
            $STH = $DBH->prepare("SELECT MAX(section_sort) AS 'max' FROM " . $dbPrefix . "module_section WHERE module_id = ?");
            $STH->bindParam(1, $_GET['moduleid']);
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
            INSERT INTO  " . $dbPrefix . "module_section
            (module_id, section_id, section_sort, section_active)
                                VALUES (?, ?, ?, ?)");
            //execute statement
            $active = '0';
            for ($i = 0; $i < count($checkarray); $i++) {
                $STH->bindParam(1, $_GET['moduleid']);
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
        $nextpage = $httpprefix . $website . 'admin.php?mode=section&action=overview&moduleid=' . $_GET['moduleid'];
        header("Location: " . $nextpage);
        exit;
    }
} elseif ($_GET['action'] == 'activatesection') {
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
        $STH = $DBH->prepare("UPDATE " . $dbPrefix . "module_section 
                SET section_active = ? WHERE module_id = ? AND section_id = ?");
        //bind variables
        $STH->bindParam(1, $_GET['active']);
        $STH->bindParam(2, $_GET['moduleid']);
        $STH->bindParam(3, $_GET['sectionid']);
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
    $nextpage = $httpprefix . $website . 'admin.php?mode=section&action=overview&moduleid=' . $_GET['moduleid'];
    header("Location: " . $nextpage);
    exit;
} elseif ($_GET['action'] == 'editsection') {

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
            SELECT m.module_id, m.module_name, ms.section_id 
            FROM " . $dbPrefix . "module AS m
            INNER JOIN " . $dbPrefix . "module_section AS ms ON m.module_id = ms.module_id
            WHERE ms.section_id = ?
            AND m.module_id <> ?");

        $STH->bindParam(1, $_GET['sectionid']);
        $STH->bindParam(2, $_GET['moduleid']);
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);
        //fetch data
        $i = 0;
        while ($row = $STH->fetch()) {
            $modules[$i] = $row;
            $i++;
        }
        if (isset($modules[0])) {
            $moremodules = TRUE;
        }
        $originalsectionid = $modules[0]->section_id;
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
            SELECT s.section_id, s.section_name, s.section_description, ms.section_active 
            FROM " . $dbPrefix . "section AS s
            INNER JOIN " . $dbPrefix . "module_section AS ms ON s.section_id = ms.section_id
            WHERE s.section_id = ?");

        $STH->bindParam(1, $_GET['sectionid']);
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);
        //fetch data
        $sectiondata = $STH->fetch();
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
                $STH = $DBH->prepare("SELECT MAX(section_sort) AS 'max' FROM " . $dbPrefix . "module_section WHERE module_id = ?");
                //execute statement
                $STH->bindParam(1, $_GET['moduleid']);
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
                $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "section
                                (section_name, section_description)
                                VALUES (?, ?)");
                $STH->bindParam(1, $_POST['name']);
                $STH->bindParam(2, $_POST['description']);
                $STH->execute();

                $STH = $DBH->prepare("
                                UPDATE " . $dbPrefix . "module_section
                                SET section_id = ?, section_active = ?
                                WHERE section_id = ?
                                AND module_id = ?");
                $sectionid = $DBH->lastInsertId();
                $STH->bindParam(1, $sectionid);
                $STH->bindParam(2, $active);
                $STH->bindParam(3, $_GET['sectionid']);
                $STH->bindParam(4, $_GET['moduleid']);
                $STH->execute();
//
//                //copy the subsection
//                $STH = $DBH->prepare("
//                                SELECT ss.subsection_id, ss.subsection_name, ss.subsection_description, sns.subsection_sort, sns.subsection_active
//                                FROM " . $dbPrefix . "subsection as ss
//                                INNER JOIN " . $dbPrefix . "section_subsection AS sns ON ss.subsection_id = sns.subsection_id
//                                WHERE sns.section_id = ?");
//                $STH->bindParam(1, $originalsectionid);
//                $STH->execute();
//
//                $STH->setFetchMode(PDO::FETCH_OBJ);
//                $i = 0;
//                while ($row = $STH->fetch()) {
//                    $subsectiontmp[$i] = $row;
//                    $i++;
//                }
//                //insert copied data
//                $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "subsection
//                                (subsection_name, subsection_description)
//                                VALUES (?, ?)");
//                for ($i = 0; $i < count($subsectiontmp); $i++) {
//                    $STH->bindParam(1, $subsectiontmp[$i]->subsection_name);
//                    $STH->bindParam(2, $subsectiontmp[$i]->subsection_description);
//                    $STH->execute();
//                    $newsubsectionidtmp[$i] = $DBH->lastInsertId();
//                }
//
//                //insert section_subsection link
//                $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "section_subsection
//                                (section_id, subsection_id, subsection_sort, subsection_active)
//                                VALUES (?, ?, ?, ?)");
//
//                for ($i = 0; $i < count($subsectiontmp); $i++) {
//                    $STH->bindParam(1, $sectionid);
//                    $STH->bindParam(2, $newsubsectionidtmp[$i]);
//                    $STH->bindParam(3, $subsectiontmp[$i]->subsection_sort);
//                    $STH->bindParam(4, $subsectiontmp[$i]->subsection_active);
//                    $STH->execute();
//                }
//
//                //copy the questions
//                $STH = $DBH->prepare("
//                                SELECT q.question_id, q.question_name, q.question_description, sq.question_sort, sq.question_active, sq.question_group, sq.question_exclass
//                                FROM " . $dbPrefix . "question as q
//                                INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.question_id = q.question_id
//                                WHERE sq.subsection_id = ?");
//                //insert questions
//                $STH2 = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "question
//                                (question_name, question_description)
//                                VALUES (?, ?)");
//                //connect question with subsection
//                $STH22 = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "subsection_question
//                                (subsection_id, question_id, question_sort, question_group, question_exclass, question_active)
//                                VALUES (?, ?, ?, ?, ?, ?)");
//                
//                //copy questionmodule
//                $STH3 = $DBH->prepare("
//                                SELECT questionmodule_id, question_id, questionmodule_description, questionmodule_type, questionmodule_sort
//                                FROM " . $dbPrefix . "questionmodule
//                                WHERE question_id = ?");
//                //insert questionmodule
//                $STH4 = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "questionmodule
//                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
//                                VALUES (?, ?, ?, ?)");
//                //copy questionmodule_values
//                $STH5 = $DBH->prepare("
//                                SELECT questionvalue_active, questionvalue_value
//                                FROM " . $dbPrefix . "questionmodule_value
//                                WHERE questionmodule_id = ?");
//                //insert questionmodule_values
//                $STH6 = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "questionmodule_value
//                                (questionmodule_id, questionvalue_active, questionvalue_value)
//                                VALUES (?, ?, ?)");
//
//
//                for ($i = 0; $i < count($subsectiontmp); $i++) {
//                    $STH->bindParam(1, $subsectiontmp[$i]->subsection_id);
//                    $STH->execute();
//                    $STH->setFetchMode(PDO::FETCH_OBJ);
//                    $j = 0;
//                    while ($row = $STH->fetch()) {
//                        $questiontmp[$i] = $row;
//                        $j++;
//                    }
//                    for ($j + 0; $j < count($questiontmp); $j++) {
//                        $STH2->bindParam(1, $questiontmp[$j]->question_name);
//                        $STH2->bindParam(2, $questiontmp[$j]->question_description);
//                        $STH2->execute();
//                        $newquestionid = $DBH->lastInsertId();
//                        
//                        //set link between subsection and question
//                        $STH22->bindParam(1, $newsubsectionidtmp[$j]);
//                        $STH22->bindParam(2, $newquestionid);
//                        $STH22->bindParam(3, $questiontmp[$j]->question_sort);
//                        $STH22->bindParam(4, $questiontmp[$j]->question_group);
//                        $STH22->bindParam(5, $questiontmp[$j]->question_exclass);
//                        $STH22->bindParam(6, $questiontmp[$j]->question_active);
//                        $STH22->execute();
//                        
//                        $STH3->bindParam(1, $questiontmp[$j]->question_id);
//                        $STH3->execute();
//                        $STH3->setFetchMode(PDO::FETCH_OBJ);
//
//                        $k = 0;
//                        while ($row = $STH3->fetch()) {
//                            $questionmoduletmp[$k] = $row;
//                            $k++;
//                        }
//
//                        //insert questionmodules
//                        for ($l = 0; $l < count($questionmoduletmp); $l++) {
//                            //question_id
//                            $STH4->bindParam(1, $newquestionid);
//                            //questionmodule_description
//                            $STH4->bindParam(2, $questionmoduletmp[$l]->questionmodule_description);
//                            //questionmodule_type
//                            $STH4->bindParam(3, $questionmoduletmp[$l]->questionmodule_type);
//                            //questionmodule_sort
//                            $STH4->bindParam(4, $questionmoduletmp[$l]->questionmodule_sort);
//                            $STH4->execute();
//                            $newquestionmodulevalueid = $DBH->lastInsertId();
//
//                            //copy the questionmodulevalues
//                            echo $questionmoduletmp[$l]->questionmodule_id;
//                            $STH5->bindParam(1, $questionmoduletmp[$l]->questionmodule_id);
//                            $STH5->execute();
//                            $STH5->setFetchMode(PDO::FETCH_OBJ);
//                            $m = 0;
//                            echo 'ok till now ';
//                            while ($row = $STH5->fetch()) {
//                                $questionmodulevaluetmp[$m] = $row;
//                                $m++;
//                                echo '<pre>';
//                                print_r($questionmodulevaluetmp);
//                                echo '</pre>';
//                            }
//
//                            //insert questionmodule_values
//                            for ($n = 0; $n < count($questionmodulevaluetmp); $n++) {
//                                echo 'im in the loop ';
//                                $STH6->bindParam(1, $newquestionmodulevalueid);
//                                echo $newquestionmodulevalueid;
//                                $STH6->bindParam(2, $questionmodulevaluetmp[$n]->questionvalue_active);
//                                echo $questionmodulevaluetmp[$n]->questionvalue_active;
//                                $STH6->bindParam(3, $questionmodulevaluetmp[$n]->questionvalue_value);
//                                echo $questionmodulevaluetmp[$n]->questionvalue_value;
//                                $STH6->execute();
//                                echo 'endloop';
//                            }
//                        }
//                    }
//                }
//                $STH->bindParam(1, $subsectiontmp[$i]->subsection_id);
//                $STH->execute();
//
//                $STH->setFetchMode(PDO::FETCH_OBJ);
//                $i = 0;
//                while ($row = $STH->fetch()) {
//                    $subsectiontmp[$i] = $row;
//                    $i++;
//                }
//                for ($i = 0; $i < count($subsectiontmp); $i++) {
//                    
//                }
                
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
            $nextpage = $httpprefix . $website . 'admin.php?mode=section&action=overview&moduleid=' . $_GET['moduleid'];
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
                                UPDATE " . $dbPrefix . "section
                                SET section_name = ?, section_description = ? 
                                WHERE section_id = ?");

                //bind variables
                $STH->bindParam(1, $_POST['name']);
                $STH->bindParam(2, $_POST['description']);
                $STH->bindParam(3, $_GET['sectionid']);
                //execute statement
                $STH->execute();
                $STH = $DBH->prepare("
                                UPDATE " . $dbPrefix . "module_section
                                SET section_active = ? 
                                WHERE section_id = ?
                                AND module_id = ?");

                $STH->bindParam(1, $active);
                $STH->bindParam(2, $_GET['sectionid']);
                $STH->bindParam(3, $_GET['moduleid']);

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
            $nextpage = $httpprefix . $website . 'admin.php?mode=section&action=overview&moduleid=' . $_GET['moduleid'];
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
            $STH = $DBH->prepare("SELECT section_id, section_sort 
                    FROM " . $dbPrefix . "module_section 
                    WHERE module_id = ?
                    AND section_sort = (                 
                        SELECT section_sort 
                        FROM " . $dbPrefix . "module_section 
                        WHERE section_id = ?
                        AND module_id = ?)-1");
            $STH->bindParam(1, $_GET['moduleid']);
            $STH->bindParam(2, $_GET['sectionid']);
            $STH->bindParam(3, $_GET['moduleid']);
            $STH->execute();
            //set fetch mode
            $STH->setFetchMode(PDO::FETCH_OBJ);

            //fetch data
            $row = $STH->fetch();

            $sectionSort = $row->section_sort;
            $sectionID = $row->section_id;

            //check if module is on place 1
            if (isset($row->section_id)) {
                //sort the modules
                try {
                    $STH = $DBH->prepare("UPDATE " . $dbPrefix . "module_section 
                        SET section_sort = ? WHERE section_id = ? AND module_id = ?");
                    $newsort = $sectionSort + 1;
                    $STH->bindParam(1, $newsort);
                    $STH->bindParam(2, $sectionID);
                    $STH->bindParam(3, $_GET['moduleid']);
                    $STH->execute();
                    $STH->bindParam(1, $sectionSort);
                    $STH->bindParam(2, $_GET['sectionid']);
                    $STH->bindParam(3, $_GET['moduleid']);
                    $STH->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
            }
        }
        if ($_GET['dir'] == 'down') {
            //get id from -1 module
            $STH = $DBH->prepare("SELECT section_id, section_sort 
                    FROM " . $dbPrefix . "module_section 
                    WHERE module_id = ?
                    AND section_sort = (
                        SELECT section_sort 
                        FROM " . $dbPrefix . "module_section 
                        WHERE section_id = ?
                        AND module_id = ?)+1");
            $STH->bindParam(1, $_GET['moduleid']);
            $STH->bindParam(2, $_GET['sectionid']);
            $STH->bindParam(3, $_GET['moduleid']);
            $STH->execute();
            //set fetch mode
            $STH->setFetchMode(PDO::FETCH_OBJ);

            //fetch data
            $row = $STH->fetch();

            $sectionSort = $row->section_sort;
            $sectionID = $row->section_id;

            //check if module is on last place
            if (isset($row->section_id)) {
                //sort the modules
                try {
                    $STH = $DBH->prepare("UPDATE " . $dbPrefix . "module_section 
                        SET section_sort = ? WHERE section_id = ? AND module_id = ?");
                    $newsort = $sectionSort - 1;
                    $STH->bindParam(1, $newsort);
                    $STH->bindParam(2, $sectionID);
                    $STH->bindParam(3, $_GET['moduleid']);
                    $STH->execute();
                    $STH->bindParam(1, $sectionSort);
                    $STH->bindParam(2, $_GET['sectionid']);
                    $STH->bindParam(3, $_GET['moduleid']);
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
    $nextpage = $httpprefix . $website . 'admin.php?mode=section&action=overview&moduleid=' . $_GET['moduleid'];
    header("Location: " . $nextpage);
    exit;
} elseif ($_GET['action'] == 'deletesection') {
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
            SELECT section_id, section_name
            FROM " . $dbPrefix . "section
            WHERE section_id = ?");

        $STH->bindParam(1, $_GET['sectionid']);
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);
        //fetch data
        $deletesectiondata = $STH->fetch();
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
        $nextpage = $httpprefix . $website . 'admin.php?mode=section&action=overview&moduleid=' . $_GET['moduleid'];
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
            SELECT s.section_id, s.section_name, ss.subsection_id, ss.subsection_name
            FROM " . $dbPrefix . "subsection AS ss
            INNER JOIN " . $dbPrefix . "section_subsection AS sns ON sns.subsection_id = ss.subsection_id
            INNER JOIN " . $dbPrefix . "section AS s ON s.section_id = sns.section_id
            WHERE sns.section_id = ?");

            $STH->bindParam(1, $_GET['sectionid']);
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
            $modErrorModule = 'Deletion not possible. You can not delete a Section where are still Subsections in it.';
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
                    SELECT module_id 
                    FROM " . $dbPrefix . "module_section
                    WHERE section_id = ?
                    AND module_id <> ?");

                $STH->bindParam(1, $_GET['sectionid']);
                $STH->bindParam(2, $_GET['moduleid']);
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


            if ($moresections == TRUE) {


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
                    $STH = $DBH->prepare("DELETE FROM " . $dbPrefix . "module_section WHERE section_id = ? AND module_id = ?");

                    $STH->bindParam(1, $_GET['sectionid']);
                    $STH->bindParam(2, $_GET['moduleid']);
                    //execute statement
                    $STH->execute();

                    //sort modules again
                    $STH = $DBH->prepare("SELECT section_id FROM " . $dbPrefix . "module_section WHERE module_id = ? ORDER BY section_sort");
                    $STH->bindParam(1, $_GET['moduleid']);
                    $STH->execute();
                    //set fetch mode
                    $STH->setFetchMode(PDO::FETCH_OBJ);
                    //fetch data
                    $i = 0;
                    while ($row = $STH->fetch()) {
                        $sorter[$i] = $row;
                        $i++;
                    }
                    $STH = $DBH->prepare("UPDATE " . $dbPrefix . "module_section SET section_sort = ? WHERE section_id = ? AND module_id = ?");
                    for ($i = 0; $i < count($sorter); $i++) {
                        $newsort = $i + 1;
                        $STH->bindParam(1, $newsort);
                        $STH->bindParam(2, $sorter[$i]->section_id);
                        $STH->bindParam(3, $_GET['moduleid']);
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
                    $STH = $DBH->prepare("DELETE FROM " . $dbPrefix . "section WHERE section_id = ?");

                    $STH->bindParam(1, $_GET['sectionid']);
                    //execute statement
                    $STH->execute();

                    //sort modules again
                    $STH = $DBH->prepare("SELECT section_id FROM " . $dbPrefix . "module_section WHERE module_id = ? ORDER BY section_sort");
                    $STH->bindParam(1, $_GET['moduleid']);
                    $STH->execute();
                    //set fetch mode
                    $STH->setFetchMode(PDO::FETCH_OBJ);
                    //fetch data
                    $i = 0;
                    while ($row = $STH->fetch()) {
                        $sorter[$i] = $row;
                        $i++;
                    }
                    $STH = $DBH->prepare("UPDATE " . $dbPrefix . "module_section SET section_sort = ? WHERE section_id = ? AND module_id = ?");
                    for ($i = 0; $i < count($sorter); $i++) {
                        $newsort = $i + 1;
                        $STH->bindParam(1, $newsort);
                        $STH->bindParam(2, $sorter[$i]->section_id);
                        $STH->bindParam(3, $_GET['moduleid']);
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
            $nextpage = $httpprefix . $website . 'admin.php?mode=section&action=overview&moduleid=' . $_GET['moduleid'];
            header("Location: " . $nextpage);
            exit;
        }
    }
}
?>
