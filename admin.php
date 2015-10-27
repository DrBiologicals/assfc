<?php

require_once 'session.php';
require_once 'localsettings.php';
//cehck if usergroup can see this page
$mode = $_GET['mode'];
if ($globalusergroup == '1') {

    if ($_GET['mode'] == 'module') {
        require_once 'admin.module.php';
    } elseif ($_GET['mode'] == 'section') {
        require_once 'admin.section.php';
    } elseif ($_GET['mode'] == 'subsection') {
        require_once 'admin.subsection.php';
    } elseif ($_GET['mode'] == 'question') {
        require_once 'admin.question.php';
    } //End of module process
    if ($mode == 'user') {
        session_start();
        $searchmode = True;
        $searching = FALSE;
        //connecting to database
        $DBH = new PDO(
                        "mysql:host=$dbHost;dbname=$dbName",
                        $dbUser,
                        $dbPasswd,
                        array(
                            PDO::ATTR_ERRMODE => $errormode,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));


        $STH = $DBH->prepare("SELECT * FROM " . $dbPrefix . "user");

        $STH->execute();

        $STH->setFetchMode(PDO::FETCH_OBJ);

        $i = 0;
        $userdata = null;
//        $returndata = null;
        while ($row = $STH->fetch()) {
            $userdata[$i] = $row;
            $i++;
        }
        $returnvalue = 0;
        //Basic search container
        if (isset($_POST['search'])) {
            $nextlot = 1;
            $searching = TRUE;
            $returndata = NULL;

            $searchdata = $_POST['input'];
            $searchid = $_POST['userid'];
            $searchemail = $_POST['useremail'];
            $searchcompanyname = $_POST['usercompanyname'];
            $searchlastname = $_POST['userlastname'];
            $searchfirstname = $_POST['userfirstname'];
            $searchstreetaddress = $_POST['userstreetaddress'];
            $searchcity = $_POST['usercity'];

            if ($searchdata == '' && $searchid == '' && $searchemail == '' && $searchcompanyname == '' &&
                    $searchlastname == '' && $searchfirstname == '' && $searchstreetaddress == '' && $searchcity == ''
                    || $searchdata == '*') {
                for ($j = 0; $j < $i; $j++) {
                    $returndata[$returnvalue] = $j;
                    $returnvalue++;
                }
            } else if ($searchdata != '') {
                for ($j = 0; $j < $i; $j++) {
                    if (strcmp($searchdata, $userdata[$j]->user_id) == 0 || stristr($userdata[$j]->user_id, $searchdata)) {
                        $returndata[$returnvalue] = $j;
                        $returnvalue++;
                    }
                }
                for ($j = 0; $j < $i; $j++) {
                    if (strcasecmp($searchdata, $userdata[$j]->user_email) == 0 || stristr($userdata[$j]->user_email, $searchdata)) {
                        $returndata[$returnvalue] = $j;
                        $returnvalue++;
                    }
                }
                for ($j = 0; $j < $i; $j++) {
                    if (strcasecmp($searchdata, $userdata[$j]->user_companyname) == 0 || stristr($userdata[$j]->user_companyname, $searchdata)) {
                        $returndata[$returnvalue] = $j;
                        $returnvalue++;
                    }
                }
                for ($j = 0; $j < $i; $j++) {
                    if (strcasecmp($searchdata, $userdata[$j]->user_lastname) == 0 || stristr($userdata[$j]->user_lastname, $searchdata)) {
                        $returndata[$returnvalue] = $j;
                        $returnvalue++;
                    }
                }
                for ($j = 0; $j < $i; $j++) {
                    if (strcasecmp($searchdata, $userdata[$j]->user_firstname) == 0 || stristr($userdata[$j]->user_firstname, $searchdata)) {
                        $returndata[$returnvalue] = $j;
                        $returnvalue++;
                    }
                }
                for ($j = 0; $j < $i; $j++) {
                    if (strcasecmp($searchdata, $userdata[$j]->user_street) == 0 || stristr($userdata[$j]->user_street, $searchdata)) {
                        $returndata[$returnvalue] = $j;
                        $returnvalue++;
                    }
                }
                for ($j = 0; $j < $i; $j++) {
                    if (strcasecmp($searchdata, $userdata[$j]->user_city) == 0 || stristr($userdata[$j]->user_city, $searchdata)) {
                        $returndata[$returnvalue] = $j;
                        $returnvalue++;
                    }
                }
            } else {
                $numsearchfields = null;
                for ($num_full = 0; $num_full < 7; $num_full++) {
                    $numsearchfields[$num_full] = 0;
                }
                if ($searchid != '') {
                    $numsearchfields[0] = 1;
                    for ($j = 0; $j < $i; $j++) {
                        if (strcmp($searchid, $userdata[$j]->user_id) == 0 || stristr($userdata[$j]->user_id, $searchid)) {
                            $returndata[$returnvalue] = $j;
                            $returnvalue++;
                        }
                    }
                }
                if ($searchemail != '') {
                    $numsearchfields[1] = 1;
                    for ($j = 0; $j < $i; $j++) {
                        if (strcasecmp($searchemail, $userdata[$j]->user_email) == 0 || stristr($userdata[$j]->user_email, $searchemail)) {
                            $returndata[$returnvalue] = $j;
                            $returnvalue++;
                        }
                    }
                }
                if ($searchcompanyname != '') {
                    $numsearchfields[2] = 1;
                    for ($j = 0; $j < $i; $j++) {
                        if (strcasecmp($searchcompanyname, $userdata[$j]->user_companyname) == 0 || stristr($userdata[$j]->user_companyname, $searchcompanyname)) {
                            $returndata[$returnvalue] = $j;
                            $returnvalue++;
                        }
                    }
                }
                if ($searchlastname != '') {
                    $numsearchfields[3] = 1;
                    for ($j = 0; $j < $i; $j++) {
                        if (strcasecmp($searchlastname, $userdata[$j]->user_lastname) == 0 || stristr($userdata[$j]->user_lastname, $searchlastname)) {
                            $returndata[$returnvalue] = $j;
                            $returnvalue++;
                        }
                    }
                }
                if ($searchfirstname != '') {
                    $numsearchfields[4] = 1;
                    for ($j = 0; $j < $i; $j++) {
                        if (strcasecmp($searchfirstname, $userdata[$j]->user_firstname) == 0 || stristr($userdata[$j]->user_firstname, $searchfirstname)) {
                            $returndata[$returnvalue] = $j;
                            $returnvalue++;
                        }
                    }
                }
                if ($searchstreetaddress != '') {
                    $numsearchfields[5] = 1;
                    for ($j = 0; $j < $i; $j++) {
                        if (strcasecmp($searchstreetaddress, $userdata[$j]->user_street) == 0 || stristr($userdata[$j]->user_street, $searchstreetaddress)) {
                            $returndata[$returnvalue] = $j;
                            $returnvalue++;
                        }
                    }
                }
                if ($searchcity != '') {
                    $numsearchfields[6] = 1;
                    for ($j = 0; $j < $i; $j++) {
                        if (strcasecmp($searchcity, $userdata[$j]->user_city) == 0 || stristr($userdata[$j]->user_city, $searchcity)) {
                            $returndata[$returnvalue] = $j;
                            $returnvalue++;
                        }
                    }
                }
//                for ($num_fields = 0; $num_fields < 7; $num_fields++) {
//                    if ($numsearchfields[$num_fields] == 1) {
//                        if ($numsearchfields[1] == 1 && $num_fields >= 1) {
//                            for ($j = 0; $j < $returnvalue; $j++) {
//                                if (strcasecmp($searchemail, $userdata[$j]->user_email) != 0 &&
//                                        stristr($userdata[$j]->user_email, $searchemail) == false) {
//                                    $returndata[$j] = null;
//                                }
//                            }
//                        }
//                        if ($numsearchfields[2] == 1 && $num_fields != 2) {
//                            for ($j = 0; $j < $returnvalue; $j++) {
//                                if (strcasecmp($searchcompanyname, $userdata[$j]->user_companyname) != 0 &&
//                                        stristr($userdata[$j]->user_companyname, $searchcompanyname) == false) {
//                                    $returndata[$j] = null;
//                                }
//                            }                            
//                        }
//                        if ($numsearchfields[3] == 1 && $num_fields != 3) {
//                            for ($j = 0; $j < $returnvalue; $j++) {
//                                if (strcasecmp($searchlastname, $userdata[$j]->user_lastname) != 0 &&
//                                        stristr($userdata[$j]->user_lastname, $searchlastname) == false) {
//                                    $returndata[$j] = null;
//                                }
//                            }                            
//                        }
//                        if ($numsearchfields[4] == 1 && $num_fields != 4) {
//                            for ($j = 0; $j < $returnvalue; $j++) {
//                                if (strcasecmp($searchfirstname, $userdata[$j]->user_firstname) != 0 &&
//                                        stristr($userdata[$j]->user_firstname, $searchfirstname) == false) {
//                                    $returndata[$j] = null;
//                                }
//                            }                            
//                        }
//                        if ($numsearchfields[5] == 1 && $num_fields != 5) {
//                            for ($j = 0; $j < $returnvalue; $j++) {
//                                if (strcasecmp($searchstreetaddress, $userdata[$j]->user_street) != 0 &&
//                                        stristr($userdata[$j]->user_street, $searchstreetaddress) == false) {
//                                    $returndata[$j] = null;
//                                }
//                            }                            
//                        }
//                        if ($numsearchfields[6] == 1 && $num_fields != 6) {
//                            for ($j = 0; $j < $returnvalue; $j++) {
//                                if (strcasecmp($searchcity, $userdata[$j]->user_city) != 0 &&
//                                        stristr($userdata[$j]->user_city, $searchcity) == false) {
//                                    $returndata[$j] = null;
//                                }
//                            }                            
//                        }
//                    }
//                }
            }
        }
//        Removes data which is already in the array
        for ($j = 0; $j < $returnvalue; $j++) {
            for ($k = $j + 1; $k < $returnvalue; $k++) {
                if ($returndata[$j] == $returndata[$k]) {
                    $returndata[$k] = null;
                }
            }
        }
        if (isset($_POST['prev30'])) {
            $searching = TRUE;
            $returndata = $_SESSION['returndata'];
            $returnvalue = $_SESSION['returnvalue'];
            $nextlot = $_SESSION['nextlot'] - 1;
        }
        if (isset($_POST['next30'])) {
            $searching = TRUE;
            $returndata = $_SESSION['returndata'];
            $returnvalue = $_SESSION['returnvalue'];
            $nextlot = $_SESSION['nextlot'] + 1;
        }
        for ($j = 0; $j < $i; $j++) {
            if (isset($_POST['user' . $userdata[$j]->user_id])) {
                $editvalue = 0;
                $searchmode = FALSE;
                $editdata[$editvalue] = $j;
                $editvalue++;
            }
        }
        if (isset($_POST['edit'])) {
            $searchmode = FALSE;
            $editdata = NULL;
            $editvalue = 0;
            for ($j = 0; $j < $i; $j++) {
                if (isset($_POST['checkbox' . $userdata[$j]->user_id])) {
                    $editdata[$editvalue] = $j;
                    $editvalue++;
                }
            }
        }
        $_SESSION['nextlot'] = $nextlot;
        $_SESSION['returndata'] = $returndata;
        $_SESSION['returnvalue'] = $returnvalue;

        if (isset($_POST['update'])) {
            $editvalue = $_SESSION['editvalue'];

            for ($l = 0; $l < $editvalue; $l++) {

                //add to database
                $update_id = $_POST['id' . $l];
                $update_company = $_POST['companyname' . $l];
                $update_email = $_POST['email' . $l];
                $update_first = $_POST['firstname' . $l];
                $update_last = $_POST['lastname' . $l];
                $update_streetnumber = $_POST['streetnumber' . $l];
                $update_street = $_POST['street' . $l];
                $update_telephone = $_POST['telephone' . $l];
                $update_postcode = $_POST['postcode' . $l];
                $update_city = $_POST['city' . $l];

                $index = 0;
                for ($k = 0; $k < strlen($userdata); $k++) {
                    if ($userdata[$k]->user_id == $update_id) {
                        $index = $k;
                    }
                }
                //creating has for emailkey
                if ($update_email != $userdata[$index]->user_email) {
                    $emailkey = md5(uniqid());
                } else {
                    $emailkey = $userdata[$index]->user_emailkey;
                }
                $STH = $DBH->prepare("
                   UPDATE " . $dbPrefix . "user 
                   SET user_companyname = ?, user_email = ?, user_emailkey = ?, user_firstname = ?,
                   user_lastname = ?, user_street = ?, user_streetnumber = ?, user_city = ?,
                   user_postcode = ?, user_telephone = ? WHERE user_id = ?"
                );

                $STH->bindParam(1, $update_company);
                $STH->bindParam(2, $update_email);
                $STH->bindParam(3, $emailkey);
                $STH->bindParam(4, $update_first);
                $STH->bindParam(5, $update_last);
                $STH->bindParam(6, $update_street);
                $STH->bindParam(7, $update_streetnumber);
                $STH->bindParam(8, $update_city);
                $STH->bindParam(9, $update_postcode);
                $STH->bindParam(10, $update_telephone);
                $STH->bindParam(11, $update_id);
                $STH->execute();
            }
        }
        $_SESSION['editvalue'] = $editvalue;
        //close db connection
        $DBH = null;
    }
    include $templateDir . 'admin.tpl.php';
}
?>
