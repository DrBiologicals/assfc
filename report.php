<?php

require_once 'session.php';
require_once 'localsettings.php';
//cehck if usergroup can see this page
if ($globalusergroup == '1') {


    // Check if entered USER-ID is in Database
    $userdata = null;
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
                SELECT u.*, c.country_name 
                FROM " . $dbPrefix . "user AS u
                INNER JOIN " . $dbPrefix . "country AS c ON c.country_id = u.user_country
                WHERE user_id = ?"
        );

        //bind variables
        $STH->bindParam(1, $_POST['userid']);

        //execute statement
        $STH->execute();

        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);

        //fetch data
        $userdata = $STH->fetch();

        //close db connection
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    if ($userdata != null && isset($_POST['create_report'])) {
        // The user-id is in the database, the report can be created.
        // get question data
        $questiondata = null;

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
                SELECT q.question_id, q.question_name, q.question_description
                FROM `" . $dbPrefix . "user` AS u
                INNER JOIN `" . $dbPrefix . "answer` AS a ON a.user_id = u.user_id 
                INNER JOIN `" . $dbPrefix . "questionmodule_value` AS qv ON qv.questionvalue_id = a.questionvalue_id
                INNER JOIN `" . $dbPrefix . "questionmodule` AS qm ON qm.questionmodule_id = qv.questionmodule_id
                INNER JOIN `" . $dbPrefix . "question` AS q ON q.question_id = qm.question_id
                WHERE u.user_id = ?
                GROUP BY q.question_id"
            );

            //bind variables
            $STH->bindParam(1, $_POST['userid']);

            //execute statement
            $STH->execute();

            //set fetch mode
            $STH->setFetchMode(PDO::FETCH_OBJ);

            //fetch data
            $i = 0;
            while ($row = $STH->fetch()) {
                $questiondata[$i] = $row;
                $i++;
            }

            //close db connection
            $DBH = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }


        // set all variables for the report
        $rCompanyname = $userdata->user_companyname;
        $rName = $userdata->user_lastname . ' ' . $userdata->user_firstname;
        $rStreet = $userdata->user_streetnumber . ' ' . $userdata->user_street;
        $rCity = $userdata->user_postcode . ' ' . $userdata->user_city;
        $rContry = $userdata->country_name;

        $rDate = date("d/m/Y", time());


        // create PDF
        require_once 'inc/fpdf/fpdf.php';

        $pdf = new FPDF();

        $pdf->SetMargins(20, 20, 20);
        $pdf->SetFont('Helvetica');

        //Titlepage
        $pdf->AddPage();

        $pdf->Image('templates/hera/images/logo.png', 90, 20, 100);
        $pdf->Ln(80);

        $pdf->SetFont('Helvetica', B, 50);
        $pdf->Cell(0, 0, 'Verification Report', 0, 2, C);
        $pdf->Ln(20);

        $pdf->SetFont('Helvetica', '', 20);
        $pdf->Cell(0, 0, 'for', 0, 2, C);
        $pdf->Ln(10);
        $pdf->SetFont('Helvetica', B, 12);
        $pdf->Cell(0, 8, $rCompanyname, 0, 2, C);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 6, $rName, 0, 2, C);
        $pdf->Cell(0, 6, $rStreet, 0, 2, C);
        $pdf->Cell(0, 6, $rCity, 0, 2, C);
        $pdf->Cell(0, 6, $rContry, 0, 2, C);
        $pdf->Ln(30);

        $pdf->SetFont('Helvetica', '', 20);
        $pdf->Cell(0, 10, 'Report Identification Number:', 0, 2, C);
        $pdf->Cell(0, 10, '#AFC 453223 - 1', 0, 2, C);
        $pdf->Ln(20);
        $pdf->Cell(0, 10, 'Date:', 0, 2, C);
        $pdf->Cell(0, 10, $rDate, 0, 2, C);
        $pdf->SetFont('Helvetica', '', 12);


        //Questionstuff
        $pdf->AddPage();


        for ($i = 0; $i < count($questiondata); $i++) {
            $pdf->SetFont('Helvetica', 'B', 12);
            $rQuestionname = "Q-ID #" . $questiondata[$i]->question_id . ": " . $questiondata[$i]->question_name;
            $pdf->Cell(0, 8, $rQuestionname, 0, 2);
            $pdf->SetFont('Helvetica', '', 12);
            $pdf->MultiCell(0, 5, $questiondata[$i]->question_description);
            $pdf->Ln(5);

            // Get and print all  module data
            for ($l = 1; $l <= 5; $l++) {



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
                SELECT qm.*
                FROM `" . $dbPrefix . "user` AS u
                INNER JOIN `" . $dbPrefix . "answer` AS a ON a.user_id = u.user_id 
                INNER JOIN `" . $dbPrefix . "questionmodule_value` AS qv ON qv.questionvalue_id = a.questionvalue_id
                INNER JOIN `" . $dbPrefix . "questionmodule` AS qm ON qm.questionmodule_id = qv.questionmodule_id
                INNER JOIN `" . $dbPrefix . "question` AS q ON q.question_id = qm.question_id
                WHERE u.user_id = ?
                AND qm.questionmodule_type = " . $l . "
                AND q.question_id = ?"
                    );

                    //bind variables
                    $STH->bindParam(1, $_POST['userid']);
                    $STH->bindParam(2, $questiondata[$i]->question_id);

                    //execute statement
                    $STH->execute();

                    //set fetch mode
                    $STH->setFetchMode(PDO::FETCH_OBJ);

                    //fetch data

                    $reportDescription = $STH->fetch();

                    //close db connection
                    $DBH = null;
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }


                if ($reportDescription != '') {
                    $pdf->SetFont('Helvetica', '', 12);
                    $reportDesc = "V-ID #" . $reportDescription->questionmodule_id . ": " . $reportDescription->questionmodule_description;
                    $pdf->Cell(0, 6, $reportDesc, 0, 2);
                }

                // Get Answers in Radiomodule
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
                SELECT qv.*, a.*
                FROM `" . $dbPrefix . "user` AS u
                INNER JOIN `" . $dbPrefix . "answer` AS a ON a.user_id = u.user_id 
                INNER JOIN `" . $dbPrefix . "questionmodule_value` AS qv ON qv.questionvalue_id = a.questionvalue_id
                INNER JOIN `" . $dbPrefix . "questionmodule` AS qm ON qm.questionmodule_id = qv.questionmodule_id
                INNER JOIN `" . $dbPrefix . "question` AS q ON q.question_id = qm.question_id
                WHERE u.user_id = ?
                AND qm.questionmodule_type = " . $l . "
                AND q.question_id = ?"
                    );

                    //bind variables
                    $STH->bindParam(1, $_POST['userid']);
                    $STH->bindParam(2, $questiondata[$i]->question_id);


                    //execute statement
                    $STH->execute();

                    //set fetch mode
                    $STH->setFetchMode(PDO::FETCH_OBJ);

                    //fetch data
                    $k = 0;
                    $rQuestionvalue = null;
                    while ($row = $STH->fetch()) {
                        $rQuestionvalue[$k] = $row;
                        $k++;
                    }

                    //close db connection
                    $DBH = null;
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }


                if (isset($reportDescription)) {
                    for ($j = 0; $j < count($rQuestionvalue); $j++) {
                        $pdf->SetFont('Helvetica', '', 12);

                        $reportDesc = "A-ID #";
                        $reportDesc .= $rQuestionvalue[$j]->questionvalue_id . " (" . $rQuestionvalue[$j]->questionvalue_value . "): ";

                        // answer is depending on the valuetype
                        switch ($l) {
                            case 1:
                                // its a radiobutton question module

                                $reportDesc .= "CHECK";

                                if ($rQuestionvalue[$j]->questionvalue_required == 0) {
                                    $reportDesc .= " (optional)";
                                }

                                $pdf->Cell(0, 6, $reportDesc, 0, 2);

                                break;
                            case 2:
                                // its a checkbox question module

                                $reportDesc .= "CHECK";

                                if ($rQuestionvalue[$j]->questionvalue_required == 0) {
                                    $reportDesc .= " (optional)";
                                }

                                $pdf->Cell(0, 6, $reportDesc, 0, 2);

                                break;
                            case 3:
                                // its a single line question module

                                if ($rQuestionvalue[$j]->answer_value == '') {
                                    $reportDesc .= "EMPTY";
                                } else {

                                    $reportDesc .= $rQuestionvalue[$j]->answer_value;
                                }

                                if ($rQuestionvalue[$j]->questionvalue_required == 0) {
                                    $reportDesc .= " (optional)";
                                }

                                $pdf->Cell(0, 6, $reportDesc, 0, 2);

                                break;
                            case 4:
                                // its a multi line question module

                                $reportDesc = "A-ID #";
                                $reportDesc .= $rQuestionvalue[$j]->questionvalue_id;
                                $reportDesc .= ": ";
                                $reportDesc .= $rQuestionvalue[$j]->answer_value;

                                if ($rQuestionvalue[$j]->questionvalue_required == 0) {
                                    $reportDesc .= " (optional)";
                                }

                                $pdf->MultiCell(0, 5, $reportDesc);

                                break;
                            case 5:
                                // its a upload question module



                                $filelist = getFilelist($rQuestionvalue[$j]->answer_value);
                                for ($m = 0; $m < count($filelist['dsname']); $m++) {
                                    $reportDesc = "A-ID #";
                                    $reportDesc .= $rQuestionvalue[$j]->questionvalue_id;
                                    $reportDesc .= ": ";
                                    $reportDesc .= "https://" . $website . "upload/" . $filelist['dsname'][$m];
                                    $pdf->Cell(0, 6, $reportDesc, 0, 2);
                                }



                                break;
                            default:
                                $pdf->Cell(0, 6, "smslwwoww ", 0, 2);
                        }
                    }
                }
                $pdf->Ln(5);
            }

            $pdf->Ln(10);
        }

        // Output the PDF
        $pdf->Output();
    } elseif (isset($_POST['create_report'])) {

        // The user-id is not in database, end with error.
        $reporterror = 'This USER-ID does not exist.';
    }



    include $templateDir . 'report.tpl.php';
}
?>