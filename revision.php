<?php

require_once 'session.php';
require_once 'localsettings.php';

// this is the first call of the functions. It is not nice, but i need the questiondata for the db entry.
// check if module, section or subsection and then get Data
// module = 0
// section = 1
// subsection = 2
switch ($_GET['type']) {
    case 0:
        //its a module

        $overalldata = getModules(true, $_GET['moduleid']);
        $data = getRevisionData(0, $_GET['moduleid']);

        $name = $overalldata[0]->module_name;

        break;
    case 1:
        //its a section

        $overalldata = getSections(true, $_GET['moduleid'], $_GET['sectionid']);
        $data = getRevisionData(1, $_GET['sectionid']);

        $name = $overalldata[0]->section_name;

        break;
    case 2:
        //its a subsection

        $overalldata = getSubsections(true, $_GET['sectionid'], $_GET['subsectionid']);
        $data = getRevisionData(2, $_GET['subsectionid']);

        $name = $overalldata[0]->subsection_name;

        break;
}


// the user pushed the delete button of a file
if (in_array('Delete', $_POST['next'])) {

    $keytmp = null;
    foreach ($_POST['next'] as $key => $value) {
        $keytmp = $key;
    }
    $keytmp = explode("-", $keytmp);

    //getting filelist from db
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
        //get questionvalue_id
        $STH = $DBH->prepare("
                SELECT a.answer_value, a.questionvalue_id, a.user_id
                FROM " . $dbPrefix . "answer AS a
                INNER JOIN " . $dbPrefix . "questionmodule_value AS qmv ON qmv.questionvalue_id = a.questionvalue_id
                INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
                WHERE qm.question_id = ?
                AND a.user_id = ?
                AND qm.questionmodule_type = '5'");
        $STH->bindParam(1, $keytmp[0]);
        $STH->bindParam(2, $globalid);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $qmv = $STH->fetch();

        $filelist = getFilelist($qmv->answer_value);

        $file2delete = $filelist['dsname'][$keytmp[1]];
        unset($filelist['dsname'][$keytmp[1]]);
        $filelist = array_values($filelist['dsname']);

        $sqlstring = null;
        for ($i = 0; $i < count($filelist); $i++) {
            $sqlstring .= $filelist[$i] . '|';
        }

        $STH = $DBH->prepare("UPDATE " . $dbPrefix . "answer 
                SET answer_value = ? WHERE questionvalue_id = ? AND user_id = ?");
        //bind variables
        $STH->bindParam(1, $sqlstring);
        $STH->bindParam(2, $qmv->questionvalue_id);
        $STH->bindParam(3, $qmv->user_id);
        $STH->execute();

        //check if answer_value null -> delete
        $STH = $DBH->prepare("SELECT answer_value
            FROM " . $dbPrefix . "answer 
             WHERE questionvalue_id = ? AND user_id = ?");
        $STH->bindParam(1, $qmv->questionvalue_id);
        $STH->bindParam(2, $qmv->user_id);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $answervalue = $STH->fetch();

        if ($answervalue->answer_value == null) {
            $STH = $DBH->prepare("
                                DELETE FROM " . $dbPrefix . "answer 
                            WHERE questionvalue_id = ? AND user_id = ?");
            $STH->bindParam(1, $qmv->questionvalue_id);
            $STH->bindParam(2, $qmv->user_id);
            $STH->execute();
        }
        $DBH->commit();
        $DBH = null;
    } catch (PDOException $e) {
        $DBH->rollBack();
        echo $e->getMessage();
    }

    unlink($rootDir . 'upload/' . $file2delete);
}

// the user pushed the upload button
if ($_POST['next'] == "Upload") {

    $maxFilesize = (int) (@ini_get('post_max_size')) * 1024 * 1024;
    foreach ($_FILES as $key => &$value) {
        if ($value['name'][0] != '') {
            //check filename and size
            $answervalue = null;
            $uploaderror[] = null;
            for ($j = 0; $j < count($value['name']); $j++) {
                $extension = getExtension($value['name'][$j]);
                if (!in_array($extension, $allowedExtenstions)) {
                    $uploaderror[$key] = "This type of files is not allwoed: *." . $extension;
                    //ignore file if forbidden extension
                    continue;
                }

                if ($value['size'][$j] > $maxFilesize) {
                    $uploaderror[$key] = "The file size is too big.";
                    continue;
                }

                // unique filename
                $save_as_name = uniqid() . '_' . $value['name'][$j];
                // save file on server
                move_uploaded_file($value['tmp_name'][$j], $rootDir . 'upload/' . $save_as_name);
                $answervalue .= $save_as_name . '|';
            }
            if ($answervalue != null) {
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
                    //get questionvalue_id
                    $STH = $DBH->prepare("
                                    SELECT questionvalue_id 
                                    FROM " . $dbPrefix . "questionmodule_value AS qmv
                                    INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
                                    WHERE qm.question_id = ?
                                    AND questionmodule_type = '5'");
                    $STH->bindParam(1, $key);
                    $STH->execute();
                    $STH->setFetchMode(PDO::FETCH_OBJ);
                    $qmv = $STH->fetch();
                    //get old uploads
                    $STH = $DBH->prepare("
                                    SELECT answer_value 
                                    FROM " . $dbPrefix . "answer
                                    WHERE questionvalue_id = ?
                                    AND user_id = ?");
                    $STH->bindParam(1, $qmv->questionvalue_id);
                    $STH->bindParam(2, $globalid);
                    $STH->execute();
                    $STH->setFetchMode(PDO::FETCH_OBJ);
                    $oldanswer = $STH->fetch();

                    $answervalue .= $oldanswer->answer_value;

                    //delete answer
                    $STH = $DBH->prepare("
                                DELETE FROM " . $dbPrefix . "answer 
                            WHERE questionvalue_id = ?
                            AND user_id = ?");
                    $STH->bindParam(1, $qmv->questionvalue_id);
                    $STH->bindParam(2, $globalid);
                    $STH->execute();

                    //create new answer
                    $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "answer
                                (user_id, questionvalue_id, answer_value, answer_checked)
                                VALUES (?, ?, ?, null)");
                    $STH->bindParam(1, $globalid);
                    $STH->bindParam(2, $qmv->questionvalue_id);
                    $STH->bindParam(3, $answervalue);
                    $STH->execute();

                    $DBH->commit();
                    $DBH = null;
                } catch (PDOException $e) {
                    $DBH->rollBack();
                    echo $e->getMessage();
                }
            }
        }
    }
}

//user clicks on next and the stuff will be safed in the Database
if ($_POST['next'] == "Save & Next" || $_POST['next'] == "Upload" || in_array('Delete', $_POST['next'])) {
    for ($i = 0; $i < count($data); $i++) {
        for ($j = 0; $j < count($data[$i]->questionmodule); $j++) {
            for ($k = 0; $k < count($data[$i]->questionmodule[$j]->questionvalue); $k++) {

                //first of all the radiobuttons
                if (isset($_POST['radio'][$data[$i]->question_id])) {
                    //delete previous answer and add new one
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
                        //delete
                        $STH = $DBH->prepare("
                                DELETE a FROM " . $dbPrefix . "answer AS a
                                INNER JOIN " . $dbPrefix . "questionmodule_value AS qmv ON qmv.questionvalue_id = a.questionvalue_id
                                INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
                                WHERE a.questionvalue_id = ?
                                AND a.user_id = ?
                                AND qm.questionmodule_type = 1");
                        $STH->bindParam(1, $data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id);
                        $STH->bindParam(2, $globalid);
                        $STH->execute();

                        //add
                        if ($_POST['radio'][$data[$i]->question_id] == $data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id) {
                            //create new answer
                            $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "answer
                                (user_id, questionvalue_id, answer_checked)
                                VALUES (?, ?, ?)");
                            $STH->bindParam(1, $globalid);
                            $STH->bindParam(2, $data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id);
                            $answercheck = 1;
                            $STH->bindParam(3, $answercheck);
                            $STH->execute();
                        }
                        $DBH->commit();
                        $DBH = null;
                    } catch (PDOException $e) {
                        $DBH->rollBack();
                        echo $e->getMessage();
                    }
                }

                //checkboxes
                if (isset($_POST['checkboxexists'])) {
                    //delete previous answer and add new one
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
                        //delete
                        $STH = $DBH->prepare("
                                DELETE a FROM " . $dbPrefix . "answer AS a
                                INNER JOIN " . $dbPrefix . "questionmodule_value AS qmv ON qmv.questionvalue_id = a.questionvalue_id
                                INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
                                WHERE a.questionvalue_id = ?
                                AND a.user_id = ?
                                AND qm.questionmodule_type = 2");
                        $STH->bindParam(1, $data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id);
                        $STH->bindParam(2, $globalid);
                        $STH->execute();
                        //add
                        if ($_POST['checkbox'][$data[$i]->question_id][$data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id] == '1') {
                            //create new answer
                            $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "answer
                                (user_id, questionvalue_id, answer_checked)
                                VALUES (?, ?, ?)");
                            $STH->bindParam(1, $globalid);
                            $STH->bindParam(2, $data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id);
                            $answercheck = 1;
                            $STH->bindParam(3, $answercheck);
                            $STH->execute();
                        }
                        $DBH->commit();
                        $DBH = null;
                    } catch (PDOException $e) {
                        $DBH->rollBack();
                        echo $e->getMessage();
                    }
                }

                //single line text
                if (isset($_POST['single'][$data[$i]->question_id])) {
                    //delete previous answer and add new one
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
                        //delete
                        $STH = $DBH->prepare("
                                DELETE a FROM " . $dbPrefix . "answer AS a
                                INNER JOIN " . $dbPrefix . "questionmodule_value AS qmv ON qmv.questionvalue_id = a.questionvalue_id
                                INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
                                WHERE a.questionvalue_id = ?
                                AND a.user_id = ?
                                AND qm.questionmodule_type = 3");
                        $STH->bindParam(1, $data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id);
                        $STH->bindParam(2, $globalid);
                        $STH->execute();
                        //add
                        if (isset($_POST['single'][$data[$i]->question_id][$data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id])) {
                            //create new answer
                            $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "answer
                                (user_id, questionvalue_id, answer_value, answer_checked)
                                VALUES (?, ?, ?, null)");
                            $STH->bindParam(1, $globalid);
                            $STH->bindParam(2, $data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id);
                            $STH->bindParam(3, $_POST['single'][$data[$i]->question_id][$data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id]);
                            $STH->execute();
                        }
                        $DBH->commit();
                        $DBH = null;
                    } catch (PDOException $e) {
                        $DBH->rollBack();
                        echo $e->getMessage();
                    }
                }

                //multi line text
                if (isset($_POST['multi'][$data[$i]->question_id])) {
                    //delete previous answer and add new one
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
                        //delete
                        $STH = $DBH->prepare("
                                DELETE a FROM " . $dbPrefix . "answer AS a
                                INNER JOIN " . $dbPrefix . "questionmodule_value AS qmv ON qmv.questionvalue_id = a.questionvalue_id
                                INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
                                WHERE a.questionvalue_id = ?
                                AND a.user_id = ?
                                AND qm.questionmodule_type = 4");
                        $STH->bindParam(1, $data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id);
                        $STH->bindParam(2, $globalid);
                        $STH->execute();
                        //add
                        if (isset($_POST['multi'][$data[$i]->question_id][$data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id])) {
                            //create new answer
                            $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "answer
                                (user_id, questionvalue_id, answer_value, answer_checked)
                                VALUES (?, ?, ?, null)");
                            $STH->bindParam(1, $globalid);
                            $STH->bindParam(2, $data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id);
                            $STH->bindParam(3, $_POST['multi'][$data[$i]->question_id][$data[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_id]);
                            $STH->execute();
                        }
                        $DBH->commit();
                        $DBH = null;
                    } catch (PDOException $e) {
                        $DBH->rollBack();
                        echo $e->getMessage();
                    }
                }
            }
        }
    }
    
    // check if every question is now correct answered
    $redirect = null;
    switch ($_GET['type']) {
    case 0:
        //its a module

        $redirect = checkforRevision(0, $_GET['moduleid']);

        break;
    case 1:
        //its a section

        $redirect = checkforRevision(1, $_GET['sectionid']);

        break;
    case 2:
        //its a subsection

        $redirect = checkforRevision(2, $_GET['subsectionid']);

        break;
}
    
    
    
    if ($sslonly == TRUE) {
        $httpprefix = 'https://';
    } else {
        $httpprefix = 'http://';
    }
    if ($redirect == 0) {
        $nextpage = $httpprefix . $website . 'revision.php?type=&moduleid=' . $_GET['moduleid'].'&sectionid=' . $_GET['sectionid'].'&subsectionid=' . $_GET['subsectionid'];
    } else {
        $nextpage = $httpprefix . $website . 'verification.php?mode=umodule&umoduleid=' . $_GET['moduleid'];
    }
    header("Location: " . $nextpage);
    exit;
}


// check if module, section or subsection and then get Data
// module = 0
// section = 1
// subsection = 2
switch ($_GET['type']) {
    case 0:
        //its a module

        $overalldata = getModules(true, $_GET['moduleid']);
        $data = getRevisionData(0, $_GET['moduleid']);

        $name = $overalldata[0]->module_name;

        break;
    case 1:
        //its a section

        $overalldata = getSections(true, $_GET['moduleid'], $_GET['sectionid']);
        $data = getRevisionData(1, $_GET['sectionid']);

        $name = $overalldata[0]->section_name;

        break;
    case 2:
        //its a subsection

        $overalldata = getSubsections(true, $_GET['sectionid'], $_GET['subsectionid']);
        $data = getRevisionData(2, $_GET['subsectionid']);

        $name = $overalldata[0]->subsection_name;

        break;
}






//check if usergroup can see this page
if ($globalusergroup == 3 || $globalusergroup == 2 || $globalusergroup == 1) {
    include $templateDir . 'revision.tpl.php';
}
?>
