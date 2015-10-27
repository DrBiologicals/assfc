<?php
$pageNavi = 'Login & Register';
include 'header.php';
?>
<div id="main-nomenue">
    <?php if ($mode == 'login') : ?>
        <?php $pageNavi = 'Login'; ?>
        <?php if (!isset($_POST['login']) || isset($loginError)) : ?>
            <div id="loginform">
                <form action="login.php?mode=login" method="post" >
                    <table  cellpadding="4" border="0">
                        <tr>
                            <td valign="center" style="width: 120px;">
                                Username/Email:
                            </td>
                            <td>
                                <input name="loginname" type="text" style="width: 316px" tabindex="1" value="<?php echo $_POST['loginname']; ?>">
                                <br /><span class="smalltext"><a href="login.php?mode=register">Register</a></span>
                            </td>    
                        </tr>
                        <tr>
                            <td valign="center">
                                Password:
                            </td>
                            <td>
                                <input name="password" type="password" style="width: 316px" tabindex="2">
                                <br /><span class="smalltext"><a href="login.php?mode=sendpassword">I have forgotten my password</a></span>
                            </td>    
                        </tr>
                        <tr>
                            <td valign="center">
                                &nbsp;
                            </td>
                            <td>
                                <input name="autologin" type="checkbox" tabindex="3"> <span class="smalltext">Autologin on return</span>
                            </td>    
                        </tr>
                        <tr>
                            <td valign="center">
                                &nbsp;
                            </td>
                            <td>
                                <input value="Login" tabindex="4" type="submit" name="login">
                                <span class="error"><?php echo $loginError; ?></span>
                            </td>    
                        </tr>
                    </table>
                </form>
            </div>
        <?php elseif (isset($_COOKIE['userid']) && isset($_COOKIE['password'])) : ?>
            <div id="loginform">
                Sie sind bereits eingelogged.
            </div>
        <?php endif; ?>
    <?php elseif ($mode == 'register') : ?>
        <?php $pageNavi = 'Register'; ?>
        <?php if (isset($_POST['register']) && $regError == FALSE) : ?>

            <div id="loginform" style="text-align: center;">

                Thank you for registration. We have received your registration details. 
                Your account has been created but has to be approved, please check your e-mail for details.

            </div>


        <?php else : ?>
            <div id="loginform">
                <form action="login.php?mode=register" method="post">
                    <table  cellpadding="2" border="0">
                        <tr>
                            <td valign="center" style="width: 150px;">
                                <span class="smalltext">Company Name:</span>
                            </td>
                            <td>
                                <input name="companyName" type="text" style="width: 316px" tabindex="1" value="<?php echo $companyName; ?>">
                                <br /><span class="error"><?php echo $regErrorCompany; ?></span>
                            </td>    
                        </tr>
                        <tr>
                            <td valign="center">
                                <span class="smalltext">Firstname & Lastname:*</span>
                            </td>
                            <td>
                                <input name="firstname" type="text" style="width: 147px" tabindex="2" value="<?php echo $firstname; ?>" >
                                <input name="lastname" type="text" style="width: 147px" tabindex="3" value="<?php echo $lastname; ?>">
                                <br /><span class="error"><?php echo $regErrorFirstname; ?></span><span class="error"><?php echo $regErrorLastname; ?></span>
                            </td>    
                        </tr>
                        <tr>
                            <td valign="center">
                                <span class="smalltext">Number & Street:*</span>
                            </td>
                            <td>
                                <input name="streetNr" type="text" style="width: 39px" tabindex="4" value="<?php echo $streetNr; ?>">
                                <input name="street" type="text" style="width: 255px" tabindex="5" value="<?php echo $street; ?>">

                                <br /><span class="error"><?php echo $regErrorStreet; ?></span>
                            </td>    
                        </tr>
                        <tr>
                            <td valign="center">
                                <span class="smalltext">Postcode & City:*</span>
                            </td>
                            <td>
                                <input name="postcode" type="text" style="width: 54px" tabindex="6" value="<?php echo $postcode; ?>">
                                <input name="city" type="text" style="width: 240px" tabindex="7" value="<?php echo $city; ?>">
                                <br /><span class="error"><?php echo $regErrorCity; ?></span>
                            </td>    
                        </tr>
                        <tr>
                            <td valign="center">
                                <span class="smalltext">Country:*</span>
                            </td>
                            <td>
                                <select name="country" size="1" style="width: 334px;" tabindex="8">
                                    <optgroup label="Main Countries">
                                        <?php for ($i = 0; $i < count($countries); $i++) : ?>
                                            <?php if ($countries[$i]->country_favorite == '1') : ?>
                                                <option value="<?php echo $countries[$i]->country_id; ?>" <?php
                                if ($_POST['country'] == $countries[$i]->country_id): echo 'selected';
                                elseif (!isset($_POST['country']) && $countries[$i]->country_id == '139') : echo 'selected';
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
                                <span class="smalltext">Telephone Number:*</span>
                            </td>
                            <td>
                                <input name="telephone" type="text" style="width: 316px" tabindex="9" value="<?php echo $telephone; ?>">
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
                                <span class="smalltext">Email:*</span>
                            </td>
                            <td>
                                <input name="email1" type="text" style="width: 316px" tabindex="10" value="<?php echo $email1; ?>">
                                <br /><span class="error"><?php echo $regErrorEmail1; ?></span>
                            </td>    
                        </tr>
                        <tr>
                            <td valign="center">
                                <span class="smalltext">Email again:*</span>
                            </td>
                            <td>
                                <input name="email2" type="text" style="width: 316px" tabindex="11" value="<?php echo $email2; ?>">
                                <br /><span class="error"><?php echo $regErrorEmail2; ?></span>
                            </td>    
                        </tr>
                        <tr>
                            <td valign="center">
                                <span class="smalltext">Password:*</span>
                            </td>
                            <td>
                                <input name="password1" type="password" style="width: 316px" tabindex="12">
                                <br /><span class="error"><?php echo $regErrorPassword1; ?></span>
                            </td>    
                        </tr>
                        <tr>
                            <td valign="center">
                                <span class="smalltext">Password again:*</span>
                            </td>
                            <td>
                                <input name="password2" type="password" style="width: 316px" tabindex="13">
                                <br /><span class="error"><?php echo $regErrorPassword2; ?></span>
                            </td>    
                        </tr>
                        <?php if ($regCaptchaCorrect != TRUE) : ?>
                            <tr>                          
                                <td valign="top">
                                    &nbsp;
                                </td>
                                <td>
                                    <script type="text/javascript">
                                        var RecaptchaOptions = {
                                            theme : 'custom',
                                            custom_theme_widget: 'recaptcha_widget',
                                            tabindex: 14
                                        };
                                    </script>
                                    <div id="recaptcha_widget" class="recaptcha-widget">
                                        <br/>
                                        <div id="captcha-image-box"><div id="recaptcha_image" class="recaptcha-image"></div></div>
                                        <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>

                                        <span class="recaptcha_only_if_image">Enter the words above:*</span>
                                        <span class="recaptcha_only_if_audio">Enter the numbers you hear:*</span>

                                        <br /><input type="text" id="recaptcha_response_field" style="width: 316px; margin-top: 4px;" name="recaptcha_response_field" />
                                        <div class="recaptcha-buttons">
                                            <a id="recaptcha_reload_btn" href="javascript:Recaptcha.reload()" title="Get a new Captcha"><span>New Captcha</span></a>
                                            <a id="recaptcha_switch_audio_btn" class="recaptcha_only_if_image" href="javascript:Recaptcha.switch_type('audio')" title="Get an audio Captcha"><span>Audio Captcha</span></a>
                                            <a id="recaptcha_switch_img_btn" class="recaptcha_only_if_audio" href="javascript:Recaptcha.switch_type('image')" title="Get an image Captcha"><span>Image CAPTCHA</span></a>
                                            <!--<a id="recaptcha_whatsthis_btn" href="javascript:Recaptcha.showhelp()"><span>Help</span></a>-->
                                        </div>
                                    </div>

                                    <script type="text/javascript"
                                            src="http://www.google.com/recaptcha/api/challenge?k=6LcfC9cSAAAAAGCMjC105Pcl2snRXQL68qiFoeLU ">
                                    </script>
                                    <noscript>
                                    <iframe src="http://www.google.com/recaptcha/api/noscript?k=6LcfC9cSAAAAAGCMjC105Pcl2snRXQL68qiFoeLU "
                                            height="300" width="500" frameborder="0"></iframe><br>
                                    <textarea name="recaptcha_challenge_field" rows="3" cols="40">
                                    </textarea>
                                    <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
                                    </noscript>
                                    <span class="error"><?php echo $regErrorCaptcha; ?></span>
                                </td>    
                            </tr>
                        <?php else : ?>
                            <input type="hidden" name="captcha" value="true"/>
                        <?php endif; ?>
                        <tr>
                            <td>
                                &nbsp;
                            </td>
                            <td valign="top">
                                <input name="terms" type="checkbox" tabindex="15" <?php
                if ($_POST['terms'] == on) : echo 'checked="checked"';
                endif;
                        ?>><span class="smalltext"> I agree the HERA <a href="">Terms of Service</a> and <a href="">Privacy Policy</a></span>
                                <br/><span class="error"><?php echo $regErrorTerms; ?></span>
                            </td>    
                        </tr>
                        <tr>
                            <td valign="center">
                                &nbsp;
                            </td>
                            <td>
                                <p>* required field</p>
                                <input value="Register" tabindex="16" type="submit" name="register">
                            </td>    
                        </tr>
                    </table>
                </form>
            </div>
        <?php endif; ?>
    <?php elseif ($mode == 'sendpassword') : ?>
        <?php if (isset($_GET['form'])) : ?>
            <div id="loginform">
                <div style="text-align: center;">
                    <h2>Forgotten Password</h2>
                    If you have forgotten your password for your login, please enter your loginname or the email address of the registration.
                    The system will send a verification email to this email address and if you click on the link, you can enter a new password for your account.<br />
                    Please note: The link in the Email is only valid for 24 hours.<br/></div>
                <br><br>
                <?php if ($_GET['form'] == 'nomail') : ?>
                    The entered email address is not the system. Please klick <a href="login.php?mode=sendpassword">here</a> to enter it again.
                <?php elseif ($_GET['form'] == 'mailsent') : ?>
                    A email was sent to your email address. Please click on the link in the email to enter a new password.
                <?php endif; ?>
            </div>
        <?php else : ?>
            <div id="loginform">
                <div style="text-align: center;">
                    <h2>Forgotten Password</h2>
                    If you have forgotten your password for your login, please enter your loginname or the email address of the registration.
                    The system will send a verification email to this email address and if you click on the link, you can enter a new password for your account.<br />
                    Please note: The link in the Email is only valid for 24 hours.<br/></div>
                <form action="login.php?mode=sendpassword" method="post" style="margin-top: 20px;" >
                    <table  cellpadding="4" border="0">
                        <tr>
                            <td valign="center" style="width: 120px;">
                                Username / Email:
                            </td>
                            <td>
                                <input name="loginname" type="text" style="width: 316px" tabindex="1">
                            </td>    
                        </tr>
                        <tr>
                            <td valign="center">
                                &nbsp;
                            </td>
                            <td>
                                <input value="Send Email" tabindex="4" type="submit" name="sendpasswd">
                                <span class="error"><?php echo $loginError; ?></span>
                            </td>    
                        </tr>
                    </table>
                </form>
            </div>
        <?php endif; ?>
    <?php elseif ($mode == 'confirmmail') : ?>
        <?php $pageNavi = 'Email verification'; ?>
        <?php if ($confirmError == FALSE) : ?>

            <div id="loginform" style="text-align: center;">

                Thank you for your verification. We have received your registration details. 
                Your account is now activated and can be used.

            </div>
        <?php else : ?>
            <div id="loginform" style="text-align: center;">

                There is a error.

            </div>
        <?php endif; ?>
    <?php else : ?>
        fehler
    <?php endif; ?>


</div>
<?php
include 'footer.php';
?>
