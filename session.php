<?php
require_once 'localsettings.php';
//check if coockie is set
if (isset($_COOKIE['userid']) && isset($_COOKIE['password'])) {
    //check if coockie-data is in database
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
                SELECT user_id, user_firstname, user_lastname, user_password, user_usergroupid, exclass_id
                FROM " . $dbPrefix . "user 
                WHERE user_id = ?"
        );

        //bind variables
        $STH->bindParam(1, $_COOKIE['userid']);

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
    if (isset($row->user_id) && $row->user_id == $_COOKIE['userid'] && $row->user_password == $_COOKIE['password']) {
        $login = TRUE;
        //set global user-datas
        $globalid = $row->user_id;
        $globalfirstname = $row->user_firstname;
        $globallastname = $row->user_lastname;
        $globalusergroup = $row->user_usergroupid;
        $globalexclass = $row->exclass_id;
    } else {
        $login = FALSE;
    }
} else {
    $login = FALSE;
}
?>
