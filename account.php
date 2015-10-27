<?php

require_once 'session.php';
require_once 'localsettings.php';
require_once 'inc/passwordhash.php';

$mode = $_GET['mode'];
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


    $STH = $DBH->prepare("SELECT * FROM " . $dbPrefix . "user WHERE user_id = ?");

    $STH->bindParam(1, $globalid);

    $STH->execute();

    $STH->setFetchMode(PDO::FETCH_OBJ);

    $userdata = $STH->fetch();

    $DBH = null;
} catch (PDOException $e) {
    echo $e->getMessage();
}
if ($mode == 'overview') {
    $DBH = new PDO(
                    "mysql:host=$dbHost;dbname=$dbName",
                    $dbUser,
                    $dbPasswd,
                    array(
                        PDO::ATTR_ERRMODE => $errormode,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));

    $STH = $DBH->prepare("SELECT country_name FROM " . $dbPrefix . "country WHERE country_id = ?");

    $STH->bindParam(1, $userdata->user_country);

    $STH->execute();

    $STH->setFetchMode(PDO::FETCH_OBJ);

    $countrydata = $STH->fetch();

    $DBH = null;
} elseif ($mode == 'edit') {
    if (isset($_POST['abort'])) {
        if ($sslonly == TRUE) {
            $httpprefix = 'https://';
        } else {
            $httpprefix = 'http://';
        }
        $nextpage = $httpprefix . $website . 'account.php?mode=overview';
        header("Location: " . $nextpage);
        exit;
    }
    $DBH = new PDO(
                    "mysql:host=$dbHost;dbname=$dbName",
                    $dbUser,
                    $dbPasswd,
                    array(
                        PDO::ATTR_ERRMODE => $errormode,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));


    $STH = $DBH->prepare("SELECT * FROM " . $dbPrefix . "user WHERE user_id = ?");

    $STH->bindParam(1, $globalid);

    $STH->execute();

    $STH->setFetchMode(PDO::FETCH_OBJ);

    $userdata = $STH->fetch();

    $STH = $DBH->prepare("SELECT country_name FROM " . $dbPrefix . "country WHERE country_id = ?");

    $STH->bindParam(1, $userdata->user_country);

    $STH->execute();

    $STH->setFetchMode(PDO::FETCH_OBJ);

    $countrydata = $STH->fetch();

    $STH = $DBH->prepare("
                SELECT country_id, country_favorite, country_name, country_telephonecode 
                FROM " . $dbPrefix . "country");
    $STH->execute();
    $STH->setFetchMode(PDO::FETCH_OBJ);

    while ($row = $STH->fetch()) {
        $countries[$row->country_id] = $row;
    }
    $DBH = null;

    if (isset($_POST['edit'])) {
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
        $oldpassword = $_POST['oldpassword'];
        $password1 = $_POST['password1'];
        $password2 = $_POST['password2'];
    }
    //checking companyName
    if (strlen($companyName) > 255) {
        $regError = TRUE;
        $regErrorCompany = 'Only 255 characters allowed as company name. You have used ' . strlen($companyName) . '.';
    }
    //checking lastname
    if (strlen($lastname) > 255) {
        $regError = TRUE;
        $regErrorLastname = 'Only 255 characters allowed as lastname. You have used ' . strlen($lastname) . '.';
    }
    //checking firstname
    if (strlen($firstname) > 255) {
        $regError = TRUE;
        $regErrorFirstname = 'Only 255 characters allowed as firstname. You have used ' . strlen($firstname) . '.';
    }
    //checking street
    if (strlen($street) > 255) {
        $regError = TRUE;
        $regErrorStreet = 'Only 255 characters allowed as street. You have used ' . strlen($street) . '.';
    }
    //checking streetNr
    if (strlen($streetNr) > 30) {
        $regError = TRUE;
        $regErrorStreetNr = 'Only 30 characters allowed as street nr. You have used ' . strlen($streetNr) . '.';
    }
    //checking city
    if (strlen($city) > 255) {
        $regError = TRUE;
        $regErrorCity = 'Only 255 characters allowed as city. You have used ' . strlen($streetNr) . '.';
    }
    //checking postcode
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
                SELECT user_postcode
                FROM " . $dbPrefix . "user 
                WHERE user_postcode = ?"
        );
        $STH->bindParam(1, $postcode);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $row = $STH->fetch();
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    if ($row->user_postcode != $postcode) {
        if (!is_numeric($postcode)) {
            $regError = TRUE;
            $regErrorPostcode = 'Only numbers in Postcode are possible.';
        } elseif (strlen($postcode) > 15) {
            $regError = TRUE;
            $regErrorPostcode = 'Only 15 characters allowed as postcode. You have used ' . strlen($postcode) . '.';
        }
    }
    //checking country
    if ($countries[$country]->country_id != $country) {
        echo $countries[$country]->country_id;
        $regError = TRUE;
        $regErrorCountry = 'Please choose a Country.';
    }
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
                SELECT user_telephone
                FROM " . $dbPrefix . "user 
                WHERE user_telephone = ?"
        );
        $STH->bindParam(1, $telephone);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $row = $STH->fetch();
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    if ($row->user_telephone != $telephone) {
        if (!is_numeric($telephone)) {
            $regError = TRUE;
            $regErrorTelephone = 'Only numbers in Telephone are possible.';
        } elseif (strlen($telephone) > 255) {
            $regError = TRUE;
            $regErrorTelephone = 'Only 255 characters allowed as postcode. You have used ' . strlen($telephone) . '.';
        }
    }
    $regCreateEmail = FALSE;
    //checking email
    if ($email2 != '' && $email1 != '') {
        $regCreateEmail = TRUE;
        if (!filter_var($email1, FILTER_VALIDATE_EMAIL)) {
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
//    if ($email2 != '' && $email1 != '') {
//        if ($row->user_email == $email1) {
//            $regError = TRUE;
//            $regErrorEmail1 = 'Email address already in use.';
//            $email2 = '';
//        }
//    }
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
                SELECT user_password
                FROM " . $dbPrefix . "user 
                WHERE user_password = ?"
        );
        $STH->bindParam(1, $oldpassword);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $row = $STH->fetch();
        $DBH = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    //create hash and compare hash with password
    $pwdHasher = new PasswordHash(8, FALSE);

    $checked = $pwdHasher->CheckPassword($_POST['oldpassword'], $row->user_password);
    if ($oldpassword != '') {
        if (!$checked && $_POST['oldpassword'] == '') {
            $regError = TRUE;
            $regErrorOldpassword = 'Password does not match';
        }
    }

    //checking password
    if ($password1 != '' && $password2 != '') {
        if (strlen($password1) < 8) {
            $regError = TRUE;
            $regErrorPassword1 = 'The password must be at least 8 characters long.';
        } elseif ($password1 != $password2) {
            $regError = TRUE;
            $regErrorPassword2 = 'The passwords do not match.';
        }
    }

    //everything went right - write data to database
    if ($regError == FALSE && isset($_POST['edit']) && $mode == 'edit') {
        //creating hash for password
        $pwdHasher = new PasswordHash(8, FALSE);
        $hash = $pwdHasher->HashPassword($password1);

        //creating has for emailkey
        if ($regCreateEmail == TRUE) {
            $emailkey = md5(uniqid());
        } else {
            $email1 = $userdata->user_email;
            $emailkey = $userdata->user_emailkey;
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
            if ($password1 == '' || $password2 == '') {
                $STH = $DBH->prepare("
                   UPDATE " . $dbPrefix . "user 
                   SET user_companyname = ?, user_email = ?, user_emailkey = ?, user_firstname = ?,
                   user_lastname = ?, user_street = ?, user_streetnumber = ?, user_city = ?,
                   user_postcode = ?, user_telephone = ?, user_country = ? WHERE user_id = ?"
                );

                $STH->bindParam(1, $companyName);
                $STH->bindParam(2, $email1);
                $STH->bindParam(3, $emailkey);
                $STH->bindParam(4, $firstname);
                $STH->bindParam(5, $lastname);
                $STH->bindParam(6, $street);
                $STH->bindParam(7, $streetNr);
                $STH->bindParam(8, $city);
                $STH->bindParam(9, $postcode);
                $STH->bindParam(10, $telephone);
                $STH->bindParam(11, $country);
                $STH->bindParam(12, $globalid);
                $STH->execute();
            } else {
                $STH = $DBH->prepare("
                   UPDATE " . $dbPrefix . "user 
                   SET user_companyname = ?, user_email = ?, user_emailkey = ?, user_firstname = ?,
                   user_lastname = ?, user_street = ?, user_streetnumber = ?, user_city = ?,
                   user_postcode = ?, user_password = ?, user_telephone = ?, user_country = ? WHERE user_id = ?"
                );

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
                $STH->bindParam(13, $globalid);
                $STH->execute();

                $lifetime = 0;
                setcookie('userid', $hash, $lifetime);
                $_COOKIE['userid'] = $hash; // fake-cookie setzen
                setcookie('password', $hash, $lifetime);
                $_COOKIE['password'] = $hash; // fake-cookie setzen
                
            }
            //close db connection
            $DBH = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        if ($sslonly == TRUE) {
            $httpprefix = 'https://';
        } else {
            $httpprefix = 'http://';
        }
        $nextpage = $httpprefix . $website . 'account.php?mode=updated';
        header("Location: " . $nextpage);
    }
} else if ($mode == 'updated') {
    
} else {

    echo 'error!';
}

require $templateDir . 'account.tpl.php';
?>
