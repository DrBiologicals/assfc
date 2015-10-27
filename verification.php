<?php
require_once 'session.php';
require_once 'localsettings.php';

function getSubsectionData($subsectionid) {
    global $dbHost;
    global $dbName;
    global $dbUser;
    global $dbPasswd;
    global $dbPrefix;
    global $errormode;

    try {
        $DBH = new PDO(
                        "mysql:host=$dbHost;dbname=$dbName",
                        $dbUser,
                        $dbPasswd,
                        array(
                            PDO::ATTR_ERRMODE => $errormode,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));

        // get questiongroups for group-bar
        $STH = $DBH->prepare("
            SELECT *
            FROM " . $dbPrefix . "subsection
            WHERE subsection_id = ?");

        $STH->bindParam(1, $subsectionid);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $row = $STH->fetch();

        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $row;
}

function getGroupcolor($currentGroup, $group, $subsection) {
    global $dbHost;
    global $dbName;
    global $dbUser;
    global $dbPasswd;
    global $dbPrefix;
    global $errormode;
    global $globalid;

    $color = '';

    if ($currentGroup == $group) {
        $color .= 'border-color: #000000; ';
    }

    try {
        $DBH = new PDO(
                        "mysql:host=$dbHost;dbname=$dbName",
                        $dbUser,
                        $dbPasswd,
                        array(
                            PDO::ATTR_ERRMODE => $errormode,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));

        // get questiongroups for group-bar
        $STH = $DBH->prepare("
            SELECT DISTINCT  a.answer_edited
            FROM " . $dbPrefix . "answer AS a
            INNER JOIN " . $dbPrefix . "questionmodule_value AS qmv ON qmv.questionvalue_id = a.questionvalue_id
            INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
            INNER JOIN " . $dbPrefix . "question AS q ON q.question_id = qm.question_id
            INNER JOIN " . $dbPrefix . "subsection_question AS sq on sq.question_id = q.question_id
            WHERE sq.subsection_id = ?
            AND a.user_id = ?
            AND sq.question_group = ?");

        $STH->bindParam(1, $subsection);
        $STH->bindParam(2, $globalid);
        $STH->bindParam(3, $group);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $row = $STH->fetch();

        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    if ($row->answer_edited != null) {
        $color .= 'background: greenyellow; ';
    } else {
        $color .= 'background: yellow; ';
    }

    return $color;
}



/**
 * This function provides the available groups of a Subsection
 * 
 * @param int $subsectionid Subsection ID
 * @return ArrayObject Group Names
 */
function getGroups($subsectionid) {
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
            SELECT question_group 
            FROM " . $dbPrefix . "subsection_question
            WHERE subsection_id = ?
            GROUP BY question_group
            ORDER BY question_group");
        $STH->bindParam(1, $subsectionid);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $i = 0;
        while ($row = $STH->fetch()) {
            $group[$i] = $row;
            $i++;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $group;
}


//check if usergroup can see this page
if ($globalusergroup == 3 || $globalusergroup == 2 || $globalusergroup == 1) {
    if ($_GET['mode'] == 'main') {
        require_once 'verification.main.php';
    } elseif ($_GET['mode'] == 'umodule') {
        require_once 'verification.umodule.php';
    } elseif ($_GET['mode'] == 'uquestion') {
        require_once 'verification.uquestion.php';
    }
    include $templateDir . 'verification.tpl.php';
}

?>