<?php
$pageNavi = 'Account Information';
include 'header.php';
?>

<?php include 'navbar.php'; ?>
<div id="main">
    <?php if ($_GET['mode'] == 'overview') : ?>
        <p><h2>Overview</h2></p>
        <p><h4>Current account information click edit to update details</h4></p>
    <a href="account.php?mode=edit" id="button-account">Edit</a><br><br>      
    <div id="table-main">
        <div style="display: table-row"> 
            <div id="table-cell-main" >Company Name: </div>
            <div id="table-cell-main"><?php echo $userdata->user_companyname; ?></div>    
        </div> 
        <div style="display: table-row"> 
            <div id="table-cell-main" >Last Name: </div> 
            <div id="table-cell-main"><?php echo $userdata->user_lastname; ?></div>    
        </div> 
        <div style="display: table-row">
            <div id="table-cell-main">First Name: </div>                    
            <div id="table-cell-main"><?php echo $userdata->user_firstname; ?> </div>
        </div>
        <div style="display: table-row">
            <div id="table-cell-main">Email: </div>                    
            <div id="table-cell-main"><?php echo $userdata->user_email; ?> </div>
        </div>
        <div style="display: table-row">
            <div id="table-cell-main">Address: </div> 
            <div id="table-cell-main"><?php echo $userdata->user_streetnumber; ?>
                <?php echo $userdata->user_street; ?></div>
        </div>
        <div style="display: table-row">
            <div id="table-cell-main">City: </div>                    
            <div id="table-cell-main"><?php echo $userdata->user_city; ?> </div>
        </div>
        <div style="display: table-row">
            <div id="table-cell-main">Country: </div>                    
            <div id="table-cell-main"><?php echo $countrydata->country_name; ?> </div>
        </div>
        <div style="display: table-row">
            <div id="table-cell-main">Postcode: </div>                    
            <div id="table-cell-main"><?php echo $userdata->user_postcode; ?> </div>
        </div>
        <div style="display: table-row">
            <div id="table-cell-main">Telephone: </div>                    
            <div id="table-cell-main"><?php echo $userdata->user_telephone; ?> </div>
        </div>
    </div><br>
    <a href="account.php?mode=edit" id="button-account">Edit</a>
