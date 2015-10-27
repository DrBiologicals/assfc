<?php

//getting questions and subsections from database
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
            SELECT ss.subsection_id, ss.subsection_name, q.question_id, q.question_version, q.question_questiongroup, q.question_delete, q.question_name, q.question_description, sq.question_active, sq.question_group 
            FROM " . $dbPrefix . "question AS q
            INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.question_id = q.question_id
            INNER JOIN " . $dbPrefix . "subsection AS ss ON ss.subsection_id = sq.subsection_id
            WHERE ss.subsection_id = ?
            ORDER BY sq.question_sort");
    //execute statement
    $STH->bindParam(1, $_GET['subsectionid']);

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
    if (!isset($array[0]->question_name)) {
        $STH = $DBH->prepare("
            SELECT ss.subsection_id, ss.subsection_name 
            FROM " . $dbPrefix . "subsection AS ss
            WHERE ss.subsection_id = ?");
        $STH->bindParam(1, $_GET['subsectionid']);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $row = $STH->fetch();
        $subsectionID = $row->subsection_id;
        $subsectionName = $row->subsection_name;
    } else {
        $subsectionID = $array[0]->subsection_id;
        $subsectionName = $array[0]->subsection_name;
    }
    //close db connection
    $DBH = null;
} catch (PDOException $e) {
    echo $e->getMessage();
}
//If no Sectionid is provided by get, we net a list with names
if (!isset($_GET['subsectionid'])) {


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
if ($_GET['action'] == 'newquestion') {

    //getting the exection classes
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

        $STH = $DBH->prepare("
            SELECT exclass_id, exclass_name 
            FROM " . $dbPrefix . "exclass
            ORDER BY exclass_sort");
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);

        $i = 0;
        while ($row = $STH->fetch()) {
            $execution[$i] = $row;
            $i++;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    //getting the groups
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

        $STH = $DBH->prepare("
            SELECT question_group 
            FROM " . $dbPrefix . "subsection_question
            WHERE subsection_id = ?
            GROUP BY question_group
            ORDER BY question_group");
        $STH->bindParam(1, $_GET['subsectionid']);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);

        $i = 0;
        while ($row = $STH->fetch()) {
            $group[$i] = $row;
            $i++;
        }
        $maxgroup = $group[count($group) - 1]->question_group + 1;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    //Stuff for Radio Buttons
    //create new lines for radio
    if (isset($_POST['checkradio'])) {
        $radiocount = $_POST['radiocount'] + $_POST['radiomore'];

        $j = 0;
        for ($i = 0; $i < count($_POST['radioinput']); $i++) {
            if (!isset($_POST['radiodelete'][$i])) {
                $radioinput[$j] = $_POST['radioinput'][$i];
                $j++;
            }
        }
        if (isset($_POST['radiodelete'])) {
            $radiocount = count($radioinput);
        }
        $j = 0;
        for ($i = 0; $i < count($_POST['radioinput']); $i++) {
            if ($_POST['radio_req'][$j] == $i) {
                $radiorequire[$i] = 1;
                $j++;
            } else {
                $radiorequire[$i] = 0;
            }
        }
    }

    //Stuff for Checkboxes
    //create new lines for check
    if (isset($_POST['checkcheck'])) {
        $checkcount = $_POST['checkcount'] + $_POST['checkmore'];

        $j = 0;
        for ($i = 0; $i < count($_POST['checkinput']); $i++) {
            if (!isset($_POST['checkdelete'][$i])) {
                $checkinput[$j] = $_POST['checkinput'][$i];
                $j++;
            }
        }
        if (isset($_POST['checkdelete'])) {
            $checkcount = count($checkinput);
        }
        $j = 0;
        for ($i = 0; $i < count($_POST['checkinput']); $i++) {
            if ($_POST['check'][$j] == $i) {
                $checkselect[$i] = 1;
                $j++;
            } else {
                $checkselect[$i] = 0;
            }
        }
        for ($i = 0; $i < count($_POST['checkinput']); $i++) {
            if ($_POST['check_req'][$j] == $i) {
                $checkrequire[$i] = 1;
                $j++;
            } else {
                $checkrequire[$i] = 0;
            }
        }
    }

    //Stuff for single line textfield
    //create new lines for single textfield
    if (isset($_POST['checksingle'])) {
        $singlecount = $_POST['singlecount'] + $_POST['singlemore'];

        $j = 0;
        for ($i = 0; $i < count($_POST['singleinput']); $i++) {
            if (!isset($_POST['singledelete'][$i])) {
                $singleinput[$j] = $_POST['singleinput'][$i];
                $j++;
            }
        }
        if (isset($_POST['singledelete'])) {
            $singlecount = count($singleinput);
        }
    }

    //Delete Questionmodule
    if (isset($_POST['deletemodule'])) {
        if (isset($_POST['deletemodule']['check'])) {
            unset($_POST['checkcheck']);
        } elseif (isset($_POST['deletemodule']['radio'])) {
            unset($_POST['checkradio']);
        } elseif (isset($_POST['deletemodule']['single'])) {
            unset($_POST['checksingle']);
        } elseif (isset($_POST['deletemodule']['multi'])) {
            unset($_POST['checkmulti']);
        } elseif (isset($_POST['deletemodule']['upload'])) {
            unset($_POST['checkupload']);
        }
    }



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
        //checking question description
        if (strlen($_POST['description']) > 65535) {
            $modError = TRUE;
            $modErrorDescription = 'Only 65535 characters allowed as question description. You have used ' . strlen($_POST['description']) . '.';
        }

        //checking question reference
        if (strlen($_POST['reference']) > 255) {
            $modError = TRUE;
            $modErrorReference = 'Only 255 characters allowed as reference. You have used ' . strlen($_POST['reference']) . '.';
        }

        //checking question help
        if (strlen($_POST['help']) > 65535) {
            $modError = TRUE;
            $modErrorHelp = 'Only 65535 characters allowed as question help. You have used ' . strlen($_POST['help']) . '.';
        }

        //check if there is at least one questionmodule selected
        if (!isset($_POST['checkradio']) && !isset($_POST['checkcheck']) && !isset($_POST['checksingle']) && !isset($_POST['checkmulti']) && !isset($_POST['checkupload'])) {
            $modError = TRUE;
            $modErrorquestionmodule = 'Please choose at least one Question module.';
        }

        //checking questionmodules
        //check radiobuttons
        if (isset($_POST['checkradio'])) {
            if (!isset($_POST['radioinput'])) {
                $modError = TRUE;
                $modErrorRadio = 'Empty options are not possible.';
            }
            for ($i = 0; $i < count($_POST['radioinput']); $i++) {
                if (strlen($_POST['radioinput'][$i]) > 255) {
                    $modError = TRUE;
                    $modErrorRadio = 'Please make shure that every option has only 255 characters.';
                } elseif ($_POST['radioinput'][$i] == '') {
                    $modError = TRUE;
                    $modErrorRadio = 'Empty options are not possible.';
                }
            }
        }
        //check checkboxes
        if (isset($_POST['checkcheck'])) {
            if (!isset($_POST['checkinput'])) {
                $modError = TRUE;
                $modErrorCheck = 'Empty options are not possible.';
            }
            for ($i = 0; $i < count($_POST['checkinput']); $i++) {
                if (strlen($_POST['checkinput'][$i]) > 255) {
                    $modError = TRUE;
                    $modErrorCheck = 'Please make shure that every option has only 255 characters.';
                } elseif ($_POST['checkinput'][$i] == '') {
                    $modError = TRUE;
                    $modErrorCheck = 'Empty options are not possible.';
                }
            }
        }
        //check singlelinetext
        if (isset($_POST['checksingle'])) {
            if (!isset($_POST['singleinput'])) {
                $modError = TRUE;
                $modErrorSingle = 'Empty options are not possible.';
            }
            for ($i = 0; $i < count($_POST['singleinput']); $i++) {
                if (strlen($_POST['singleinput'][$i]) > 255) {
                    $modError = TRUE;
                    $modErrorSingle = 'Please make shure that every option has only 255 characters.';
                } elseif ($_POST['singleinput'][$i] == '') {
                    $modError = TRUE;
                    $modErrorSingle = 'Empty options are not possible.';
                }
            }
        }
        //check multilinetext
        if (isset($_POST['checkmulti'])) {
            
        }
        //check upload
        if (isset($_POST['checkupload'])) {
            //no check is needed
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

                $DBH->beginTransaction();
                //make number for question_sort
                $STH = $DBH->prepare("SELECT MAX(question_sort) AS 'max' FROM " . $dbPrefix . "subsection_question WHERE subsection_id = ?");
                //execute statement
                $STH->bindParam(1, $_GET['subsectionid']);
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
                if ($_POST['active'] == 'on') {
                    $active = 1;
                } else {
                    $active = 0;
                }

                //insert data into database
                $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "question
                                (question_version, question_name, question_description, question_help, question_reference)
                                VALUES (1, ?, ?, ?, ?)");
                $STH->bindParam(1, $_POST['name']);
                $STH->bindParam(2, $_POST['description']);
                $STH->bindParam(3, $_POST['help']);
                $STH->bindParam(4, $_POST['reference']);

                $STH->execute();

                $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "subsection_question
                                (subsection_id, question_id, question_sort, question_active, question_group, question_exclass)
                                VALUES (?, ?, ?, ?, ?, ?)");
                $question = $DBH->lastInsertId();
                $STH->bindParam(1, $_GET['subsectionid']);
                $STH->bindParam(2, $question);
                $STH->bindParam(3, $sort);
                $STH->bindParam(4, $active);
                $STH->bindParam(5, $_POST['group']);
                $STH->bindParam(6, $_POST['execution']);
                //execute statement
                $STH->execute();

                //Insert questionmodules
                //insert radio buttons
                if (isset($_POST['checkradio'])) {


                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule
                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
                                VALUES (?, ?, ?, ?)");
                    $STH->bindParam(1, $question);
                    $STH->bindParam(2, $_POST['descriptionradio']);
                    $type = 1;
                    $STH->bindParam(3, $type);
                    //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
                    $sorting = 1;
                    $STH->bindParam(4, $sorting);
                    $STH->execute();


                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule_value
                                (questionmodule_id, questionvalue_active, questionvalue_value, questionvalue_required)
                                VALUES (?, ?, ?, ?)");
                    $questionmoduleid = $DBH->lastInsertId();
                    for ($i = 0; $i < count($_POST['radioinput']); $i++) {

                        $STH->bindParam(1, $questionmoduleid);
                        if ($_POST['radio'] == $i) {
                            $questionmoduleactive = 1;
                        } else {
                            $questionmoduleactive = 0;
                        }
                        if ($_POST['radio_req'][$i] == '1') {
                            $questionmodulerequire = 1;
                        } else {
                            $questionmodulerequire = 0;
                        }
                        $STH->bindParam(2, $questionmoduleactive);
                        $STH->bindParam(3, $_POST['radioinput'][$i]);
                        $STH->bindParam(4, $questionmodulerequire);
                        $STH->execute();
                    }
                }

                //insert checkboxes
                if (isset($_POST['checkcheck'])) {
                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule
                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
                                VALUES (?, ?, ?, ?)");
                    $STH->bindParam(1, $question);
                    $STH->bindParam(2, $_POST['descriptioncheck']);
                    $type = 2;
                    $STH->bindParam(3, $type);
                    //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
                    $sorting = 1;
                    $STH->bindParam(4, $sorting);
                    $STH->execute();


                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule_value
                                (questionmodule_id, questionvalue_active, questionvalue_value, questionvalue_required)
                                VALUES (?, ?, ?, ?)");
                    $questionmoduleid = $DBH->lastInsertId();
                    for ($i = 0; $i < count($_POST['checkinput']); $i++) {

                        $STH->bindParam(1, $questionmoduleid);
                        if ($_POST['check'][$i] == '1') {
                            $questionmoduleactive = 1;
                        } else {
                            $questionmoduleactive = 0;
                        }
                        if ($_POST['check_req'][$i] == '1') {
                            $questionmodulerequire = 1;
                        } else {
                            $questionmodulerequire = 0;
                        }
                        $STH->bindParam(2, $questionmoduleactive);
                        $STH->bindParam(3, $_POST['checkinput'][$i]);
                        $STH->bindParam(4, $questionmodulerequire);
                        $STH->execute();
                    }
                }

                //insert single text lines
                if (isset($_POST['checksingle'])) {
                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule
                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
                                VALUES (?, ?, ?, ?)");
                    $STH->bindParam(1, $question);
                    $STH->bindParam(2, $_POST['descriptionsingle']);
                    $type = 3;
                    $STH->bindParam(3, $type);
                    //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
                    $sorting = 1;
                    $STH->bindParam(4, $sorting);
                    $STH->execute();


                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule_value
                                (questionmodule_id, questionvalue_value, questionvalue_required)
                                VALUES (?, ?, ?)");
                    $questionmoduleid = $DBH->lastInsertId();
                    for ($i = 0; $i < count($_POST['singleinput']); $i++) {

                        $STH->bindParam(1, $questionmoduleid);
                        $STH->bindParam(2, $_POST['singleinput'][$i]);
                        if ($_POST['single_req'][$i] == '1') {
                            $questionmodulerequire = 1;
                        } else {
                            $questionmodulerequire = 0;
                        }
                        $STH->bindParam(3, $questionmodulerequire);
                        $STH->execute();
                    }
                }

                //insert multi text lines
                if (isset($_POST['checkmulti'])) {
                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule
                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
                                VALUES (?, ?, ?, ?)");
                    $STH->bindParam(1, $question);
                    if (!isset($_POST['descriptionmulti'])) {
                        $_POST['descriptionmulti'] = '';
                    }
                    $STH->bindParam(2, $_POST['descriptionmulti']);
                    $type = 4;
                    $STH->bindParam(3, $type);
                    //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
                    $sorting = 1;
                    $STH->bindParam(4, $sorting);
                    $STH->execute();

                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule_value
                                (questionmodule_id, questionvalue_required)
                                VALUES (?, ?)");
                    $questionmoduleid = $DBH->lastInsertId();
                    $STH->bindParam(1, $questionmoduleid);
                    if ($_POST['multi_req'] == '1') {
                        $questionmodulerequire = 1;
                    } else {
                        $questionmodulerequire = 0;
                    }
                    $STH->bindParam(2, $questionmodulerequire);
                    $STH->execute();
                }

                //insert upload module
                if (isset($_POST['checkupload'])) {
                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule
                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
                                VALUES (?, ?, ?, ?)");
                    $STH->bindParam(1, $question);
                    if (!isset($_POST['descriptionupload'])) {
                        $_POST['descriptionupload'] = '';
                    }
                    $STH->bindParam(2, $_POST['descriptionupload']);
                    $type = 5;
                    $STH->bindParam(3, $type);
                    //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
                    $sorting = 1;
                    $STH->bindParam(4, $sorting);
                    $STH->execute();

                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule_value
                                (questionmodule_id, questionvalue_required)
                                VALUES (?, ?)");
                    $questionmoduleid = $DBH->lastInsertId();
                    $STH->bindParam(1, $questionmoduleid);
                    if ($_POST['upload_req'] == '1') {
                        $questionmodulerequire = 1;
                    } else {
                        $questionmodulerequire = 0;
                    }
                    $STH->bindParam(2, $questionmodulerequire);
                    $STH->execute();
                }
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
            $nextpage = $httpprefix . $website . 'admin.php?mode=question&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'] . '&subsectionid=' . $_GET['subsectionid'];
            header("Location: " . $nextpage);
            exit;
        }
    }
} elseif ($_GET['action'] == 'connectquestion') {
    //get subsections + question
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

        //get subsectionstuff
        $STH = $DBH->prepare("
            SELECT subsection_id, subsection_name 
            FROM " . $dbPrefix . "subsection
            WHERE subsection_id <> ?
            AND subsection_id IN (SELECT subsection_id FROM " . $dbPrefix . "subsection_question)");
        $STH->bindParam(1, $_GET['subsectionid']);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);


        //get questionstuff
        $STH2 = $DBH->prepare("
            SELECT q.question_id, q.question_name, q.question_description 
            FROM " . $dbPrefix . "question q
            INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.question_id = q.question_id
            WHERE sq.subsection_id = ?
            AND q.question_id NOT IN (SELECT question_id FROM " . $dbPrefix . "subsection_question WHERE subsection_id = ?)");
        $STH2->setFetchMode(PDO::FETCH_OBJ);

        //fetch data
        $i = 0;
        while ($row = $STH->fetch()) {
            $STH2->bindParam(1, $row->subsection_id);
            $STH2->bindParam(2, $_GET['subsectionid']);
            $STH2->execute();
            $j = 0;
            while ($row2 = $STH2->fetch()) {
                //check if this question is already in another subsection
                $isin = FALSE;
                for ($k = 0; $k < count($questionidtemp); $k++) {
                    if ($questionidtemp[$k] == $row2->question_id) {
                        $isin = TRUE;
                    }
                }
                if ($isin == FALSE) {
                    $questions[$i][$j] = $row2;
                    $questionidtemp[] = $row2->question_id;
                    $j++;
                }
            }
            $subsections[$i] = $row;
            $i++;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    if (isset($_POST['connect'])) {
        $checkarray = $_POST['check'];
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
            $STH = $DBH->prepare("SELECT MAX(question_sort) AS 'max' FROM " . $dbPrefix . "subsection_question WHERE subsection_id = ?");
            $STH->bindParam(1, $_GET['subsectionid']);
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
            INSERT INTO  " . $dbPrefix . "subsection_question
            (subsection_id, question_id, question_sort, question_active)
                                VALUES (?, ?, ?, ?)");
            //execute statement
            $active = '0';
            for ($i = 0; $i < count($checkarray); $i++) {
                $STH->bindParam(1, $_GET['subsectionid']);
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
        $nextpage = $httpprefix . $website . 'admin.php?mode=question&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'] . '&subsectionid=' . $_GET['subsectionid'];
        header("Location: " . $nextpage);
        exit;
    }
} elseif ($_GET['action'] == 'activatequestion') {
    try {
        $DBH = new PDO(
                        "mysql:host=$dbHost;dbname=$dbName",
                        $dbUser,
                        $dbPasswd,
                        array(
                            PDO::ATTR_ERRMODE => $errormode,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));

        $STH = $DBH->prepare("UPDATE " . $dbPrefix . "subsection_question 
                SET question_active = ? WHERE subsection_id = ? AND question_id = ?");
        //bind variables
        $STH->bindParam(1, $_GET['active']);
        $STH->bindParam(2, $_GET['subsectionid']);
        $STH->bindParam(3, $_GET['questionid']);
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
    $nextpage = $httpprefix . $website . 'admin.php?mode=question&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'] . '&subsectionid=' . $_GET['subsectionid'];
    header("Location: " . $nextpage);
    exit;
} elseif ($_GET['action'] == 'duplicatequestion') {

    //get questiondata
    $questiontmp = getQuestions(TRUE, $_GET['subsectionid'], $_GET['questionid']);

    //make a new entry in the database
    try {
        $DBH = new PDO(
                        "mysql:host=$dbHost;dbname=$dbName",
                        $dbUser,
                        $dbPasswd,
                        array(
                            PDO::ATTR_ERRMODE => $errormode,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));

        $DBH->beginTransaction();

        //make number for question_sort
        $STH = $DBH->prepare("SELECT MAX(question_sort) AS 'max' FROM " . $dbPrefix . "subsection_question WHERE subsection_id = ?");
        //execute statement
        $STH->bindParam(1, $_GET['subsectionid']);
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

        $STH = $DBH->prepare("INSERT INTO " . $dbPrefix . "question
                                (question_version, question_name, question_description, question_help, question_reference)
                                VALUES (1, ?, ?, ?, ?)");
        //bind variables
        // change name of question:
        $newquestionname = "[copy]" . $questiontmp[0]->question_name;
        $STH->bindParam(1, $newquestionname);
        $STH->bindParam(2, $questiontmp[0]->question_description);
        $STH->bindParam(3, $questiontmp[0]->question_help);
        $STH->bindParam(4, $questiontmp[0]->question_reference);
        //execute statement
        $STH->execute();


        $STH = $DBH->prepare("INSERT INTO " . $dbPrefix . "subsection_question
                                (subsection_id, question_id, question_sort, question_group, question_exclass, question_active)
                                VALUES (?, ?, ?, ?, ?, ?)");
        //bind variables
        $newquestionid = $DBH->lastInsertId();
        $STH->bindParam(1, $_GET['subsectionid']);
        $STH->bindParam(2, $newquestionid);
        $STH->bindParam(3, $sort);
        $STH->bindParam(4, $questiontmp[0]->question_group);
        $STH->bindParam(5, $questiontmp[0]->question_exclass);
        $STH->bindParam(6, $questiontmp[0]->question_active);
        //execute statement
        $STH->execute();

        $STH1 = $DBH->prepare("
            SELECT qm.* 
            FROM " . $dbPrefix . "questionmodule AS qm
            WHERE question_id = ?");
        $STH2 = $DBH->prepare("
            SELECT qmv.* 
            FROM " . $dbPrefix . "questionmodule_value AS qmv 
            WHERE questionmodule_id = ?");
        $STH3 = $DBH->prepare("
            INSERT INTO " . $dbPrefix . "questionmodule
            (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
            VALUES (?, ?, ?, ?)");
        $STH4 = $DBH->prepare("
            INSERT INTO " . $dbPrefix . "questionmodule_value
            (questionmodule_id, questionvalue_active, questionvalue_value)
            VALUES (?, ?, ?)");

        $STH1->bindParam(1, $_GET['questionid']);
        $STH1->execute();
        $STH1->setFetchMode(PDO::FETCH_OBJ);

        while ($row1 = $STH1->fetch()) {

            $STH3->bindParam(1, $newquestionid);
            $STH3->bindParam(2, $row1->questionmodule_description);
            $STH3->bindParam(3, $row1->questionmodule_type);
            $STH3->bindParam(4, $row1->questionmodule_sort);
            $STH3->execute();
            $STH2->bindParam(1, $row1->questionmodule_id);
            $STH2->execute();
            $STH2->setFetchMode(PDO::FETCH_OBJ);
            while ($row2 = $STH2->fetch()) {
                $newquestionmoduleid = $DBH->lastInsertId();
                echo $newquestionmoduleid;
                echo "<br>";
                echo $row2->questionvalue_value;
                $STH4->bindParam(1, $newquestionmoduleid);
                $STH4->bindParam(2, $row2->questionvalue_active);
                $STH4->bindParam(3, $row2->questionvalue_value);
                $STH4->execute();
            }
        }

        //commit and close db connection
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
    $nextpage = $httpprefix . $website . 'admin.php?mode=question&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'] . '&subsectionid=' . $_GET['subsectionid'];
    header("Location: " . $nextpage);
    exit;
} elseif ($_GET['action'] == 'editquestion') {

    //check if question is used in another subsection
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
            SELECT ss.subsection_id, ss.subsection_name 
            FROM " . $dbPrefix . "subsection AS ss
            INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.subsection_id = ss.subsection_id
            WHERE sq.question_id = ?
            AND ss.subsection_id <> ?");

        $STH->bindParam(1, $_GET['questionid']);
        $STH->bindParam(2, $_GET['subsectionid']);
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

    //getting question data from database
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
        $STH = $DBH->prepare("
            SELECT q.question_id, q.question_version, q.question_name, q.question_description, q.question_help, q.question_reference, sq.question_active, sq.question_group, sq.question_exclass
            FROM " . $dbPrefix . "question AS q
            INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.question_id = q.question_id
            WHERE q.question_id = ?");

        $STH->bindParam(1, $_GET['questionid']);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);

        $questiondata = $STH->fetch();
        //getting questionmodule data
        $STH = $DBH->prepare("
            SELECT questionmodule_id, questionmodule_type, questionmodule_description
            FROM " . $dbPrefix . "questionmodule
            WHERE question_id = ?
            ORDER BY questionmodule_sort");
        $STH->bindParam(1, $_GET['questionid']);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $i = 0;
        while ($row = $STH->fetch()) {
            $questionmoduledata[$i] = $row;
            $i++;
        }
        //getting groups from db
        $STH = $DBH->prepare("
            SELECT question_group 
            FROM " . $dbPrefix . "subsection_question
            WHERE subsection_id = ?
            GROUP BY question_group
            ORDER BY question_group");
        $STH->bindParam(1, $_GET['subsectionid']);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);

        $i = 0;
        while ($row = $STH->fetch()) {
            $group[$i] = $row;
            $i++;
        }
        $maxgroup = $group[count($group) - 1]->question_group + 1;

        //getting questionmodulevalue data only if questionmodule is 
        // typeid 1: Radiobutton
        // typeid 2: checkbox
        // typeid 3: singlelinetext
        //The other questionmodules typse don't have a questionvalue       
        $STH = $DBH->prepare("
            SELECT qv.questionmodule_id, qv.questionvalue_id, qv.questionvalue_active, qv.questionvalue_required, qv.questionvalue_value
            FROM " . $dbPrefix . "questionmodule_value AS qv
            INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qv.questionmodule_id
            WHERE qm.questionmodule_id = ?
            AND qm.questionmodule_type IN(1,2,3,4,5)
            ORDER BY qm.questionmodule_sort");
        for ($i = 0; $i < count($questionmoduledata); $i++) {
            $STH->bindParam(1, $questionmoduledata[$i]->questionmodule_id);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_OBJ);
            $j = 0;
            while ($row = $STH->fetch()) {
                $questionmodulevalue[$questionmoduledata[$i]->questionmodule_id][$j] = $row;
                $j++;
            }
        }

        //get execution class
        $STH = $DBH->prepare("
            SELECT exclass_id, exclass_name 
            FROM " . $dbPrefix . "exclass
            ORDER BY exclass_sort");
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);

        $i = 0;
        while ($row = $STH->fetch()) {
            $execution[$i] = $row;
            $i++;
        }

        //close db connection
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    if (isset($_POST['refresh']) || isset($_POST['edit']) || isset($_POST['deletemodule']) || isset($_POST['deletevalue'])) {

        //this part of the program is for data trough $_POST
        $questionname = $_POST['name'];
        $questionexclass = $execution;
        $questionexclassselect = $_POST['execution'];
        $questiongroup = $group;
        $questiongroupselect = $_POST['group'];
        $questiondescription = $_POST['description'];
        $questionhelp = $_POST['help'];
        $questionreference = $_POST['reference'];

        //radiostuff
        if (!isset($_POST['deletemodule']['radio'])) {
            $checkradio = $_POST['checkradio'];
        }
        $radiodescription = $_POST['descriptionradio'];
        $radiocheck = $_POST['radio'];
        $radioreq = $_POST['radio_req'];

        //make new radiovalues
        $radiocount = $_POST['radiocount'] + $_POST['radiomore'];
        $k = 0;
        for ($j = 0; $j < $radiocount; $j++) {

            if (!isset($_POST['deletevalue']['radio'][$j])) {
                $radioinput[$k] = $_POST['radioinput'][$j];
                $k++;
            }
        }
        if (isset($_POST['deletevalue']['radio'])) {
            $radiocount = $k;
        }

        //checkstuff
        if (!isset($_POST['deletemodule']['check'])) {
            $checkcheck = $_POST['checkcheck'];
        }
        $checkdescription = $_POST['descriptioncheck'];
        $checkactive = $_POST['check'];
        $checkreq = $_POST['check_req'];

        //make new checkvalues
        $checkcount = $_POST['checkcount'] + $_POST['checkmore'];
        $k = 0;
        for ($j = 0; $j < $checkcount; $j++) {

            if (!isset($_POST['deletevalue']['check'][$j])) {
                $checkinput[$k] = $_POST['checkinput'][$j];
                $k++;
            }
        }
        if (isset($_POST['deletevalue']['check'])) {
            $checkcount = $k;
        }

        //single line stuff
        if (!isset($_POST['deletemodule']['single'])) {
            $checksingle = $_POST['checksingle'];
        }
        $singledescription = $_POST['descriptionsingle'];
        $singlereq = $_POST['single_req'];

        //make new checkvalues
        $singlecount = $_POST['singlecount'] + $_POST['singlemore'];
        $k = 0;
        for ($j = 0; $j < $singlecount; $j++) {

            if (!isset($_POST['deletevalue']['single'][$j])) {
                $singleinput[$k] = $_POST['singleinput'][$j];
                $k++;
            }
        }
        if (isset($_POST['deletevalue']['single'])) {
            $singlecount = $k;
        }

        //multi line stuff
        if (!isset($_POST['deletemodule']['multi'])) {
            $checkmulti = $_POST['checkmulti'];
        }
        $multireq = $_POST['multi_req'];
        $multidescription = $_POST['descriptionmulti'];

        //multi line stuff
        if (!isset($_POST['deletemodule']['upload'])) {
            $checkupload = $_POST['checkupload'];
        }
        $uploadreq = $_POST['upload_req'];
        $uploaddescription = $_POST['descriptionupload'];
    } else {

        //This part of the programm is for inital data from database
        //data for questions

        $questionexclass = $execution;
        $questionexclassselect = $questiondata->question_exclass;
        $questiongroup = $group;
        $questiongroupselect = $questiondata->question_group;
        $questionname = $questiondata->question_name;
        $questiondescription = $questiondata->question_description;
        $questionhelp = $questiondata->question_help;
        $questionreference = $questiondata->question_reference;

        if ($questiondata->question_active == 1) {
            $questionactive = TRUE;
        }
        for ($i = 0; $i < count($questionmoduledata); $i++) {

            //check if radio is in database
            if ($questionmoduledata[$i]->questionmodule_type == 1) {
                $checkradio = 'radio';
                $radiodescription = $questionmoduledata[$i]->questionmodule_description;
                $radiocount = count($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id]);
                for ($j = 0; $j < count($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id]); $j++) {
                    $radioinput[$j] = $questionmodulevalue[$questionmoduledata[$i]->questionmodule_id][$j]->questionvalue_value;
                    if ($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id][$j]->questionvalue_active == '1') {
                        $radiocheck = $j;
                    }
                    if ($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id][$j]->questionvalue_required == '1') {
                        $radioreq[$j] = 1;
                    }
                }
            }
            //check if checkbox is in database
            if ($questionmoduledata[$i]->questionmodule_type == 2) {
                $checkcheck = 'check';
                $checkdescription = $questionmoduledata[$i]->questionmodule_description;
                $checkcount = count($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id]);
                for ($j = 0; $j < count($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id]); $j++) {
                    $checkinput[$j] = $questionmodulevalue[$questionmoduledata[$i]->questionmodule_id][$j]->questionvalue_value;
                    if ($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id][$j]->questionvalue_active == '1') {
                        $checkactive[$j] = 1;
                    }
                    if ($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id][$j]->questionvalue_required == '1') {
                        $checkreq[$j] = 1;
                    }
                }
            }
            //check if singleline textfield is in database
            if ($questionmoduledata[$i]->questionmodule_type == 3) {
                $checksingle = 'single';
                $singledescription = $questionmoduledata[$i]->questionmodule_description;
                $singlecount = count($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id]);
                for ($j = 0; $j < count($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id]); $j++) {
                    $singleinput[$j] = $questionmodulevalue[$questionmoduledata[$i]->questionmodule_id][$j]->questionvalue_value;
                    if ($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id][$j]->questionvalue_active == '1') {
                        $singleactive[$j] = 1;
                    }
                    if ($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id][$j]->questionvalue_required == '1') {
                        $singlereq[$j] = 1;
                    }
                }
            }
            //check if multi line textfield is in database
            if ($questionmoduledata[$i]->questionmodule_type == 4) {
                $checkmulti = 'multi';
                $multidescription = $questionmoduledata[$i]->questionmodule_description;
                if ($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id][0]->questionvalue_required == '1') {
                    $multireq = 1;
                }
            }
            //check if upload data is in database
            if ($questionmoduledata[$i]->questionmodule_type == 5) {
                $checkupload = 'upload';
                $uploaddescription = $questionmoduledata[$i]->questionmodule_description;
                if ($questionmodulevalue[$questionmoduledata[$i]->questionmodule_id][0]->questionvalue_required == '1') {
                    $uploadreq = 1;
                }
            }
        }
    }

    if (isset($_POST['edit'])) {
        $modError = FALSE;
        //check input data
        //checking question name
        if ($_POST['name'] == '') {
            $modError = TRUE;
            $modErrorName = 'Required field.';
        } elseif (strlen($_POST['name']) > 255) {
            $modError = TRUE;
            $modErrorName = 'Only 255 characters allowed as question name. You have used ' . strlen($_POST['name']) . '.';
        }
        //checking question description
        if (strlen($_POST['description']) > 65535) {
            $modError = TRUE;
            $modErrorDescription = 'Only 65535 characters allowed as question description. You have used ' . strlen($_POST['description']) . '.';
        }

        //checking question help
        if (strlen($_POST['help']) > 65535) {
            $modError = TRUE;
            $modErrorHelp = 'Only 65535 characters allowed as question help. You have used ' . strlen($_POST['help']) . '.';
        }
        //checking question reference
        if (strlen($_POST['reference']) > 255) {
            $modError = TRUE;
            $modErrorReference = 'Only 255 characters allowed as question reference. You have used ' . strlen($_POST['reference']) . '.';
        }

        //check if there is at least one questionmodule selected
        if (!isset($_POST['checkradio']) && !isset($_POST['checkcheck']) && !isset($_POST['checksingle']) && !isset($_POST['checkmulti']) && !isset($_POST['checkupload'])) {
            $modError = TRUE;
            $modErrorquestionmodule = 'Please choose at least one Question module.';
        }

        //checking questionmodules
        //check radiobuttons
        if (isset($_POST['checkradio'])) {
            if (!isset($_POST['radioinput'])) {
                $modError = TRUE;
                $modErrorRadio = 'Empty options are not possible.';
            }
            for ($i = 0; $i < count($_POST['radioinput']); $i++) {
                if (strlen($_POST['radioinput'][$i]) > 255) {
                    $modError = TRUE;
                    $modErrorRadio = 'Please make shure that every option has only 255 characters.';
                } elseif ($_POST['radioinput'][$i] == '') {
                    $modError = TRUE;
                    $modErrorRadio = 'Empty options are not possible.';
                }
            }
        }
        //check checkboxes
        if (isset($_POST['checkcheck'])) {
            if (!isset($_POST['checkinput'])) {
                $modError = TRUE;
                $modErrorCheck = 'Empty options are not possible.';
            }
            for ($i = 0; $i < count($_POST['checkinput']); $i++) {
                if (strlen($_POST['checkinput'][$i]) > 255) {
                    $modError = TRUE;
                    $modErrorCheck = 'Please make shure that every option has only 255 characters.';
                } elseif ($_POST['checkinput'][$i] == '') {
                    $modError = TRUE;
                    $modErrorCheck = 'Empty options are not possible.';
                }
            }
        }
        //check singlelinetext
        if (isset($_POST['checksingle'])) {
            if (!isset($_POST['singleinput'])) {
                $modError = TRUE;
                $modErrorSingle = 'Empty options are not possible.';
            }
            for ($i = 0; $i < count($_POST['singleinput']); $i++) {
                if (strlen($_POST['singleinput'][$i]) > 255) {
                    $modError = TRUE;
                    $modErrorSingle = 'Please make shure that every option has only 255 characters.';
                } elseif ($_POST['singleinput'][$i] == '') {
                    $modError = TRUE;
                    $modErrorSingle = 'Empty options are not possible.';
                }
            }
        }
        //check multilinetext
        if (isset($_POST['checkmulti'])) {
            //no check is needed
        }
        //check upload
        if (isset($_POST['checkupload'])) {
            //no check is needed
        }
        //everything went right - write data to database
        if ($modError == FALSE && isset($_POST['edit'])) {
            $questionversion = ($questiondata->question_version + 1);
            //there are two different ways to set the data into the database
            //1. Question will be separated (a copy of the question will be created)
//            if ($_POST['separate'] == 'on') {
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

                $DBH->beginTransaction();

                //make number for question_sort
                $STH = $DBH->prepare("SELECT MAX(question_sort) AS 'max' FROM " . $dbPrefix . "subsection_question WHERE subsection_id = ?");
                //execute statement
                $STH->bindParam(1, $_GET['subsectionid']);
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
                if ($_POST['active'] == 'on') {
                    $active = 1;
                } else {
                    $active = 0;
                }

                //insert new question
                $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "question
                                (question_name, question_version, question_description, question_help, question_reference)
                                VALUES (?, ?, ?, ?, ?)");
                $STH->bindParam(1, $_POST['name']);
                $STH->bindParam(2, $questionversion);
                $STH->bindParam(3, $_POST['description']);
                $STH->bindParam(4, $_POST['help']);
                $STH->bindParam(5, $_POST['reference']);
                $STH->execute();
                $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "subsection_question 
                                (subsection_id, question_id, question_sort, question_group, question_exclass, question_active) 
                                VALUES (?, ?, ?, ?, ?, ?)");
                $STH->bindParam(1, $_GET['subsectionid']);
                $questionidtmp = $DBH->lastInsertId();
                $STH->bindParam(2, $questionidtmp);
                $STH->bindParam(3, $sort);
                $STH->bindParam(4, $_POST['group']);
                $STH->bindParam(5, $_POST['execution']);
                $STH->bindParam(6, $active);
                $STH->execute();



                //insert new questionmodules
                //Radio buttons
                if (isset($_POST['checkradio'])) {

                    //insert new question module
                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule
                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
                                VALUES (?, ?, ?, ?)");
                    $STH->bindParam(1, $questionidtmp);
                    $STH->bindParam(2, $_POST['descriptionradio']);
                    $type = 1;
                    $STH->bindParam(3, $type);
                    //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
                    $sorting = 1;
                    $STH->bindParam(4, $sorting);
                    $STH->execute();


                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule_value
                                (questionmodule_id, questionvalue_active, questionvalue_value, questionvalue_required)
                                VALUES (?, ?, ?, ?)");
                    $questionmoduleid = $DBH->lastInsertId();
                    for ($i = 0; $i < count($_POST['radioinput']); $i++) {

                        $STH->bindParam(1, $questionmoduleid);
                        if ($_POST['radio'] == $i) {
                            $questionmoduleactive = 1;
                        } else {
                            $questionmoduleactive = 0;
                        }
                        $STH->bindParam(2, $questionmoduleactive);
                        $STH->bindParam(3, $_POST['radioinput'][$i]);
                        if ($_POST['radio_req'][$i] == 1) {
                            $questionmodulerequired = 1;
                        } else {
                            $questionmodulerequired = 0;
                        }
                        $STH->bindParam(4, $questionmodulerequired);
                        $STH->execute();
                    }
                }

                //checkboxen

                if (isset($_POST['checkcheck'])) {

                    //insert new question module
                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule
                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
                                VALUES (?, ?, ?, ?)");
                    $STH->bindParam(1, $questionidtmp);
                    $STH->bindParam(2, $_POST['descriptioncheck']);
                    $type = 2;
                    $STH->bindParam(3, $type);
                    //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
                    $sorting = 1;
                    $STH->bindParam(4, $sorting);
                    $STH->execute();


                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule_value
                                (questionmodule_id, questionvalue_active, questionvalue_value, questionvalue_required)
                                VALUES (?, ?, ?, ?)");
                    $questionmoduleid = $DBH->lastInsertId();
                    for ($i = 0; $i < count($_POST['checkinput']); $i++) {

                        $STH->bindParam(1, $questionmoduleid);
                        if ($_POST['check'][$i] == 1) {
                            $questionmoduleactive = 1;
                        } else {
                            $questionmoduleactive = 0;
                        }
                        $STH->bindParam(2, $questionmoduleactive);
                        $STH->bindParam(3, $_POST['checkinput'][$i]);
                        if ($_POST['check_req'][$i] == 1) {
                            $questionmodulerequired = 1;
                        } else {
                            $questionmodulerequired = 0;
                        }
                        $STH->bindParam(4, $questionmodulerequired);
                        $STH->execute();
                    }
                }

                //single line text
                if (isset($_POST['checksingle'])) {

                    //insert new question module
                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule
                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
                                VALUES (?, ?, ?, ?)");
                    $STH->bindParam(1, $questionidtmp);
                    $STH->bindParam(2, $_POST['descriptionsingle']);
                    $type = 3;
                    $STH->bindParam(3, $type);
                    //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
                    $sorting = 1;
                    $STH->bindParam(4, $sorting);
                    $STH->execute();


                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule_value
                                (questionmodule_id, questionvalue_active, questionvalue_value, questionvalue_required)
                                VALUES (?, ?, ?, ?)");
                    $questionmoduleid = $DBH->lastInsertId();
                    for ($i = 0; $i < count($_POST['singleinput']); $i++) {

                        $STH->bindParam(1, $questionmoduleid);
                        if ($_POST['single'][$i] == 1) {
                            $questionmoduleactive = 1;
                        } else {
                            $questionmoduleactive = 0;
                        }
                        $STH->bindParam(2, $questionmoduleactive);
                        $STH->bindParam(3, $_POST['singleinput'][$i]);
                        if ($_POST['single_req'][$i] == 1) {
                            $questionmodulerequired = 1;
                        } else {
                            $questionmodulerequired = 0;
                        }
                        $STH->bindParam(4, $questionmodulerequired);
                        $STH->execute();
                    }
                }


                //multi text line
                if (isset($_POST['checkmulti'])) {

                    //insert new question module
                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule
                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
                                VALUES (?, ?, ?, ?)");
                    $STH->bindParam(1, $questionidtmp);
                    $STH->bindParam(2, $_POST['descriptionmulti']);
                    $type = 4;
                    $STH->bindParam(3, $type);
                    //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
                    $sorting = 1;
                    $STH->bindParam(4, $sorting);
                    $STH->execute();

                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule_value
                                (questionmodule_id, questionvalue_required)
                                VALUES (?,?)");
                    $questionmoduleid = $DBH->lastInsertId();
                    $STH->bindParam(1, $questionmoduleid);
                    if ($_POST['multi_req'] == 1) {
                        $questionmodulerequired = 1;
                    } else {
                        $questionmodulerequired = 0;
                    }
                    $STH->bindParam(2, $questionmodulerequired);
                    $STH->execute();
                }


                // upload module
                if (isset($_POST['checkupload'])) {

                    //insert new question module
                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule
                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
                                VALUES (?, ?, ?, ?)");
                    $STH->bindParam(1, $questionidtmp);
                    $STH->bindParam(2, $_POST['descriptionupload']);
                    $type = 5;
                    $STH->bindParam(3, $type);
                    //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
                    $sorting = 1;
                    $STH->bindParam(4, $sorting);
                    $STH->execute();

                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "questionmodule_value
                                (questionmodule_id, questionvalue_required)
                                VALUES (?,?)");
                    $questionmoduleid = $DBH->lastInsertId();
                    $STH->bindParam(1, $questionmoduleid);
                    if ($_POST['upload_req'] == 1) {
                        $questionmodulerequired = 1;
                    } else {
                        $questionmodulerequired = 0;
                    }
                    $STH->bindParam(2, $questionmodulerequired);
                    $STH->execute();
                }
                $delete_flag = 1;

                $STH = $DBH->prepare("UPDATE " . $dbPrefix . "question SET question_delete = ? WHERE question_id = ?");
                $STH->bindParam(1, $delete_flag);
                $STH->bindParam(2, $_GET['questionid']);
                $STH->execute();

                $STH = $DBH->prepare("UPDATE " . $dbPrefix . "subsection_question 
                SET question_active = '0' WHERE subsection_id = ? AND question_id = ?");
                //bind variables
                $STH->bindParam(1, $_GET['subsectionid']);
                $STH->bindParam(2, $_GET['questionid']);
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
//            } else {
//                $questionversion++;
//
//                //2. Question will not be separated (the question appears in different subsections but the data is the same)
//                //add to database
//                try {
//                    //connecting to database
//                    $DBH = new PDO(
//                                    "mysql:host=$dbHost;dbname=$dbName",
//                                    $dbUser,
//                                    $dbPasswd,
//                                    array(
//                                        PDO::ATTR_ERRMODE => $errormode,
//                                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
//                            ));
//
//                    $DBH->beginTransaction();
//                    //make number for module_active
//                    if ($_POST['active'] == 'on') {
//                        $active = 1;
//                    } else {
//                        $active = 0;
//                    }
//
//                    //update question data
//                    $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "question 
//                (question_version, question_name, question_description, question_help, question_reference)
//                VALUES(?,?,?,?,?)");
//                    $STH->bindParam(1, $questionversion);
//                    $STH->bindParam(2, $_POST['name']);
//                    $STH->bindParam(3, $_POST['description']);
//                    $STH->bindParam(4, $_POST['help']);
//                    $STH->bindParam(5, $_POST['reference']);
//                    $STH->execute();
//                    
//                                        //update question data
//                    $STH = $DBH->prepare("
//                                UPDATE " . $dbPrefix . "question 
//                                SET question_delete = ?)
//                                   WHERE question_id = ?");
//                    $delete = 1;
//                    $STH->bindParam(1, $delete);
//                    $STH->bindParam(2, $questiondata->question_id);
//                    $STH->execute();
//
//                    $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "subsection_question 
//                                (subsection_id,question_sort, question_group, question_exclass, question_active) 
//                                VALUES (?,?,?,?,?)");
//                    $STH->bindParam(1, $_GET['subsectionid']);
//                    $STH->bindParam(2, $sort);
//                    $STH->bindParam(3, $_POST['group']);
//                    $STH->bindParam(4, $_POST['execution']);
//                    $STH->bindParam(5, $active);
//                    $STH->execute();
//                    
////                    //Delete old and insert new questionmodules               
////                    //Radio buttons
////                    //delete old question module
////                    $STH = $DBH->prepare("
////                        DELETE FROM " . $dbPrefix . "questionmodule 
////                            WHERE question_id = ? 
////                            AND questionmodule_type = ?");
////                    $STH->bindParam(1, $_GET['questionid']);
////                    $questionmoduletype = 1;
////                    $STH->bindParam(2, $questionmoduletype);
////                    $STH->execute();
//
//                    if (isset($_POST['checkradio'])) {
//
//                        //insert new question module
//                        $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "questionmodule
//                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
//                                VALUES (?, ?, ?, ?)");
//                        $STH->bindParam(1, $_GET['questionid']);
//                        $STH->bindParam(2, $_POST['descriptionradio']);
//                        $type = 1;
//                        $STH->bindParam(3, $type);
//                        //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
//                        $sorting = 1;
//                        $STH->bindParam(4, $sorting);
//                        $STH->execute();
//
//
//                        $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "questionmodule_value
//                                (questionmodule_id, questionvalue_active, questionvalue_value, questionvalue_required)
//                                VALUES (?, ?, ?, ?)");
//                        $questionmoduleid = $DBH->lastInsertId();
//                        for ($i = 0; $i < count($_POST['radioinput']); $i++) {
//
//                            $STH->bindParam(1, $questionmoduleid);
//                            if ($_POST['radio'] == $i) {
//                                $questionmoduleactive = 1;
//                            } else {
//                                $questionmoduleactive = 0;
//                            }
//                            $STH->bindParam(2, $questionmoduleactive);
//                            $STH->bindParam(3, $_POST['radioinput'][$i]);
//                            if ($_POST['radio_req'][$i] == 1) {
//                                $questionmodulerequired = 1;
//                            } else {
//                                $questionmodulerequired = 0;
//                            }
//                            $STH->bindParam(4, $questionmodulerequired);
//                            $STH->execute();
//                        }
//                    }
//
////                    //delete old question module
////                    $STH = $DBH->prepare("
////                        DELETE FROM " . $dbPrefix . "questionmodule 
////                            WHERE question_id = ? 
////                            AND questionmodule_type = ?");
////                    $STH->bindParam(1, $_GET['questionid']);
////                    $questionmoduletype = 2;
////                    $STH->bindParam(2, $questionmoduletype);
////                    $STH->execute();
//
//                    if (isset($_POST['checkcheck'])) {
//
//                        //insert new question module
//                        $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "questionmodule
//                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
//                                VALUES (?, ?, ?, ?)");
//                        $STH->bindParam(1, $_GET['questionid']);
//                        $STH->bindParam(2, $_POST['descriptioncheck']);
//                        $type = 2;
//                        $STH->bindParam(3, $type);
//                        //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
//                        $sorting = 1;
//                        $STH->bindParam(4, $sorting);
//                        $STH->execute();
//
//
//                        $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "questionmodule_value
//                                (questionmodule_id, questionvalue_active, questionvalue_value, questionvalue_required)
//                                VALUES (?, ?, ?, ?)");
//                        $questionmoduleid = $DBH->lastInsertId();
//                        for ($i = 0; $i < count($_POST['checkinput']); $i++) {
//
//                            $STH->bindParam(1, $questionmoduleid);
//                            if ($_POST['check'][$i] == 1) {
//                                $questionmoduleactive = 1;
//                            } else {
//                                $questionmoduleactive = 0;
//                            }
//                            $STH->bindParam(2, $questionmoduleactive);
//                            $STH->bindParam(3, $_POST['checkinput'][$i]);
//                            if ($_POST['check_req'][$i] == 1) {
//                                $questionmodulerequired = 1;
//                            } else {
//                                $questionmodulerequired = 0;
//                            }
//                            $STH->bindParam(4, $questionmodulerequired);
//                            $STH->execute();
//                        }
//                    }
//
////                    //delete old question module
////                    $STH = $DBH->prepare("
////                        DELETE FROM " . $dbPrefix . "questionmodule 
////                            WHERE question_id = ? 
////                            AND questionmodule_type = ?");
////                    $STH->bindParam(1, $_GET['questionid']);
////                    $questionmoduletype = 3;
////                    $STH->bindParam(2, $questionmoduletype);
////                    $STH->execute();
//
//                    if (isset($_POST['checksingle'])) {
//
//                        //insert new question module
//                        $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "questionmodule
//                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
//                                VALUES (?, ?, ?, ?)");
//                        $STH->bindParam(1, $_GET['questionid']);
//                        $STH->bindParam(2, $_POST['descriptionsingle']);
//                        $type = 3;
//                        $STH->bindParam(3, $type);
//                        //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
//                        $sorting = 1;
//                        $STH->bindParam(4, $sorting);
//                        $STH->execute();
//
//
//                        $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "questionmodule_value
//                                (questionmodule_id, questionvalue_active, questionvalue_value, questionvalue_required)
//                                VALUES (?, ?, ?, ?)");
//                        $questionmoduleid = $DBH->lastInsertId();
//                        for ($i = 0; $i < count($_POST['singleinput']); $i++) {
//
//                            $STH->bindParam(1, $questionmoduleid);
//                            if ($_POST['single'][$i] == 1) {
//                                $questionmoduleactive = 1;
//                            } else {
//                                $questionmoduleactive = 0;
//                            }
//                            $STH->bindParam(2, $questionmoduleactive);
//                            $STH->bindParam(3, $_POST['singleinput'][$i]);
//                            if ($_POST['single_req'][$i] == 1) {
//                                $questionmodulerequired = 1;
//                            } else {
//                                $questionmodulerequired = 0;
//                            }
//                            $STH->bindParam(4, $questionmodulerequired);
//                            $STH->execute();
//                        }
//                    }
//
////                    //delete old question module
////                    $STH = $DBH->prepare("
////                        DELETE FROM " . $dbPrefix . "questionmodule 
////                            WHERE question_id = ? 
////                            AND questionmodule_type = ?");
////                    $STH->bindParam(1, $_GET['questionid']);
////                    $questionmoduletype = 4;
////                    $STH->bindParam(2, $questionmoduletype);
////                    $STH->execute();
//
//                    if (isset($_POST['checkmulti'])) {
//
//                        //insert new question module
//                        $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "questionmodule
//                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
//                                VALUES (?, ?, ?, ?)");
//                        $STH->bindParam(1, $_GET['questionid']);
//                        $STH->bindParam(2, $_POST['descriptionmulti']);
//                        $type = 4;
//                        $STH->bindParam(3, $type);
//                        //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
//                        $sorting = 1;
//                        $STH->bindParam(4, $sorting);
//                        $STH->execute();
//
//                        $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "questionmodule_value
//                                (questionmodule_id, questionvalue_required)
//                                VALUES (?,?)");
//                        $questionmoduleid = $DBH->lastInsertId();
//                        $STH->bindParam(1, $questionmoduleid);
//                        if ($_POST['multi_req'] == 1) {
//                            $questionmodulerequired = 1;
//                        } else {
//                            $questionmodulerequired = 0;
//                        }
//                        $STH->bindParam(2, $questionmodulerequired);
//                        $STH->execute();
//                    }
//
//                    //delete old question module
//                    //before deleting, we need the answer-value of the upload
//                    $STH = $DBH->prepare("
//                        SELECT answer_value  
//                        FROM " . $dbPrefix . "answer 
//                            WHERE questionvalue_id = (
//                                SELECT qmv.questionvalue_id
//                                FROM " . $dbPrefix . "questionmodule_value AS qmv
//                                INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
//                                WHERE qm.question_id = ?)");
//                    $STH->bindParam(1, $_GET['questionid']);
//                    $STH->execute();
//
//                    //set fetch mode
//                    $STH->setFetchMode(PDO::FETCH_OBJ);
//
//                    //fetch data
//                    $i = 0;
//                    $uploadtemp = null;
//                    while ($row = $STH->fetch()) {
//                        $uploadtemp[0] = $row;
//                    }
//                    //now the data is saved in $uploadtemp array and all can be deleted
////
////                    $STH = $DBH->prepare("
////                        DELETE FROM " . $dbPrefix . "questionmodule 
////                            WHERE question_id = ? 
////                            AND questionmodule_type = ?");
////                    $STH->bindParam(1, $_GET['questionid']);
////                    $questionmoduletype = 5;
////                    $STH->bindParam(2, $questionmoduletype);
////                    $STH->execute();
//
//                    if (isset($_POST['checkupload'])) {
//
//                        //insert new question module
//                        $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "questionmodule
//                                (question_id, questionmodule_description, questionmodule_type, questionmodule_sort)
//                                VALUES (?, ?, ?, ?)");
//                        $STH->bindParam(1, $_GET['questionid']);
//                        $STH->bindParam(2, $_POST['descriptionupload']);
//                        $type = 5;
//                        $STH->bindParam(3, $type);
//                        //BITTE NOCH BEARBEITEN UND SORTING IMPLEMENTIEREN
//                        $sorting = 1;
//                        $STH->bindParam(4, $sorting);
//                        $STH->execute();
//
//                        $STH = $DBH->prepare("
//                                INSERT INTO " . $dbPrefix . "questionmodule_value
//                                (questionmodule_id, questionvalue_required)
//                                VALUES (?, ?)");
//                        $questionmoduleid = $DBH->lastInsertId();
//                        $STH->bindParam(1, $questionmoduleid);
//                        if ($_POST['upload_req'] == 1) {
//                            $questionmodulerequired = 1;
//                        } else {
//                            $questionmodulerequired = 0;
//                        }
//                        $STH->bindParam(2, $questionmodulerequired);
//                        $STH->execute();
//                    }
//                    //commit statements
//                    $DBH->commit();
//
//                    //the data in the question-values has changed. that deletes also the answer-values, but not the files on the filesystem
//                    //the data-string of the answer_value is saved in $uploadtmp
//
//                    $filetemp = null;
//                    for ($i = 0; $i < count($uploadtemp); $i++) {
//                        $filetemp = getFilelist($uploadtemp[$i]->answer_value);
//                        for ($j = 0; $j < count($filetemp['dsname']); $j++) {
//                            unlink($rootDir . 'upload/' . $filetemp['dsname'][$j]);
//                        }
//                    }
//
//
//                    //close db connection
//                    $DBH = null;
//                } catch (PDOException $e) {
//                    $DBH->rollBack();
//                    echo $e->getMessage();
//                }
//            }
            //redirect to overviewpage
            if ($sslonly == TRUE) {
                $httpprefix = 'https://';
            } else {
                $httpprefix = 'http://';
            }
            $nextpage = $httpprefix . $website . 'admin.php?mode=question&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'] . '&subsectionid=' . $_GET['subsectionid'];
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
            $STH = $DBH->prepare("SELECT question_id, question_sort 
                    FROM " . $dbPrefix . "subsection_question
                    WHERE subsection_id = ?
                    AND question_sort = (                 
                        SELECT question_sort 
                        FROM " . $dbPrefix . "subsection_question
                        WHERE question_id = ?
                        AND subsection_id = ?)-1");
            $STH->bindParam(1, $_GET['subsectionid']);
            $STH->bindParam(2, $_GET['questionid']);
            $STH->bindParam(3, $_GET['subsectionid']);
            $STH->execute();
            //set fetch mode
            $STH->setFetchMode(PDO::FETCH_OBJ);

            //fetch data
            $row = $STH->fetch();

            $questionSort = $row->question_sort;
            $questionID = $row->question_id;

            //check if module is on place 1
            if (isset($row->question_id)) {
                //sort the modules
                try {
                    $STH = $DBH->prepare("UPDATE " . $dbPrefix . "subsection_question
                        SET question_sort = ? WHERE question_id = ? AND subsection_id = ?");
                    $newsort = $questionSort + 1;
                    $STH->bindParam(1, $newsort);
                    $STH->bindParam(2, $questionID);
                    $STH->bindParam(3, $_GET['subsectionid']);
                    $STH->execute();
                    $STH->bindParam(1, $questionSort);
                    $STH->bindParam(2, $_GET['questionid']);
                    $STH->bindParam(3, $_GET['subsectionid']);
                    $STH->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
            }
        }
        if ($_GET['dir'] == 'down') {
            //get id from -1 module
            $STH = $DBH->prepare("SELECT question_id, question_sort 
                    FROM " . $dbPrefix . "subsection_question
                    WHERE subsection_id = ?
                    AND question_sort = (
                        SELECT question_sort 
                        FROM " . $dbPrefix . "subsection_question
                        WHERE question_id = ?
                        AND subsection_id = ?)+1");
            $STH->bindParam(1, $_GET['subsectionid']);
            $STH->bindParam(2, $_GET['questionid']);
            $STH->bindParam(3, $_GET['subsectionid']);
            $STH->execute();
            //set fetch mode
            $STH->setFetchMode(PDO::FETCH_OBJ);

            //fetch data
            $row = $STH->fetch();

            $questionSort = $row->question_sort;
            $questionID = $row->question_id;

            //check if module is on last place
            if (isset($row->question_id)) {
                //sort the modules
                try {
                    $STH = $DBH->prepare("UPDATE " . $dbPrefix . "subsection_question
                        SET question_sort = ? WHERE question_id = ? AND subsection_id = ?");
                    $newsort = $questionSort - 1;
                    $STH->bindParam(1, $newsort);
                    $STH->bindParam(2, $questionID);
                    $STH->bindParam(3, $_GET['subsectionid']);
                    $STH->execute();
                    $STH->bindParam(1, $questionSort);
                    $STH->bindParam(2, $_GET['questionid']);
                    $STH->bindParam(3, $_GET['subsectionid']);
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
    $nextpage = $httpprefix . $website . 'admin.php?mode=question&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'] . '&subsectionid=' . $_GET['subsectionid'];
    header("Location: " . $nextpage);
    exit;
} elseif ($_GET['action'] == 'deletequestion') {
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
            SELECT question_id, question_name
            FROM " . $dbPrefix . "question
            WHERE question_id = ?");

        $STH->bindParam(1, $_GET['questionid']);
        //execute statement
        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);
        //fetch data
        $questiondatadelete = $STH->fetch();
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
        $nextpage = $httpprefix . $website . 'admin.php?mode=question&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'] . '&subsectionid=' . $_GET['subsectionid'];
        header("Location: " . $nextpage);
        exit;
    }
    if (isset($_POST['delete'])) {
        //two ways are possible.
        //1: question is used in other subsection -> delete entry in subsection_question
        //2: question is not used in other module -> delete entry in question
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
                    SELECT subsection_id 
                    FROM " . $dbPrefix . "subsection_question
                    WHERE question_id = ?
                    AND subsection_id <> ?");

            $STH->bindParam(1, $_GET['questionid']);
            $STH->bindParam(2, $_GET['subsectionid']);
            //execute statement
            $STH->execute();
            //set fetch mode
            $STH->setFetchMode(PDO::FETCH_OBJ);
            //fetch data
            $i = 0;
            while ($row = $STH->fetch()) {
                $questions[$i] = $row;
                $i++;
            }
            if (isset($questions[0])) {
                $morequestions = TRUE;
            }
            //close db connection
            $DBH = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }


        if ($morequestions == TRUE) {


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
                $STH = $DBH->prepare("DELETE FROM " . $dbPrefix . "subsection_question WHERE question_id = ? AND subsection_id = ?");

                $STH->bindParam(1, $_GET['questionid']);
                $STH->bindParam(2, $_GET['subsectionid']);
                //execute statement
                $STH->execute();

                //sort modules again
                $STH = $DBH->prepare("SELECT question_id FROM " . $dbPrefix . "subsection_question WHERE subsection_id = ? ORDER BY question_sort");
                $STH->bindParam(1, $_GET['subsectionid']);
                $STH->execute();
                //set fetch mode
                $STH->setFetchMode(PDO::FETCH_OBJ);
                //fetch data
                $i = 0;
                while ($row = $STH->fetch()) {
                    $sorter[$i] = $row;
                    $i++;
                }
                $STH = $DBH->prepare("UPDATE " . $dbPrefix . "subsection_question SET question_sort = ? WHERE question_id = ? AND subsection_id = ?");
                for ($i = 0; $i < count($sorter); $i++) {
                    $newsort = $i + 1;
                    $STH->bindParam(1, $newsort);
                    $STH->bindParam(2, $sorter[$i]->question_id);
                    $STH->bindParam(3, $_GET['subsectionid']);
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

                //before deleting, we need the answer-value of the upload
                $STH = $DBH->prepare("
                        SELECT answer_value  
                        FROM " . $dbPrefix . "answer 
                            WHERE questionvalue_id = (
                                SELECT qmv.questionvalue_id
                                FROM " . $dbPrefix . "questionmodule_value AS qmv
                                INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
                                WHERE qm.question_id = ?)");
                $STH->bindParam(1, $_GET['questionid']);
                $STH->execute();

                //set fetch mode
                $STH->setFetchMode(PDO::FETCH_OBJ);

                //fetch data
                $i = 0;
                $uploadtemp = null;
                while ($row = $STH->fetch()) {
                    $uploadtemp[0] = $row;
                }
                //now the data is saved in $uploadtemp array and all can be deleted
                $delete_flag = 1;

                $STH = $DBH->prepare("UPDATE " . $dbPrefix . "question SET question_delete = ? WHERE question_id = ?");
                $STH->bindParam(1, $delete_flag);
                $STH->bindParam(2, $_GET['questionid']);
                $STH->execute();

                //sort modules again
                $STH = $DBH->prepare("SELECT question_id FROM " . $dbPrefix . "subsection_question WHERE subsection_id = ? ORDER BY question_sort");
                $STH->bindParam(1, $_GET['subsectionid']);
                $STH->execute();
                //set fetch mode
                $STH->setFetchMode(PDO::FETCH_OBJ);
                //fetch data
                $i = 0;
                while ($row = $STH->fetch()) {
                    $sorter[$i] = $row;
                    $i++;
                }
                $STH = $DBH->prepare("UPDATE " . $dbPrefix . "subsection_question SET question_sort = ? WHERE question_id = ? AND subsection_id = ?");
                for ($i = 0; $i < count($sorter); $i++) {
                    $newsort = $i + 1;
                    $STH->bindParam(1, $newsort);
                    $STH->bindParam(2, $sorter[$i]->question_id);
                    $STH->bindParam(3, $_GET['subsectionid']);
                    $STH->execute();
                }

                //sort modules again
                $STH = $DBH->prepare("SELECT question_id, question_group FROM " . $dbPrefix . "subsection_question WHERE subsection_id = ?");
                $STH->bindParam(1, $_GET['subsectionid']);
                $STH->execute();
                //set fetch mode
                $STH->setFetchMode(PDO::FETCH_OBJ);
                //fetch data
                $i = 0;
                while ($row = $STH->fetch()) {
                    $groupquestionsort[$i] = $row;
                    $i++;
                }
                $STH = $DBH->prepare("UPDATE " . $dbPrefix . "subsection_question SET question_group = ? WHERE question_id = ? AND subsection_id = ?");
                for ($i = 0; $i <= count($groupquestionsort); $i++) {
                    if ($groupquestionsort[$i]->question_group != $groupquestionsort[$i + 1]->question_group
                            && ($groupquestionsort[$i]->question_group + 1) < $groupquestionsort[$i + 1]->question_group) {
                        $newsort = $groupquestionsort[$i + 1]->question_group - 1;
                        $STH->bindParam(1, $newsort);
                        $STH->bindParam(2, $groupquestionsort[$i + 1]->question_id);
                        $STH->bindParam(3, $_GET['subsectionid']);
                        $STH->execute();
                    }
                }

                //close db connection
                $DBH->commit();
                $DBH = null;
            } catch (PDOException $e) {
                $DBH->rollBack();
                echo $e->getMessage();
            }

            //the data in the question-values has been deleted. That deletes also the answer-values, but not the files on the filesystem
            //the data-string of the answer_value is saved in $uploadtmp
            //now we need to delte the files on the filesystem

            $filetemp = null;
            for ($i = 0; $i < count($uploadtemp); $i++) {
                $filetemp = getFilelist($uploadtemp[$i]->answer_value);
                for ($j = 0; $j < count($filetemp['dsname']); $j++) {
                    unlink($rootDir . 'upload/' . $filetemp['dsname'][$j]);
                }
            }
        }
        if ($sslonly == TRUE) {
            $httpprefix = 'https://';
        } else {
            $httpprefix = 'http://';
        }
        $nextpage = $httpprefix . $website . 'admin.php?mode=question&action=overview&moduleid=' . $_GET['moduleid'] . '&sectionid=' . $_GET['sectionid'] . '&subsectionid=' . $_GET['subsectionid'];
        header("Location: " . $nextpage);
        exit;
    }
} else if ($_GET['action'] == 'versionquestion') {
    try {
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
            SELECT question_questiongroup
            FROM " . $dbPrefix . "question
            WHERE question_id = ?");
        //execute statement
        $STH->bindParam(1, $_GET['questionid']);

        $STH->execute();
        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);

        $questiongroup = 0;

//        //make number for module_sort
//        $STH = $DBH->prepare("
//            SELECT ss.subsection_id, ss.subsection_name, q.question_id, q.question_version, q.question_delete, q.question_name, q.question_description, sq.question_active, sq.question_group 
//            FROM " . $dbPrefix . "question AS q
//            INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.question_id = q.question_id
//            INNER JOIN " . $dbPrefix . "subsection AS ss ON ss.subsection_id = sq.subsection_id
//            WHERE q.questiongroup = ?
//            ORDER BY sq.question_sort");
//        //execute statement
//        $STH->bindParam(1, $questiongroup);
//
//        $STH->execute();
//        //set fetch mode
//        $STH->setFetchMode(PDO::FETCH_OBJ);
//        //fetch data
//        $i = 0;
//        while ($row = $STH->fetch()) {
//            $questiongroupversion[$i] = $row;
//            $i++;
//        }
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>
