<?php
#This file contains every necessary information to move the system to a new server.
#Please make sure that you know what you are doing here!
#
#Website data

$adminemail = 'nzwc01';
$website = 'http://assfc.host56.com/assfc/';
$sslonly = TRUE;
#
#Database userdata
$dbUser = 'a3170674_assfc';
$dbPasswd = 'xep624';
$dbHost = 'mysql9.000webhost.com';
$dbName = 'a3170674_assfcd';
$dbPrefix = 'assfc_';
$errormode = 'PDO::ERRMODE_WARNING';
#
#
#direction to root (example: '/srv/www/htdocs/website.com/'
$rootDir = 'assfc/htdocs/';
#options for tempalte
#used templated dir (example: 'templates/templatename/')
$templateDir = 'templates/hera/';
#
#
#settings for the website
#
#pagetitle
$pageTitle = 'HERA: Steel Construction Accreditation';


#Allowed file extensions
$allowedExtenstions = array('png', 'gif', 'jpg', 'jpeg', 'pdf', 'dia', 'zip', 'svg', 'rar', 'svgz', 'odt', 'ods', 'odg', 'odc', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx');
###### DO NOT ANYTHING CHANGE ABOVE THIS ######

require_once 'inc/wikiparser/wiky.inc.php';

/**
 * Reads a string and returns propper html inclusive htmlspecialchars() and wiki-syntax.
 * 
 * @param String $string The string to change from wiki-tex
 * @return String HTML-Code
 */
function getHtml($input) {
    $wiky = new wiky;

// Call for the function parse() on the variable You created and pass some unparsed text to it, it will return parsed HTML or false if the content was empty. In this example we are loading the file input.wiki, escaping all html characters with htmlspecialchars, running parse and echoing the output

    $output = null;
    $order = array("\r\n", "\n", "\r");
    $input = str_replace($order, "\n", $input);
    $input = htmlspecialchars($input);
    $output = $wiky->parse($input);

    return $output;
}

/**
 * Returns all Module data sorted by the module_sort. If there is a moduleid it will return only the data of the searched module.
 * 
 * @param boolean $active true = returns only the active modules, false = returns also the inactive modules
 * @param int $moduleid If provided: Gives back only data from module with module_id. If not provided: Gives back all modules
 * @return ArrayObject Array of Objects data of the Modules
 */
function getModules($active, $moduleid = null) {
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

        $activated = "";
        if ($active == true) {
            $activated = 'WHERE module_active = 1';
        }

        $where = "";
        if ($moduleid != null) {
            $where = "AND module_id = " . $moduleid;
        }

        // get questiongroups for group-bar
        $STH = $DBH->prepare("
            SELECT *
            FROM " . $dbPrefix . "module
            " . $activated . "
            " . $where . "
            ORDER BY module_sort");

        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);

        $i = 0;
        $objectarray = null;
        while ($row = $STH->fetch()) {
            $objectarray[$i] = $row;
            $i++;
        }
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $objectarray;
}

/**
 * Returns all Section data which is in the Module sorted by the section_sort. If a section id is provided it only returns data of this explicit section.
 * 
 * @param int $moduleid The module ID
 * @param int $sectionid The section ID
 * @param boolean $active true = returns only the active section, false = returns also the inactive sections
 * @return ArrayObject Array of Objects with Sections of the Module
 */
function getSections($active, $moduleid, $sectionid = null) {

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

        if ($active == true) {
            $activated = 'AND section_active = 1';
        }

        if (isset($sectionid)) {
            $sectionsearch = "AND s.section_id =" . $sectionid;
        }

        // get questiongroups for group-bar
        $STH = $DBH->prepare("
            SELECT *
            FROM " . $dbPrefix . "section AS s
            INNER JOIN " . $dbPrefix . "module_section AS ms ON ms.section_id = s.section_id
            WHERE ms.module_id = ?
            " . $activated . "
            " . $sectionsearch . "
            ORDER BY section_sort");


        $STH->bindParam(1, $moduleid);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);

        $i = 0;
        $objectarray = null;
        while ($row = $STH->fetch()) {
            $objectarray[$i] = $row;
            $i++;
        }
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $objectarray;
}

/**
 * Returns all Subsection data which is in the subsection sorted by the section_sort. If a subsectionid is provided it returns only data from this specific subsection.
 * 
 * @param int $sectionid The section ID
 * @param int $subsectionid The subsection ID
 * @param boolean $active true = returns only the active subsection, false = returns also the inactive subsections
 * @return ArrayObject Array of Objects with subsections of the section
 */
function getSubsections($active, $sectionid, $subsectionid = null) {

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

        if ($active == true) {
            $activated = 'AND subsection_active = 1';
        }

        if (isset($subsectionid)) {
            $sectionsearch = "AND ss.subsection_id =" . $subsectionid;
        }

        // get questiongroups for group-bar
        $STH = $DBH->prepare("
            SELECT *
            FROM " . $dbPrefix . "subsection AS ss
            INNER JOIN " . $dbPrefix . "section_subsection AS sns ON sns.subsection_id = ss.subsection_id
            WHERE sns.section_id = ?
            " . $activated . "
            " . $sectionsearch . "
            ORDER BY subsection_sort");


        $STH->bindParam(1, $sectionid);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);

        $i = 0;
        $objectarray = null;
        while ($row = $STH->fetch()) {
            $objectarray[$i] = $row;
            $i++;
        }
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $objectarray;
}

/**
 * Returns all Question data sorted by the question_sort. If there is a questionid it will return only the data of the searched question.
 * 
 * @param boolean $active true = returns only the active questions, false = returns also the inactive questions
 * @param int $subsectionid The subsectionid from the questions.
 * @param int $questionid If provided: Gives back only data from the question with question_id. If not provided: Gives back all questions
 * @return ArrayObject Array of Objects data of the Modules
 */
function getQuestions($active, $subsectionid, $questionid = null) {
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

        $activated = "";
        if ($active == true) {
            $activated = 'AND sq.question_active = 1';
        }

        $where = "";
        if ($questionid != null) {
            $where = "AND q.question_id = " . $questionid;
        }

        // get questiongroups for group-bar
        $STH = $DBH->prepare("
            SELECT q.*, sq.*
            FROM " . $dbPrefix . "question AS q
            INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.question_id = q.question_id
            WHERE sq.subsection_id = ?
            " . $activated . "
            " . $where . "
            ORDER BY sq.question_sort");

        $STH->bindParam(1, $subsectionid);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);

        $i = 0;
        $objectarray = null;
        while ($row = $STH->fetch()) {
            $objectarray[$i] = $row;
            $i++;
        }
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $objectarray;
}

/**
 * Returns all Questionvalues data sorted by the question_sort. If there is a questionid it will return only the data of the searched question.
 * 
 * @param boolean $active true = returns only the active questions, false = returns also the inactive questions
 * @param int $subsectionid The subsectionid from the questions.
 * @param int $questionid If provided: Gives back only data from the question with question_id. If not provided: Gives back all questions
 * @return ArrayObject Array of Objects data of the Modules
 */

/**
 * Returns the question progress of answered questions of all active subsections in all active sections in this module. When provided: Gives back only progress of questions in exact this execution class.
 *  
 * @param int $moduleid The Module-ID
 * @param int $exclass The interested Executionclass (if provided)
 * @return int Progress of the answered Questions. e.G. 0,6 for 60%
 */
function getModuleProgress($moduleid, $exclass = null) {
    global $dbHost;
    global $dbName;
    global $dbUser;
    global $dbPasswd;
    global $dbPrefix;
    global $errormode;
    global $globalexclass;
    global $globalid;

    try {
        $DBH = new PDO(
                        "mysql:host=$dbHost;dbname=$dbName",
                        $dbUser,
                        $dbPasswd,
                        array(
                            PDO::ATTR_ERRMODE => $errormode,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));

        if ($exclass == null) {
            $exclass = $globalexclass;
            $STH = $DBH->prepare("
            SELECT  exclass_sort
            FROM " . $dbPrefix . "exclass
            WHERE exclass_id = ?");
            $STH->bindParam(1, $exclass);
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
        } else {
            $exnums = $exclass;
        }
        // get amount of questions
        $STH = $DBH->prepare("
            SELECT count(sq.question_id) AS num
            FROM " . $dbPrefix . "subsection_question AS sq
            INNER JOIN " . $dbPrefix . "section_subsection AS ss ON ss.subsection_id = sq.subsection_id
            INNER JOIN " . $dbPrefix . "module_section AS ms ON ms.section_id = ss.section_id
            WHERE ms.module_id = ?
            AND ms.section_active = 1
            AND ss.subsection_active = 1
            AND sq.question_active = 1
            AND sq.question_exclass IN (" . $exnums . ")");
        $STH->bindParam(1, $moduleid);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $row = $STH->fetch();

        $allquestions = $row->num;
        //get amount of answered questions
        $STH = $DBH->prepare("
            SELECT count(DISTINCT sq.question_id) AS num
            FROM " . $dbPrefix . "subsection_question AS sq           
            INNER JOIN " . $dbPrefix . "section_subsection AS ss on ss.subsection_id = sq.subsection_id
                INNER JOIN " . $dbPrefix . "module_section AS ms ON ms.section_id = ss.section_id
            INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.question_id = sq.question_id
            INNER JOIN " . $dbPrefix . "questionmodule_value AS qmv ON qmv.questionmodule_id = qm.questionmodule_id
            INNER JOIN " . $dbPrefix . "answer AS a ON a.questionvalue_id = qmv.questionvalue_id
            WHERE ms.module_id = ?
            AND ms.section_active = 1
            AND ss.subsection_active = 1
            AND sq.question_active = 1
            AND a.user_id = ?
            AND sq.question_exclass IN (" . $exnums . ")");

        $STH->bindParam(1, $moduleid);
        $STH->bindParam(2, $globalid);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $row = $STH->fetch();
        $answeredquestions = $row->num;

        if ($allquestions != 0) {
            $progress = $answeredquestions / $allquestions;
        } else {
            $progress = 0;
        }


        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $progress;
}

/**
 * Returns the Progress of answered questions of all active subsections and all active questions in this section.
 * 
 * @param int $sectionid The Section-ID
 * @return int Progress of the answered Questions. e.G. 0,6 for 60%
 */
function getSectionProgress($sectionid) {
    global $dbHost;
    global $dbName;
    global $dbUser;
    global $dbPasswd;
    global $dbPrefix;
    global $errormode;
    global $globalexclass;
    global $globalid;

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

        // get amount of questions
        $STH = $DBH->prepare("
            SELECT count(sq.question_id) AS num
            FROM " . $dbPrefix . "subsection_question AS sq
            INNER JOIN " . $dbPrefix . "section_subsection AS ss ON ss.subsection_id = sq.subsection_id
            WHERE ss.section_id = ?
            AND ss.subsection_active = 1
            AND sq.question_active = 1
            AND sq.question_exclass IN (" . $exnums . ")");


        $STH->bindParam(1, $sectionid);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $row = $STH->fetch();

        $allquestions = $row->num;


        //get amount of answered questions
        $STH = $DBH->prepare("
            SELECT count(DISTINCT sq.question_id) AS num
            FROM " . $dbPrefix . "subsection_question AS sq
            INNER JOIN " . $dbPrefix . "section_subsection AS ss on ss.subsection_id = sq.subsection_id
            INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.question_id = sq.question_id
            INNER JOIN " . $dbPrefix . "questionmodule_value AS qmv ON qmv.questionmodule_id = qm.questionmodule_id
            INNER JOIN " . $dbPrefix . "answer AS a ON a.questionvalue_id = qmv.questionvalue_id
            WHERE ss.section_id = ?
            AND sq.question_active = 1
            AND a.user_id = ?
            AND sq.question_exclass IN (" . $exnums . ")");

        $STH->bindParam(1, $sectionid);
        $STH->bindParam(2, $globalid);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $row = $STH->fetch();
        $answeredquestions = $row->num;


        if ($allquestions != 0) {
            $progress = $answeredquestions / $allquestions;
        } else {
            $progress = 0;
        }


        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $progress;
}

/**
 * Returns the Progress of answered questions of all active questions in this subsection.
 * 
 * @param int $subsectionid The Subsection-ID
 * @return int Progress of the answered Questions. e.G. 0,6 for 60%
 */
function getSubsectionProgress($subsectionid) {
    global $dbHost;
    global $dbName;
    global $dbUser;
    global $dbPasswd;
    global $dbPrefix;
    global $errormode;
    global $globalexclass;
    global $globalid;

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


        // get amount of questions
        $STH = $DBH->prepare("
            SELECT count(subsection_id) AS num
            FROM " . $dbPrefix . "subsection_question
            WHERE subsection_id = ?
            AND question_active = 1
            AND question_exclass IN (" . $exnums . ")");


        $STH->bindParam(1, $subsectionid);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $row = $STH->fetch();

        $allquestions = $row->num;


        //get amount of answered questions
        $STH = $DBH->prepare("
            SELECT count(sq.subsection_id) AS num
            FROM " . $dbPrefix . "subsection_question AS sq
            INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.question_id = sq.question_id
            INNER JOIN " . $dbPrefix . "questionmodule_value AS qmv ON qmv.questionmodule_id = qm.questionmodule_id
            INNER JOIN " . $dbPrefix . "answer AS a ON a.questionvalue_id = qmv.questionvalue_id
            WHERE subsection_id = ?
            AND sq.question_active = 1
            AND a.user_id = ?
            AND sq.question_exclass IN (" . $exnums . ")
            GROUP BY sq.question_id");

        $STH->bindParam(1, $subsectionid);
        $STH->bindParam(2, $globalid);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $answeredquestions = 0;
        while ($STH->fetch()) {
            $answeredquestions++;
        }

        if ($allquestions != 0) {
            $progress = $answeredquestions / $allquestions;
        } else {
            $progress = 0;
        }


        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $progress;
}

/**
 * Returns the status of the provided module section, subsection question or questionvalue. The function checks for all answered questionvalues, if the questionvalue needs to be answered or not. It checks also if all questions in the module or subsection is answered.
 * - A returning 0 means: Revision is needed
 * - A returning 1 means: No revision needed
 * - A returning 2 means: Not all questions are answered yet
 * 
 * @param int $moduleOrSection 0 means module, 1 means section, 2 means subsection, 3 means a single question, 4 means a single questionvalue
 * @param int $id The questionvalue id, question id, subsection id, section id or module id
 * @return int the status of the module, section subsection, question, questionvalue: 0 means revision needed, 1 means no revision neede, 2 means not all question answered yet (2 will be only provided in modules, sections and subsections)
 */
function checkforRevision($moduleOrSection, $id) {

    global $dbHost;
    global $dbName;
    global $dbUser;
    global $dbPasswd;
    global $dbPrefix;
    global $errormode;
    global $globalexclass;
    global $globalid;



    //is it module or section?
    // 0 means module
    // 1 means section
    if ($moduleOrSection == 0) {
        //it is a module!
        //check if all questions are processed
        if (getModuleProgress($id) != 1) {
            return 2;
        }

        //now check if revision is needed or not
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



            // get questionvalue_id of all active questions in module which are required
            $STH = $DBH->prepare("
                SELECT qmv.questionvalue_id, qm.questionmodule_type, qmv.questionvalue_value
                FROM " . $dbPrefix . "questionmodule_value AS qmv
                INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
                INNER JOIN " . $dbPrefix . "question AS q ON q.question_id = qm.question_id
                INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.question_id = q.question_id
                INNER JOIN " . $dbPrefix . "section_subsection AS ss ON ss.subsection_id = sq.subsection_id
                INNER JOIN " . $dbPrefix . "module_section AS ms ON ms.section_id = ss.section_id
                WHERE ms.module_id = ?
                AND ms.section_active = 1
                AND ss.subsection_active = 1
                AND sq.question_active = 1
                AND qmv.questionvalue_required = 1
                AND question_exclass IN (" . $exnums . ")
                ORDER BY qmv.questionvalue_id");

            $STH2 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "answer
                WHERE user_id = ?
                AND questionvalue_id = ?
                ORDER BY questionvalue_id");


            $STH->bindParam(1, $id);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_OBJ);

            $i = 0;
            while ($row = $STH->fetch()) {
                $STH2->bindParam(1, $globalid);
                $STH2->bindParam(2, $row->questionvalue_id);
                $STH2->execute();
                $STH2->setFetchMode(PDO::FETCH_OBJ);

                $row2 = $STH2->fetch();

                if ($row2->answer_edited == null) {
                    $DBH = null;
                    return 0;
                } elseif ($row->questionmodule_type == 3 || $row->questionmodule_type == 4) {
                    if ($row2->answer_value == '') {
                        $DBH = null;
                        return 0;
                    }
                }
                $i++;
            }

            $DBH = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return 1;
    } elseif ($moduleOrSection == '1') {
        //it is a section!
        //check if all questions are processed
        if (getSectionProgress($id) != 1) {
            return 2;
        }

        //now check if revision is needed or not
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



            // get questionvalue_id of all active questions in module which are required
            $STH = $DBH->prepare("
                SELECT qmv.questionvalue_id, qm.questionmodule_type, qmv.questionvalue_value
                FROM " . $dbPrefix . "questionmodule_value AS qmv
                INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
                INNER JOIN " . $dbPrefix . "question AS q ON q.question_id = qm.question_id
                INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.question_id = q.question_id
                INNER JOIN " . $dbPrefix . "section_subsection AS ss ON ss.subsection_id = sq.subsection_id
                WHERE ss.section_id = ?
                AND ss.subsection_active = 1
                AND sq.question_active = 1
                AND qmv.questionvalue_required = 1
                AND question_exclass IN (" . $exnums . ")
                ORDER BY qmv.questionvalue_id");

            $STH2 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "answer
                WHERE user_id = ?
                AND questionvalue_id = ?
                ORDER BY questionvalue_id");


            $STH->bindParam(1, $id);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_OBJ);

            $i = 0;
            while ($row = $STH->fetch()) {
                $STH2->bindParam(1, $globalid);
                $STH2->bindParam(2, $row->questionvalue_id);
                $STH2->execute();
                $STH2->setFetchMode(PDO::FETCH_OBJ);

                $row2 = $STH2->fetch();

                if ($row2->answer_edited == null) {
                    $DBH = null;
                    return 0;
                } elseif ($row->questionmodule_type == 3 || $row->questionmodule_type == 4) {
                    if ($row2->answer_value == '') {
                        $DBH = null;
                        return 0;
                    }
                }
                $i++;
            }

            $DBH = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return 1;
    } elseif ($moduleOrSection == '2') {
        //it is a subsection!
        //check if all questions are processed
        if (getSubsectionProgress($id) != 1) {
            return 2;
        }

        //now check if revision is needed or not
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



            // get questionvalue_id of all active questions in module which are required
            $STH = $DBH->prepare("
                SELECT qmv.questionvalue_id, qm.questionmodule_type, qmv.questionvalue_value
                FROM " . $dbPrefix . "questionmodule_value AS qmv
                INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
                INNER JOIN " . $dbPrefix . "question AS q ON q.question_id = qm.question_id
                INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.question_id = q.question_id
                INNER JOIN " . $dbPrefix . "section_subsection AS ss ON ss.subsection_id = sq.subsection_id
                WHERE sq.subsection_id = ?
                AND sq.question_active = 1
                AND qmv.questionvalue_required = 1
                AND question_exclass IN (" . $exnums . ")
                ORDER BY qmv.questionvalue_id");

            $STH2 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "answer
                WHERE user_id = ?
                AND questionvalue_id = ?
                ORDER BY questionvalue_id");


            $STH->bindParam(1, $id);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_OBJ);

            $i = 0;
            while ($row = $STH->fetch()) {
                $STH2->bindParam(1, $globalid);
                $STH2->bindParam(2, $row->questionvalue_id);
                $STH2->execute();
                $STH2->setFetchMode(PDO::FETCH_OBJ);

                $row2 = $STH2->fetch();

                if ($row2->answer_edited == null) {
                    $DBH = null;
                    return 0;
                } elseif ($row->questionmodule_type == 3 || $row->questionmodule_type == 4) {
                    if ($row2->answer_value == '') {
                        $DBH = null;
                        return 0;
                    }
                }
                $i++;
            }

            $DBH = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return 1;
    } elseif ($moduleOrSection == '3') {
        //it is question
        //check if revision is needed or not
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



            // get questionvalue_id of all active questions in module which are required
            $STH = $DBH->prepare("
                SELECT qmv.questionvalue_id, qm.questionmodule_type, qmv.questionvalue_value
                FROM " . $dbPrefix . "questionmodule_value AS qmv
                INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
                INNER JOIN " . $dbPrefix . "question AS q ON q.question_id = qm.question_id
                WHERE q.question_id = ?
                AND qmv.questionvalue_required = 1
                ORDER BY qmv.questionvalue_id");

            $STH2 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "answer
                WHERE user_id = ?
                AND questionvalue_id = ?
                ORDER BY questionvalue_id");


            $STH->bindParam(1, $id);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_OBJ);

            $i = 0;
            while ($row = $STH->fetch()) {
                $STH2->bindParam(1, $globalid);
                $STH2->bindParam(2, $row->questionvalue_id);
                $STH2->execute();
                $STH2->setFetchMode(PDO::FETCH_OBJ);

                $row2 = $STH2->fetch();

                if ($row2->answer_edited == null) {
                    $DBH = null;
                    return 0;
                } elseif ($row->questionmodule_type == 3 || $row->questionmodule_type == 4) {
                    if ($row2->answer_value == '') {
                        $DBH = null;
                        return 0;
                    }
                }
                $i++;
            }

            $DBH = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return 1;
    } elseif ($moduleOrSection == '4') {
        //it is question
        //check if revision is needed or not
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



            // get questionvalue_id of all active questions in module which are required
            $STH = $DBH->prepare("
                SELECT qmv.questionvalue_id, qm.questionmodule_type, qmv.questionvalue_value
                FROM " . $dbPrefix . "questionmodule_value AS qmv
                INNER JOIN " . $dbPrefix . "questionmodule AS qm ON qm.questionmodule_id = qmv.questionmodule_id
                WHERE qmv.questionvalue_id = ?
                AND qmv.questionvalue_required = 1
                ORDER BY qmv.questionvalue_id");

            $STH2 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "answer
                WHERE user_id = ?
                AND questionvalue_id = ?
                ORDER BY questionvalue_id");


            $STH->bindParam(1, $id);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_OBJ);

            $i = 0;
            while ($row = $STH->fetch()) {
                $STH2->bindParam(1, $globalid);
                $STH2->bindParam(2, $row->questionvalue_id);
                $STH2->execute();
                $STH2->setFetchMode(PDO::FETCH_OBJ);

                $row2 = $STH2->fetch();

                if ($row2->answer_edited == null) {
                    $DBH = null;
                    return 0;
                } elseif ($row->questionmodule_type == 3 || $row->questionmodule_type == 4) {
                    if ($row2->answer_value == '') {
                        $DBH = null;
                        return 0;
                    }
                }
                $i++;
            }

            $DBH = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return 1;
    }
}

/**
 * Returns the question data of questions where revision is needed out of the module, section or subsection of the provided id. The function returns only active and questions where revision is needed.
 * 
 * @param int $moduleOrSection 0 means module, 1 means section, 2 means subsection
 * @param int $id The subsection, section or module id
 * @return ArrayObject All needed questiondata in a array.
 */
function getRevisionData($moduleOrSection, $id) {

    global $dbHost;
    global $dbName;
    global $dbUser;
    global $dbPasswd;
    global $dbPrefix;
    global $errormode;
    global $globalexclass;
    global $globalid;



    //is it module or section?
    // 0 means module
    // 1 means section
    if ($moduleOrSection == 0) {
        //it is a module
        //get all questions in this module

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



            // get questionvalue_id of all active questions in module which are required
            $STH = $DBH->prepare("
                SELECT q.*
                FROM " . $dbPrefix . "question AS q
                INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.question_id = q.question_id
                INNER JOIN " . $dbPrefix . "section_subsection AS ss ON ss.subsection_id = sq.subsection_id
                INNER JOIN " . $dbPrefix . "module_section AS ms ON ms.section_id = ss.section_id
                WHERE ms.module_id = ?
                AND ms.section_active = 1
                AND ss.subsection_active = 1
                AND sq.question_active = 1
                AND sq.question_exclass IN (" . $exnums . ")
                ORDER BY q.question_id");

            $STH->bindParam(1, $id);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_OBJ);

            $STH2 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "questionmodule
                WHERE question_id = ?");

            $STH3 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "questionmodule_value
                WHERE questionmodule_id = ?");

            $STH4 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "answer
                WHERE questionvalue_id = ?
                AND user_id = ?");
            $i = 0;
            $output = null;
            while ($row = $STH->fetch()) {
                $output[$i] = $row;
                $STH2->bindParam(1, $row->question_id);
                $STH2->execute();
                $STH2->setFetchMode(PDO::FETCH_OBJ);
                $j = 0;
                while ($row2 = $STH2->fetch()) {
                    $output[$i]->questionmodule[$j] = $row2;
                    $STH3->bindParam(1, $row2->questionmodule_id);
                    $STH3->execute();
                    $STH3->setFetchMode(PDO::FETCH_OBJ);
                    $k = 0;
                    while ($row3 = $STH3->fetch()) {
                        $output[$i]->questionmodule[$j]->questionvalue[$k] = $row3;
                        if (checkforRevision(4, $row3->questionvalue_id) == 0) {
                            $output[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_revision = '1';
                        } else {
                            $output[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_revision = '0';
                        }

                        $STH4->bindParam(1, $row3->questionvalue_id);
                        $STH4->bindParam(2, $globalid);
                        $STH4->execute();
                        $STH4->setFetchMode(PDO::FETCH_OBJ);

                        while ($row4 = $STH4->fetch()) {
                            $output[$i]->questionmodule[$j]->questionvalue[$k]->answer_checked = $row4->answer_checked;
                            $output[$i]->questionmodule[$j]->questionvalue[$k]->answer_value = $row4->answer_value;
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

        //check if question need to be revisioned
        for ($i = 0; $i < count($output); $i++) {

            if (checkforRevision(3, $output[$i]->question_id) == 1) {
                unset($output[$i]);
            }
        }
        $output = array_values($output);

        return $output;
    } elseif ($moduleOrSection == '1') {
        //it is a section
        //get all questions in this section

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



            // get questionvalue_id of all active questions in module which are required
            $STH = $DBH->prepare("
                SELECT q.*
                FROM " . $dbPrefix . "question AS q
                INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.question_id = q.question_id
                INNER JOIN " . $dbPrefix . "section_subsection AS ss ON ss.subsection_id = sq.subsection_id
                INNER JOIN " . $dbPrefix . "module_section AS ms ON ms.section_id = ss.section_id
                WHERE ss.section_id = ?
                AND ms.section_active = 1
                AND ss.subsection_active = 1
                AND sq.question_active = 1
                AND sq.question_exclass IN (" . $exnums . ")
                ORDER BY q.question_id");

            $STH->bindParam(1, $id);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_OBJ);

            $STH2 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "questionmodule
                WHERE question_id = ?");

            $STH3 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "questionmodule_value
                WHERE questionmodule_id = ?");

            $STH4 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "answer
                WHERE questionvalue_id = ?
                AND user_id = ?");
            $i = 0;
            $output = null;
            while ($row = $STH->fetch()) {
                $output[$i] = $row;
                $STH2->bindParam(1, $row->question_id);
                $STH2->execute();
                $STH2->setFetchMode(PDO::FETCH_OBJ);
                $j = 0;
                while ($row2 = $STH2->fetch()) {
                    $output[$i]->questionmodule[$j] = $row2;
                    $STH3->bindParam(1, $row2->questionmodule_id);
                    $STH3->execute();
                    $STH3->setFetchMode(PDO::FETCH_OBJ);
                    $k = 0;
                    while ($row3 = $STH3->fetch()) {
                        $output[$i]->questionmodule[$j]->questionvalue[$k] = $row3;
                        if (checkforRevision(4, $row3->questionvalue_id) == 0) {
                            $output[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_revision = '1';
                        } else {
                            $output[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_revision = '0';
                        }

                        $STH4->bindParam(1, $row3->questionvalue_id);
                        $STH4->bindParam(2, $globalid);
                        $STH4->execute();
                        $STH4->setFetchMode(PDO::FETCH_OBJ);

                        while ($row4 = $STH4->fetch()) {
                            $output[$i]->questionmodule[$j]->questionvalue[$k]->answer_checked = $row4->answer_checked;
                            $output[$i]->questionmodule[$j]->questionvalue[$k]->answer_value = $row4->answer_value;
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

        //check if question need to be revisioned
        for ($i = 0; $i < count($output); $i++) {

            if (checkforRevision(3, $output[$i]->question_id) == 1) {
                unset($output[$i]);
            }
        }
        $output = array_values($output);

        return $output;
    } elseif ($moduleOrSection == '2') {
        //it is a section
        //get all questions in this section

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



            // get questionvalue_id of all active questions in module which are required
            $STH = $DBH->prepare("
                SELECT q.*
                FROM " . $dbPrefix . "question AS q
                INNER JOIN " . $dbPrefix . "subsection_question AS sq ON sq.question_id = q.question_id
                INNER JOIN " . $dbPrefix . "section_subsection AS ss ON ss.subsection_id = sq.subsection_id
                INNER JOIN " . $dbPrefix . "module_section AS ms ON ms.section_id = ss.section_id
                WHERE sq.subsection_id = ?
                AND ms.section_active = 1
                AND ss.subsection_active = 1
                AND sq.question_active = 1
                AND sq.question_exclass IN (" . $exnums . ")
                ORDER BY q.question_id");

            $STH->bindParam(1, $id);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_OBJ);

            $STH2 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "questionmodule
                WHERE question_id = ?");

            $STH3 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "questionmodule_value
                WHERE questionmodule_id = ?");

            $STH4 = $DBH->prepare("
                SELECT *
                FROM " . $dbPrefix . "answer
                WHERE questionvalue_id = ?
                AND user_id = ?");
            $i = 0;
            $output = null;
            $isradio = false;
            while ($row = $STH->fetch()) {
                $output[$i] = $row;
                $STH2->bindParam(1, $row->question_id);
                $STH2->execute();
                $STH2->setFetchMode(PDO::FETCH_OBJ);
                $j = 0;
                while ($row2 = $STH2->fetch()) {
                    $output[$i]->questionmodule[$j] = $row2;
                    $STH3->bindParam(1, $row2->questionmodule_id);
                    $STH3->execute();
                    $STH3->setFetchMode(PDO::FETCH_OBJ);
                    $k = 0;
                    while ($row3 = $STH3->fetch()) {
                        $output[$i]->questionmodule[$j]->questionvalue[$k] = $row3;
                        if (checkforRevision(4, $row3->questionvalue_id) == 0 && $output[$i]->questionmodule[$j]->questionmodule_type == 1
                                && $isradio == false) {
                            $output[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_revision = '1';
                            $isradio = true;
                        }else if (checkforRevision(4, $row3->questionvalue_id) == 0 && $isradio == false) {
                            $output[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_revision = '1';
                        }else {
                            $output[$i]->questionmodule[$j]->questionvalue[$k]->questionvalue_revision = '0';
                        }

                        $STH4->bindParam(1, $row3->questionvalue_id);
                        $STH4->bindParam(2, $globalid);
                        $STH4->execute();
                        $STH4->setFetchMode(PDO::FETCH_OBJ);

                        while ($row4 = $STH4->fetch()) {
                            $output[$i]->questionmodule[$j]->questionvalue[$k]->answer_checked = $row4->answer_checked;
                            $output[$i]->questionmodule[$j]->questionvalue[$k]->answer_value = $row4->answer_value;
                        }

                        $k++;
                    }
                    $isradio = false;
                    $j++;
                }
                $i++;
            }

            $DBH = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        //check if question need to be revisioned
        for ($i = 0; $i < count($output); $i++) {

            if (checkforRevision(3, $output[$i]->question_id) == 1) {
                unset($output[$i]);
            }
        }
        $output = array_values($output);

        return $output;
    } elseif ($moduleOrSection == '3') {
        //it is question
    }
}

/**
 * Returns all execution classes data from the database.
 * 
 * @return ArrayObject Dataarray with all execution classes.
 */
function getExecutiondata() {
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
            FROM " . $dbPrefix . "exclass
            ORDER BY exclass_sort");

        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);

        $i = 0;
        $objectarray = null;
        while ($row = $STH->fetch()) {
            $objectarray[$i] = $row;
            $i++;
        }
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $objectarray;
}

/**
 * Reads the answer-string of files in the database and returns a sorted array with all files
 * 
 * @param string $filestring The file string of the database.
 * @return ArrayObject Data array with all execution classes.
 */
function getFilelist($filestring) {

    $filelist['dsname'] = explode("|", $filestring);
    unset($filelist['dsname'][count($filelist['dsname']) - 1]);
    for ($m = 0; $m < count($filelist['dsname']); $m++) {
        $filelist['name'][$m] = substr($filelist['dsname'][$m], 14, strlen($filelist['dsname'][$m]) - 18);
        $filelist['extension'][$m] = getExtension($filelist['dsname'][$m]);
    }
    return $filelist;
}

function getExtension($name) {
    return (false === ( $p = strrpos($name, '.') ) ? '' : substr($name, ++$p));
}

?>
