<?php

require_once 'localsettings.php';
require_once 'inc/passwordhash.php';
$mode = $_GET['mode'];

if ($mode == 'login') {

    if (isset($_POST['login'])) {
        //establish database connection and check login
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
                SELECT user_id, user_password, user_usergroupid 
                FROM " . $dbPrefix . "user 
                WHERE user_email = ?"
            );

            //bind variables
            $STH->bindParam(1, $_POST['loginname']);

            //execute statement
            $STH->execute();

            //set fetch mode
            $STH->setFetchMode(PDO::FETCH_OBJ);

            //fetch data
            $row = $STH->fetch();

            //close db connection
            $DBH = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        //create hash and compare hash with password
        $pwdHasher = new PasswordHash(8, FALSE);

        $checked = $pwdHasher->CheckPassword($_POST['password'], $row->user_password);
        if ($checked && $_POST['password'] != '' && $row->user_usergroupid == '4') {
            $loginError = 'Please check your Email for verification.';
        } elseif ($checked && $_POST['password'] != '') {
            if ($_POST['autologin'] == on) {
                $lifetime = strtotime("+1 month");
            } else {
                $lifetime = 0;
            }
            setcookie('userid', $row->user_id, $lifetime);
            setcookie('password', $row->user_password, $lifetime);
            $_COOKIE['userid'] = $row->user_id; // fake-cookie setzen
            $_COOKIE['password'] = $row->user_password; // fake-cookie setzen
            //redirect to overviewpage
            if ($sslonly == TRUE) {
                $httpprefix = 'https://';
            } else {
                $httpprefix = 'http://';
            }
            $nextpage = $httpprefix . $website . 'index.php';
            header("Location: " . $nextpage);
            exit;
        } else {
            $loginError = 'Username or Password incorrect.';
        }
    }
} elseif ($mode == 'logout') {

    setcookie('userid', '', strtotime('-1 day'));
    setcookie('password', '', strtotime('-1 day'));
    unset($_COOKIE['userid']);
    unset($_COOKIE['password']);
    //redirect to loginpage
    if ($sslonly == TRUE) {
        $httpprefix = 'https://';
    } else {
        $httpprefix = 'http://';
    }
    $nextpage = $httpprefix . $website . 'login.php?mode=login';
    header("Location: " . $nextpage);
    exit;
} elseif ($mode == 'sendpassword') {

    //user pushes send button

    if (isset($_POST['sendpasswd'])) {
        try {
            include_once 'localsettings.php';

            //check if email is in DB
            $DBH = new PDO(
                            "mysql:host=$dbHost;dbname=$dbName",
                            $dbUser,
                            $dbPasswd,
                            array(
                                PDO::ATTR_ERRMODE => $errormode,
                                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                    ));
            $STH = $DBH->prepare("
                SELECT user_id 
                FROM " . $dbPrefix . "user
                WHERE user_email = ?");
            $STH->bindParam(1, $_POST['loginname']);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_OBJ);
            $userid = null;
            $userid = $STH->fetch();
            $DBH = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        // check if $userid is empty or not
        // if empty --> email address does not exist
        // if not empty --> userid is the userid if the entered email address
        if ($userid != null) {

            $forgottenpasskey = md5(uniqid());
            try {
                // write uniqid to DB
                $DBH = new PDO(
                                "mysql:host=$dbHost;dbname=$dbName",
                                $dbUser,
                                $dbPasswd,
                                array(
                                    PDO::ATTR_ERRMODE => $errormode,
                                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                        ));
                $STH = $DBH->prepare("UPDATE " . $dbPrefix . "user 
                SET user_forgottenpasskey = ?, user_forgottenpassdate = NOW()
                WHERE user_id = ?");
                //bind variables
                $STH->bindParam(1, $forgottenpasskey);
                $STH->bindParam(2, $userid->user_id);
                $STH->execute();
                $DBH = null;
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            
            //Send Email for Verification
            $subject = 'HERA Email Verification';
            $headers = "From: " . $adminemail . "\r\n";
            $headers .= "Reply-To: " . $adminemail . "\r\n";
            $headers .= "CC: $adminemail \r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $message = 'You are receiving this notification because you have (or someone pretending to be you has) requested a new password be sent for your account. If you did not request this notification then please ignore it, if you keep receiving it please contact the administrator.';
            $message .= 'To type in a new password, please click on this link: ';
            if ($sslonly == TRUE) {
                $httpprefix = 'https://';
            } else {
                $httpprefix = 'http://';
            }
            $message .= '<a href="' . $httpprefix . $website . 'login.php?mode=sendpassword&key=' . $forgottenpasskey . '">' . $httpprefix . $website . 'login.php?mode=sendpassword&key=' . $forgottenpasskey . '</a>';
            mail($_POST['loginname'], $subject, $message, $headers);
            
            
            // redirect back
            if ($sslonly == TRUE) {
                $httpprefix = 'https://';
            } else {
                $httpprefix = 'http://';
            }
            $nextpage = $httpprefix . $website . 'login.php?mode=sendpassword&form=mailsent';
            header("Location: " . $nextpage);
            exit;
        } else {
            // redirect back
            if ($sslonly == TRUE) {
                $httpprefix = 'https://';
            } else {
                $httpprefix = 'http://';
            }
            $nextpage = $httpprefix . $website . 'login.php?mode=sendpassword&form=nomail';
            header("Location: " . $nextpage);
            exit;
        }
    }

} elseif ($mode == 'register') {

    //getting countries
    try {
        include_once 'localsettings.php';
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
                SELECT country_id, country_favorite, country_name, country_telephonecode 
                FROM " . $dbPrefix . "country");
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);

        while ($row = $STH->fetch()) {
            $countries[$row->country_id] = $row;
        }
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    if (isset($_POST['register'])) {
        $regError = FALSE;
        //Creating local variables
        $companyName = $_POST['companyName'];
        $lastname = $_POST['lastname'];
        $firstname = $_POST['firstname'];
        $street = $_POST['street'];
        $streetNr = $_POST['streetNr'];
        $city = $_POST['city'];
        $postcode = $_POST['postcode'];
        $country = $_POST['country'];
        $telephone = $_POST['telephone'];
        $email1 = $_POST['email1'];
        $email2 = $_POST['email2'];
        $password1 = $_POST['password1'];
        $password2 = $_POST['password2'];



        //Checking the registration information
        
        // Delete the following line, when going live:
        $regCaptchaCorrect = TRUE;
        
        // Uncomment the following lines when going live:
        // checking reCapcha
//        if (isset($_POST['captcha'])) {
//            $regCaptchaCorrect = TRUE;
//        } else {
//            require_once('inc/recaptchalib.php');
//            $privatekey = "6LcfC9cSAAAAAMIaWuYGsnV8UVLKD8fn7ihP7Tif";
//            $resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
//            if (!$resp->is_valid) {
//                $regError = TRUE;
//                $regErrorCaptcha = 'The reCAPTCHA is not correct. Please try again.';
//            } else {
//                $regCaptchaCorrect = TRUE;
//            }
//        }
        
        
        //checking companyName
        if (strlen($companyName) > 255) {
            $regError = TRUE;
            $regErrorCompany = 'Only 255 characters allowed as company name. You have used ' . strlen($companyName) . '.';
        }
        //checking lastname
        if ($lastname == '') {
            $regError = TRUE;
            $regErrorLastname = 'Required field.';
        } elseif (strlen($lastname) > 255) {
            $regError = TRUE;
            $regErrorLastname = 'Only 255 characters allowed as lastname. You have used ' . strlen($lastname) . '.';
        }
        //checking firstname
        if ($firstname == '') {
            $regError = TRUE;
            $regErrorFirstname = 'Required field.';
        } elseif (strlen($firstname) > 255) {
            $regError = TRUE;
            $regErrorFirstname = 'Only 255 characters allowed as firstname. You have used ' . strlen($firstname) . '.';
        }
        //checking street
        if ($street == '') {
            $regError = TRUE;
            $regErrorStreet = 'Required field.';
        } elseif (strlen($street) > 255) {
            $regError = TRUE;
            $regErrorStreet = 'Only 255 characters allowed as street. You have used ' . strlen($street) . '.';
        }
        //checking streetNr
        if ($streetNr == '') {
            $regError = TRUE;
            $regErrorStreet = 'Required field.';
        } elseif (strlen($streetNr) > 30) {
            $regError = TRUE;
            $regErrorStreet = 'Only 30 characters allowed as street nr. You have used ' . strlen($streetNr) . '.';
        }
        //checking city
        if ($city == '') {
            $regError = TRUE;
            $regErrorCity = 'Required field.';
        } elseif (strlen($city) > 255) {
            $regError = TRUE;
            $regErrorCity = 'Only 255 characters allowed as city. You have used ' . strlen($streetNr) . '.';
        }
        //checking postcode
        if ($postcode == '') {
            $regError = TRUE;
            $regErrorCity = 'Required field.';
        } elseif (!is_numeric($postcode)) {
            $regError = TRUE;
            $regErrorCity = 'Only numbers in Postcode are possible.';
        } elseif (strlen($postcode) > 15) {
            $regError = TRUE;
            $regErrorCity = 'Only 15 characters allowed as postcode. You have used ' . strlen($postcode) . '.';
        }

        //checking country
        if ($countries[$country]->country_id != $country) {
            echo $countries[$country]->country_id;
            $regError = TRUE;
            $regErrorCountry = 'Please choose a Country.';
        }

        //check telephone
        if ($telephone == '') {
            $regError = TRUE;
            $regErrorTelephone = 'Required field.';
        } elseif (!is_numeric($telephone)) {
            $regError = TRUE;
            $regErrorTelephone = 'Only numbers in Telephone are possible.';
        } elseif (strlen($telephone) > 255) {
            $regError = TRUE;
            $regErrorTelephone = 'Only 255 characters allowed as postcode. You have used ' . strlen($telephone) . '.';
        }

        //checking email
        if ($email1 == '') {
            $regError = TRUE;
            $regErrorEmail1 = 'Required field.';
        } elseif (!filter_var($email1, FILTER_VALIDATE_EMAIL)) {
            $regError = TRUE;
            $regErrorEmail1 = 'This is not a valid email address.';
            $email2 = '';
        } elseif ($email1 != $email2) {
            $regError = TRUE;
            $regErrorEmail2 = 'The two email adresses do not match.';
            $email2 = '';
        } elseif (strlen($email1) > 255) {
            $regError = TRUE;
            $regErrorEmail1 = 'Only 255 characters allowed as email. You have used ' . strlen($streetNr) . '.';
            $email2 = '';
        }
        //check email in database
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
                SELECT user_email 
                FROM " . $dbPrefix . "user 
                WHERE user_email = ?"
            );
            $STH->bindParam(1, $email1);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_OBJ);
            $row = $STH->fetch();
            $DBH = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        if ($row->user_email == $email1 && $email1 != '') {
            $regError = TRUE;
            $regErrorEmail1 = 'Email address already in use. Please use the <span class="smalltext"><a href="login.php?mode=sendpassword">password reminder</a></span>.';
            $email2 = '';
        }

        //checking password
        if ($password1 == '') {
            $regError = TRUE;
            $regErrorPassword1 = 'Required field.';
        } elseif (strlen($password1) < 8) {
            $regError = TRUE;
            $regErrorPassword1 = 'The password must be at least 8 characters long.';
        } elseif ($password1 != $password2) {
            $regError = TRUE;
            $regErrorPassword2 = 'The passwords do not match.';
        }
        //check terms
        if ($_POST['terms'] != on) {
            $regError = TRUE;
            $regErrorTerms = 'You have to accept the terms & conditions.';
        }

        //everything went right - write data to database
        if ($regError == FALSE && isset($_POST['register']) && $mode == 'register') {

            //creating hash for password
            $pwdHasher = new PasswordHash(8, FALSE);
            $hash = $pwdHasher->HashPassword($password1);

            //creating has for emailkey
            $emailkey = md5(uniqid());

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

                //prepare statement
                $STH = $DBH->prepare("
                    INSERT INTO " . $dbPrefix . "user 
                    (user_companyname, user_email, user_emailkey, user_firstname, user_lastname, user_street, user_streetnumber, user_city, user_postcode, user_password, user_usergroupid, user_telephone, user_country) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 4, ?, ?)");

                //bind variables
                $STH->bindParam(1, $companyName);
                $STH->bindParam(2, $email1);
                $STH->bindParam(3, $emailkey);
                $STH->bindParam(4, $firstname);
                $STH->bindParam(5, $lastname);
                $STH->bindParam(6, $street);
                $STH->bindParam(7, $streetNr);
                $STH->bindParam(8, $city);
                $STH->bindParam(9, $postcode);
                $STH->bindParam(10, $hash);
                $STH->bindParam(11, $telephone);
                $STH->bindParam(12, $country);

                //execute statement
                $STH->execute();

                //close db connection
                $DBH = null;
            } catch (PDOException $e) {
                echo $e->getMessage();
            }

            //Send Email for Verification
            $subject = 'HERA Email Verification';
            $headers = "From: " . $adminemail . "\r\n";
            $headers .= "Reply-To: " . $adminemail . "\r\n";
            $headers .= "CC: $adminemail \r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $message = 'Please verify your email address by clicking this link:';
            if ($sslonly == TRUE) {
                $httpprefix = 'https://';
            } else {
                $httpprefix = 'http://';
            }
            $message .= '<a href="' . $httpprefix . $website . 'login.php?mode=confirmmail&key=' . $emailkey . '">' . $httpprefix . $websitename . 'login.php?mode=confirmmail&key=' . $emailkey . '</a>';
            mail($email1, $subject, $message, $headers);
        }
    }
} elseif ($mode == 'confirmmail') {
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
                SELECT user_id 
                FROM " . $dbPrefix . "user 
                WHERE user_emailkey = ?"
        );

        //bind variables
        $STH->bindParam(1, $_GET['key']);

        //execute statement
        $STH->execute();

        //set fetch mode
        $STH->setFetchMode(PDO::FETCH_OBJ);

        //fetch data
        $row = $STH->fetch();

        if ($row->user_id != '') {
            //prepare statement
            $STH = $DBH->prepare("
                UPDATE " . $dbPrefix . "user 
                SET user_usergroupid = '3' WHERE user_id = ?"
            );

            //bind variables
            $STH->bindParam(1, $row->user_id);

            //execute statement
            $STH->execute();

            //close db connection
            $DBH = null;
            $confirmError = FALSE;
        } else {
            $confirmError = TRUE;
        }

        //close db connection
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    $output = 'There is a problem. Please contact the administrator.';
}

require $templateDir . 'login.tpl.php';
?>

