<?php
require_once 'localsettings.php';
$pageNavi = 'Help';

$questiontmp = getQuestions(TRUE, $_GET['subsectionid'], $_GET['questionid']);

$errorNoHelp = null;
$errorNoReference = null;


if ($questiontmp[0]->question_help == '') {
    $errorNoHelp = TRUE;
}

if ($questiontmp[0]->question_reference == '') {
    $errorNoReference = TRUE;
}


include $templateDir . 'help.tpl.php';
?>