<?php elseif ($_GET['mode'] == 'edit') : ?>
    <div id="loginform">
        <form action="account.php?mode=edit" method="post">
            <p><h2>Edit Details</h2></p>
            <p><h4>Change and update your account details, when finished click submit</h4></p>
            <table  cellpadding="2" border="0">
                <tr>
                    <td valign="center" style="width: 150px;">
                        <span class="smalltext">Company Name:</span>
                    </td>
                    <td>
                        <input name="companyName" type="text" style="width: 316px" tabindex="1" value="<?php echo $userdata->user_companyname; ?>">
                        <br /><span class="error"><?php echo $regErrorCompany; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        <span class="smalltext">Firstname & Lastname:</span>
                    </td>
                    <td>
                        <input name="firstname" type="text" style="width: 147px" tabindex="2" value="<?php echo $userdata->user_firstname; ?>" >
                        <input name="lastname" type="text" style="width: 147px" tabindex="3" value="<?php echo $userdata->user_lastname; ?>">
                        <br /><span class="error"><?php echo $regErrorFirstname; ?></span><span class="error"><?php echo $regErrorLastname; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        <span class="smalltext">Number & Street:</span>
                    </td>
                    <td>
                        <input name="streetNr" type="text" style="width: 39px" tabindex="4" value="<?php echo $userdata->user_streetnumber; ?>">
                        <input name="street" type="text" style="width: 255px" tabindex="5" value="<?php echo $userdata->user_street; ?>">

                        <br /><span class="error"><?php echo $regErrorStreet; ?></span><span class="error"><?php echo $regErrorStreetNr; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        <span class="smalltext">Postcode & City:</span>
                    </td>
                    <td>
                        <input name="postcode" type="text" style="width: 54px" tabindex="6" value="<?php echo $userdata->user_postcode; ?>">
                        <input name="city" type="text" style="width: 240px" tabindex="7" value="<?php echo $userdata->user_city; ?>">
                        <br /><span class="error"><?php echo $regErrorPostcode; ?></span><span class="error"><?php echo $regErrorCity; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        <span class="smalltext">Country:</span>
                    </td>
                    <td>
                        <select name="country" size="1" style="width: 334px;" tabindex="8">
                            <optgroup label="Main Countries">
                                <?php for ($i = 0; $i < count($countries); $i++) : ?>
                                    <?php if ($countries[$i]->country_favorite == '1') : ?>
                                        <option value="<?php echo $countries[$i]->country_id; ?>" <?php
                            if ($_POST['country'] == $countries[$i]->country_id): echo 'selected';
                            elseif (!isset($_POST['country']) && strcmp($countries[$i]->country_id,$userdata->user_country) == 0) : echo 'selected';
                            endif;
                                        ?>><?php echo $countries[$i]->country_name; ?></option>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                            </optgroup>
                            <optgroup label="Other Countries">
                                <?php for ($i = 0; $i < count($countries); $i++) : ?>
                                    <?php if ($countries[$i]->country_favorite == '0') : ?>
                                        <option value="<?php echo $countries[$i]->country_id; ?>" 
                                        <?php
                                        if (isset($_POST['country']) && $_POST['country'] == $countries[$i]->country_id): echo 'selected';
                                        endif;
                                        ?>><?php echo $countries[$i]->country_name; ?></option>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                            </optgroup>
                        </select>
                        <br /><span class="error"><?php echo $regErrorCountry; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        <span class="smalltext">Telephone Number:</span>
                    </td>
                    <td>
                        <input name="telephone" type="text" style="width: 316px" tabindex="9" value="<?php echo $userdata->user_telephone; ?>">
                        <br /><span class="smalltext">0064123456789</span>
                        <br /><span class="error"><?php echo $regErrorTelephone; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td>
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        <span class="smalltext">Email:</span>
                    </td>
                    <td>
                        <input name="email1" type="text" style="width: 316px" tabindex="10" value="<?php echo $userdata->user_email; ?>">
                        <br /><span class="error"><?php echo $regErrorEmail1; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        <span class="smalltext">Email again:</span>
                    </td>
                    <td>
                        <input name="email2" type="text" style="width: 316px" tabindex="11" value="<?php echo $email2; ?>">
                        <br /><span class="error"><?php echo $regErrorEmail2; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td>
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        <span class="smalltext">Old Password:</span>
                    </td>
                    <td>
                        <input name="oldpassword" type="password" style="width: 316px" tabindex="12">
                        <br /><span class="error"><?php echo $regErrorOldpassword; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        <span class="smalltext">Password:</span>
                    </td>
                    <td>
                        <input name="password1" type="password" style="width: 316px" tabindex="13">
                        <br /><span class="error"><?php echo $regErrorPassword1; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        <span class="smalltext">Password again:</span>
                    </td>
                    <td>
                        <input name="password2" type="password" style="width: 316px" tabindex="14">
                        <br /><span class="error"><?php echo $regErrorPassword2; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        &nbsp;
                    </td>
                    <td>
                        <input tabindex="15" type="submit" value="Submit" name="edit">
                        <input tabindex="16" type="submit" value="Cancel" name="abort">
                    </td>    
                </tr>
            </table>
        </form>  
    </div>
<?php elseif ($_GET['mode'] == 'updated') : ?>
    <form action="account.php?mode=updated" method="post">
        <meta http-equiv="REFRESH" content="3;url=https://assfc.webserver.local/account.php?mode=overview">
        <p>Successfully updated details... redirecting to Account information in 3 seconds</p>
    </form>
<?php endif; ?>
</div>
<?php include 'footer.php'; ?>
