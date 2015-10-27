<?php

//getting questions and questionvalues
try {
    $DBH = new PDO(
                    "mysql:host=$dbHost;dbname=$dbName",
                    $dbUser,
                    $dbPasswd,
                    array(
                        PDO::ATTR_ERRMODE => $errormode,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));

    $STH = $DBH->prepare("
            SELECT  exclass_sort
            FROM " . $dbPrefix . "exclass
            WHERE exclass_id = ?");
    $STH->bindParam(1, $globalexclass);
    $STH->execute();
    $STH->setFetchMode(PDO::FETCH_OBJ);
    $num = $STH->fetch();

    //get questions
    //make numbers for execution class
    $exnums = null;
    for ($i = 1; $i < $num->exclass_sort; $i++) {
        $exnums .= $i;
        $exnums .= ", ";
    }
    $exnums .= $num->exclass_sort;

    $STH = $DBH->prepare("
            SELECT  q.question_id, q.question_name, q.question_description, sq.question_sort
            FROM " . $dbPrefix . "question AS q
            INNER JOIN " . $dbPrefix . "subsection_question AS sq ON q.question_id = sq.question_id 
            WHERE sq.subsection_id = ?
            AND sq.question_group = ?
            AND sq.question_active = 1
            AND sq.question_exclass IN (" . $exnums . ")
            ORDER BY sq.question_sort");
    $STH->bindParam(1, $_GET['usubsectionid']);
    $STH->bindParam(2, $_GET['uquestiongroup']);
    $STH->execute();
    $STH->setFetchMode(PDO::FETCH_OBJ);

    //get questionmodulevalues
    $STH2 = $DBH->prepare("
            SELECT  questionmodule_id, questionmodule_description, questionmodule_type
            FROM " . $dbPrefix . "questionmodule
            WHERE question_id = ?
            ORDER BY questionmodule_sort");

    //get questionmodule_values
    $STH3 = $DBH->prepare("
            SELECT  questionvalue_id, questionvalue_value, questionvalue_active
            FROM " . $dbPrefix . "questionmodule_value
            WHERE questionmodule_id = ?");

    //get possible answers in the questionmodule
    $STH4 = $DBH->prepare("
            SELECT  answer_value, answer_checked, answer_edited
            FROM " . $dbPrefix . "answer
            WHERE user_id = ?
            AND questionvalue_id = ?");

    $i = 0;
    while ($row = $STH->fetch()) {
        $uquestions[$i] = $row;
        $STH2->bindParam(1, $uquestions[$i]->question_id);
        $STH2->execute();
        $STH2->setFetchMode(PDO::FETCH_OBJ);
        $j = 0;
        while ($row2 = $STH2->fetch()) {
            $uquestionmodules[$i][$j] = $row2;
            $STH3->bindParam(1, $uquestionmodules[$i][$j]->questionmodule_id);
            $STH3->execute();
            $STH3->setFetchMode(PDO::FETCH_OBJ);
            $k = 0;
            $isanswered[$i][$j] = false;
            while ($row3 = $STH3->fetch()) {
                $uquestionmodulevalues[$i][$j][$k] = $row3;

                $STH4->bindParam(1, $globalid);
                $STH4->bindParam(2, $uquestionmodulevalues[$i][$j][$k]->questionvalue_id);
                $STH4->execute();
                $STH4->setFetchMode(PDO::FETCH_OBJ);

                $uanswermodulevalues[$i][$j][$k] = $STH4->fetch();

                if (isset($uanswermodulevalues[$i][$j][$k]->answer_edited)) {
                    $isanswered[$i][$j] = true;
                }

                $k++;
            }
            $j++;
        }
        $i++;
    }

    // get questiongroups for group-bar
    $STH = $DBH->prepare("
            SELECT DISTINCT  question_group
            FROM " . $dbPrefix . "subsection_question
            WHERE subsection_id = ?
            AND question_active = 1
            ORDER BY question_group");

    $STH->bindParam(1, $_GET['usubsectionid']);
    $STH->execute();
    $STH->setFetchMode(PDO::FETCH_OBJ);
    $i = 0;
    while ($row = $STH->fetch()) {
        $ugroups[$i] = $row;
        $i++;
    }

    $DBH = null;
} catch (PDOException $e) {
    echo $e->getMessage();
}

//the user has pushed the next button

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

    //NOT NICE PLEASE REMOVE IT WHEN YOU RETHINK!
    try {
        $DBH = new PDO(
                        "mysql:host=$dbHost;dbname=$dbName",
                        $dbUser,
                        $dbPasswd,
                        array(
                            PDO::ATTR_ERRMODE => $errormode,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));

        $STH = $DBH->prepare("
            SELECT  exclass_sort
            FROM " . $dbPrefix . "exclass
            WHERE exclass_id = ?");
        $STH->bindParam(1, $globalexclass);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $num = $STH->fetch();

        //get questions
        //make numbers for execution class
        $exnums = null;
        for ($i = 1; $i < $num->exclass_sort; $i++) {
            $exnums .= $i;
            $exnums .= ", ";
        }
        $exnums .= $num->exclass_sort;

        $STH = $DBH->prepare("
            SELECT  q.question_id, q.question_name, q.question_description, sq.question_sort
            FROM " . $dbPrefix . "question AS q
            INNER JOIN " . $dbPrefix . "subsection_question AS sq ON q.question_id = sq.question_id 
            WHERE sq.subsection_id = ?
            AND sq.question_group = ?
            AND sq.question_active = 1
            AND sq.question_exclass IN (" . $exnums . ")
            ORDER BY sq.question_sort");
        $STH->bindParam(1, $_GET['usubsectionid']);
        $STH->bindParam(2, $_GET['uquestiongroup']);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);

        //get questionmodulevalues
        $STH2 = $DBH->prepare("
            SELECT  questionmodule_id, questionmodule_description, questionmodule_type
            FROM " . $dbPrefix . "questionmodule
            WHERE question_id = ?
            ORDER BY questionmodule_sort");

        //get questionmodule_values
        $STH3 = $DBH->prepare("
            SELECT  questionvalue_id, questionvalue_value, questionvalue_active
            FROM " . $dbPrefix . "questionmodule_value
            WHERE questionmodule_id = ?");

        //get possible answers in the questionmodule
        $STH4 = $DBH->prepare("
            SELECT  answer_value, answer_checked, answer_edited
            FROM " . $dbPrefix . "answer
            WHERE user_id = ?
            AND questionvalue_id = ?");

        $i = 0;
        while ($row = $STH->fetch()) {
            $uquestions[$i] = $row;
            $STH2->bindParam(1, $uquestions[$i]->question_id);
            $STH2->execute();
            $STH2->setFetchMode(PDO::FETCH_OBJ);
            $j = 0;
            while ($row2 = $STH2->fetch()) {
                $uquestionmodules[$i][$j] = $row2;
                $STH3->bindParam(1, $uquestionmodules[$i][$j]->questionmodule_id);
                $STH3->execute();
                $STH3->setFetchMode(PDO::FETCH_OBJ);
                $k = 0;
                $isanswered[$i][$j] = false;
                while ($row3 = $STH3->fetch()) {
                    $uquestionmodulevalues[$i][$j][$k] = $row3;

                    $STH4->bindParam(1, $globalid);
                    $STH4->bindParam(2, $uquestionmodulevalues[$i][$j][$k]->questionvalue_id);
                    $STH4->execute();
                    $STH4->setFetchMode(PDO::FETCH_OBJ);

                    $uanswermodulevalues[$i][$j][$k] = $STH4->fetch();

                    if (isset($uanswermodulevalues[$i][$j][$k]->answer_edited)) {
                        $isanswered[$i][$j] = true;
                    }

                    $k++;
                }
                $j++;
            }
            $i++;
        }

        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}


if ($_POST['next'] == "Upload") {

    $maxFilesize = (int) (@ini_get('post_max_size')) * 1024 * 1024;

    for ($i = 0; $i < count($uquestions); $i++) {

        if ($_FILES[$uquestions[$i]->question_id]['name'][0] != '') {

            //check filename and size
            $answervalue = null;
            $uploaderror[] = null;
            for ($j = 0; $j < count($_FILES[$uquestions[$i]->question_id]['name']); $j++) {
                $extension = getExtension($_FILES[$uquestions[$i]->question_id]['name'][$j]);
                if (!in_array($extension, $allowedExtenstions)) {
                    $uploaderror[$uquestions[$i]->question_id] = "This type of files is not allwoed: *." . $extension;
                    //ignore file if forbidden extension
                    continue;
                }

                if ($_FILES[$uquestions[$i]->question_id]['size'][$j] > $maxFilesize) {
                    $uploaderror[$uquestions[$i]->question_id] = "The file size is too big.";
                    continue;
                }

                // unique filename
                $save_as_name = uniqid() . '_' . $_FILES[$uquestions[$i]->question_id]['name'][$j];
                // save file on server
                move_uploaded_file($_FILES[$uquestions[$i]->question_id]['tmp_name'][$j], $rootDir . 'upload/' . $save_as_name);
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
                    $STH->bindParam(1, $uquestions[$i]->question_id);
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
                //getting questions and questionvalues again to show the right answer-stuff
                //NOT NICE PLEASE REMOVE IT WHEN YOU RETHINK!
                try {
                    $DBH = new PDO(
                                    "mysql:host=$dbHost;dbname=$dbName",
                                    $dbUser,
                                    $dbPasswd,
                                    array(
                                        PDO::ATTR_ERRMODE => $errormode,
                                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                            ));

                    $STH = $DBH->prepare("
            SELECT  exclass_sort
            FROM " . $dbPrefix . "exclass
            WHERE exclass_id = ?");
                    $STH->bindParam(1, $globalexclass);
                    $STH->execute();
                    $STH->setFetchMode(PDO::FETCH_OBJ);
                    $num = $STH->fetch();

                    //get questions
                    //make numbers for execution class
                    $exnums = null;
                    for ($i = 1; $i < $num->exclass_sort; $i++) {
                        $exnums .= $i;
                        $exnums .= ", ";
                    }
                    $exnums .= $num->exclass_sort;

                    $STH = $DBH->prepare("
            SELECT  q.question_id, q.question_name, q.question_description, sq.question_sort
            FROM " . $dbPrefix . "question AS q
            INNER JOIN " . $dbPrefix . "subsection_question AS sq ON q.question_id = sq.question_id 
            WHERE sq.subsection_id = ?
            AND sq.question_group = ?
            AND sq.question_active = 1
            AND sq.question_exclass IN (" . $exnums . ")
            ORDER BY sq.question_sort");
                    $STH->bindParam(1, $_GET['usubsectionid']);
                    $STH->bindParam(2, $_GET['uquestiongroup']);
                    $STH->execute();
                    $STH->setFetchMode(PDO::FETCH_OBJ);

                    //get questionmodulevalues
                    $STH2 = $DBH->prepare("
            SELECT  questionmodule_id, questionmodule_description, questionmodule_type
            FROM " . $dbPrefix . "questionmodule
            WHERE question_id = ?
            ORDER BY questionmodule_sort");

                    //get questionmodule_values
                    $STH3 = $DBH->prepare("
            SELECT  questionvalue_id, questionvalue_value, questionvalue_active
            FROM " . $dbPrefix . "questionmodule_value
            WHERE questionmodule_id = ?");

                    //get possible answers in the questionmodule
                    $STH4 = $DBH->prepare("
            SELECT  answer_value, answer_checked, answer_edited
            FROM " . $dbPrefix . "answer
            WHERE user_id = ?
            AND questionvalue_id = ?");

                    $i = 0;
                    while ($row = $STH->fetch()) {
                        $uquestions[$i] = $row;
                        $STH2->bindParam(1, $uquestions[$i]->question_id);
                        $STH2->execute();
                        $STH2->setFetchMode(PDO::FETCH_OBJ);
                        $j = 0;
                        while ($row2 = $STH2->fetch()) {
                            $uquestionmodules[$i][$j] = $row2;
                            $STH3->bindParam(1, $uquestionmodules[$i][$j]->questionmodule_id);
                            $STH3->execute();
                            $STH3->setFetchMode(PDO::FETCH_OBJ);
                            $k = 0;
                            $isanswered[$i][$j] = false;
                            while ($row3 = $STH3->fetch()) {
                                $uquestionmodulevalues[$i][$j][$k] = $row3;

                                $STH4->bindParam(1, $globalid);
                                $STH4->bindParam(2, $uquestionmodulevalues[$i][$j][$k]->questionvalue_id);
                                $STH4->execute();
                                $STH4->setFetchMode(PDO::FETCH_OBJ);

                                $uanswermodulevalues[$i][$j][$k] = $STH4->fetch();

                                if (isset($uanswermodulevalues[$i][$j][$k]->answer_edited)) {
                                    $isanswered[$i][$j] = true;
                                }

                                $k++;
                            }
                            $j++;
                        }
                        $i++;
                    }

                    $DBH = null;
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
            }
        }
    }
}

//user clicks on next and the stuff will be safed in the Database
if ($_POST['next'] == "Save & Next") {
    for ($i = 0; $i < count($uquestions); $i++) {
        for ($j = 0; $j < count($uquestionmodules[$i]); $j++) {
            for ($k = 0; $k < count($uquestionmodulevalues[$i][$j]); $k++) {

                //first of all the radiobuttons
                if (isset($_POST['radio'][$uquestions[$i]->question_id])) {
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
                        $STH->bindParam(1, $uquestionmodulevalues[$i][$j][$k]->questionvalue_id);
                        $STH->bindParam(2, $globalid);
                        $STH->execute();

                        //add
                        if ($_POST['radio'][$uquestions[$i]->question_id] == $uquestionmodulevalues[$i][$j][$k]->questionvalue_id) {
                            //create new answer
                            $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "answer
                                (user_id, questionvalue_id, answer_checked)
                                VALUES (?, ?, ?)");
                            $STH->bindParam(1, $globalid);
                            $STH->bindParam(2, $uquestionmodulevalues[$i][$j][$k]->questionvalue_id);
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
                        $STH->bindParam(1, $uquestionmodulevalues[$i][$j][$k]->questionvalue_id);
                        $STH->bindParam(2, $globalid);
                        $STH->execute();
                        //add
                        if ($_POST['checkbox'][$uquestions[$i]->question_id][$uquestionmodulevalues[$i][$j][$k]->questionvalue_id] == '1') {
                            //create new answer
                            $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "answer
                                (user_id, questionvalue_id, answer_checked)
                                VALUES (?, ?, ?)");
                            $STH->bindParam(1, $globalid);
                            $STH->bindParam(2, $uquestionmodulevalues[$i][$j][$k]->questionvalue_id);
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
                if (isset($_POST['single'][$uquestions[$i]->question_id])) {
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
                        $STH->bindParam(1, $uquestionmodulevalues[$i][$j][$k]->questionvalue_id);
                        $STH->bindParam(2, $globalid);
                        $STH->execute();
                        //add
                        if (isset($_POST['single'][$uquestions[$i]->question_id][$uquestionmodulevalues[$i][$j][$k]->questionvalue_id])) {
                            //create new answer
                            $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "answer
                                (user_id, questionvalue_id, answer_value, answer_checked)
                                VALUES (?, ?, ?, null)");
                            $STH->bindParam(1, $globalid);
                            $STH->bindParam(2, $uquestionmodulevalues[$i][$j][$k]->questionvalue_id);
                            $STH->bindParam(3, $_POST['single'][$uquestions[$i]->question_id][$uquestionmodulevalues[$i][$j][$k]->questionvalue_id]);
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
                if (isset($_POST['multi'][$uquestions[$i]->question_id])) {
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
                        $STH->bindParam(1, $uquestionmodulevalues[$i][$j][$k]->questionvalue_id);
                        $STH->bindParam(2, $globalid);
                        $STH->execute();
                        //add
                        if (isset($_POST['multi'][$uquestions[$i]->question_id][$uquestionmodulevalues[$i][$j][$k]->questionvalue_id])) {
                            //create new answer
                            $STH = $DBH->prepare("
                                INSERT INTO " . $dbPrefix . "answer
                                (user_id, questionvalue_id, answer_value, answer_checked)
                                VALUES (?, ?, ?, null)");
                            $STH->bindParam(1, $globalid);
                            $STH->bindParam(2, $uquestionmodulevalues[$i][$j][$k]->questionvalue_id);
                            $STH->bindParam(3, $_POST['multi'][$uquestions[$i]->question_id][$uquestionmodulevalues[$i][$j][$k]->questionvalue_id]);
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
    //redirect to next page or overviewpage
    //it is necessary to check if the last questiongroup is reached. After the last questiongroup it should be a redirect to the overviewpage
    try {
        $DBH = new PDO(
                        "mysql:host=$dbHost;dbname=$dbName",
                        $dbUser,
                        $dbPasswd,
                        array(
                            PDO::ATTR_ERRMODE => $errormode,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));
        $STH = $DBH->prepare("
                            SELECT max(question_group) AS max 
                            FROM " . $dbPrefix . "subsection_question 
                                WHERE question_active = 1 
                                AND subsection_id = ?");
        $STH->bindParam(1, $_GET['usubsectionid']);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $groupmax = $STH->fetch();

        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }



    if ($sslonly == TRUE) {
        $httpprefix = 'https://';
    } else {
        $httpprefix = 'http://';
    }
    if ($groupmax->max == $_GET['uquestiongroup']) {
        $nextpage = $httpprefix . $website . 'verification.php?mode=umodule&umoduleid=' . $_GET['umoduleid'];
    } else {
        $nextpage = $httpprefix . $website . 'verification.php?mode=uquestion&umoduleid=' . $_GET['umoduleid'] . '&usectionid=' . $_GET['usectionid'] . '&usubsectionid=' . $_GET['usubsectionid'] . '&uquestiongroup=' . ($_GET['uquestiongroup'] + 1);
    }
    header("Location: " . $nextpage);
    exit;
}
?>
