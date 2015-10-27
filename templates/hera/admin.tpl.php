<?php
$pageNavi = 'Administration Backend';
include 'header.php';
?>

<?php include 'navbar.php'; ?>

<script language="javascript" type="text/javascript">
    function showHide(shID) {
        if (document.getElementById(shID)) {
            if (document.getElementById(shID+'-show').style.display != 'none') {
                document.getElementById(shID+'-show').style.display = 'none';
                document.getElementById(shID).style.display = 'block';
            }
            else {
                document.getElementById(shID+'-show').style.display = 'inline';
                document.getElementById(shID).style.display = 'none';
            }
        }
    }
</script>
<div id="main">
    <?php if ($_GET['mode'] == 'main') : ?>
        <h2>Overview</h2>
        This Administration Backend is for Administration.
    <?php elseif ($_GET['mode'] == 'module') : ?>

        <?php require_once 'admin.module.tpl.php'; ?>

    <?php elseif ($_GET['mode'] == 'section') : ?>

        <?php require_once 'admin.section.tpl.php'; ?>

    <?php elseif ($_GET['mode'] == 'subsection') : ?>

        <?php require_once 'admin.subsection.tpl.php'; ?>

    <?php elseif ($_GET['mode'] == 'question') : ?>

        <?php require_once 'admin.question.tpl.php'; ?>

    <?php elseif ($_GET['mode'] == 'user') : ?>           
        <form action="admin.php?mode=user" method="post">
            <?php if ($searchmode == TRUE) : ?>
                <h2>User Information</h2>
                <input name="input" type="text" style="width: 316px" tabindex="1" >
                <input tabindex="2" type="submit" value="Search" name="search" ><br>
                <a href="#" style="position:relative;left:45%;top:20%;width:200px" id="advancedsearch-show" class="showLink" onclick="showHide('advancedsearch'); return false;">Advanced search</a>
                <div style="display: none" id="advancedsearch">      
                    <a href="#" style="position:relative;left:45%;top:20%;width:200px" id="advancedsearch-hide" class="hideLink" onclick="showHide('advancedsearch');return false;">Advanced search</a>
                    <br><br><div style="display: table">
                        <div style="display: table-row">
                            <div style="display: table-cell">User Id</div>
                            <div style="display: table-cell"><input type="text" name="userid"></div><br><br>
                            <div style="display: table-cell;padding-left: 20px;">Email Address</div>
                            <div style="display: table-cell"><input type="text" name="useremail"></div>
                        </div>
                        <div style="display: table-row">
                            <div style="display: table-cell">Company Name</div>
                            <div style="display: table-cell"><input type="text" name="usercompanyname"></div><br><br>
                        </div>
                        <div style="display: table-row">
                            <div style="display: table-cell">Last Name</div>
                            <div style="display: table-cell"><input type="text" name="userlastname"></div><br><br>
                            <div style="display: table-cell;padding-left: 20px;">First Name</div>
                            <div style="display: table-cell"><input type="text" name="userfirstname"></div>
                        </div>
                        <div style="display: table-row">
                            <div style="display: table-cell">Street Address</div>
                            <div style="display: table-cell"><input type="text" name="userstreetaddress"></div><br><br>
                            <div style="display: table-cell;padding-left: 20px;">City</div>
                            <div style="display: table-cell"><input type="text" name="usercity"></div>
                        </div>                       
                    </div>
                </div>
                <br><br>
                <?php if ($searching == TRUE) : ?>
                    <?php for ($j = ($nextlot - 1); $j < $nextlot; $j++) : ?>
                        <?php if ($returnvalue != 0 && $searching == TRUE) : ?>  
                            <div style="display: table">
                                <div style="display: table-row"> 
                                    <div id="table-cell-id">
                                        Id
                                    </div>
                                    <div id="table-cell-admin">
                                        Email Address/User
                                    </div>
                                    <div id="table-cell-admin">
                                        Company Name
                                    </div>
                                    <div id="table-cell-admin">
                                        Full Name
                                    </div>
                                    <div id="table-cell-admin">
                                        Address
                                    </div>
                                    <div id="table-cell-id">

                                    </div>
                                </div>
                                <?php for ($i = (0 + (30 * $j)); $i < $returnvalue && $i < (30 + (30 * $j)); $i++) : ?>
                                    <?php if ($returndata[$i] === null && $i != 0) : ?>
                                        <?php continue; ?>
                                    <?php endif; ?>
                                    <div style="display: table-row"> 
                                        <div id="table-cell-id">
                                            <?php echo $userdata[$returndata[$i]]->user_id; ?>
                                        </div>
                                        <div id="table-cell-admin">
                                            <input type="submit" value="<?php echo $userdata[$returndata[$i]]->user_email; ?>"
                                                   name="<?php echo "user" . $userdata[$returndata[$i]]->user_id; ?>" onmouseover="this.style.textDecoration ='underline';"
                                                   onmouseout="this.style.textDecoration='none';" style="border: none;color:#0000FF;cursor: pointer;">
                                        </div>
                                        <div id="table-cell-admin">
                                            <?php echo $userdata[$returndata[$i]]->user_companyname; ?>
                                        </div>
                                        <div id="table-cell-admin">
                                            <?php echo $userdata[$returndata[$i]]->user_lastname; ?>&nbsp<?php echo $userdata[$returndata[$i]]->user_firstname; ?>
                                        </div>
                                        <div id="table-cell-admin">
                                            <?php echo $userdata[$returndata[$i]]->user_streetnumber; ?>&nbsp;<?php echo $userdata[$returndata[$i]]->user_street; ?>
                                            <br><?php echo $userdata[$returndata[$i]]->user_postcode; ?>&nbsp;<?php echo $userdata[$returndata[$i]]->user_city; ?>
                                        </div>
                                        <div id="table-cell-id">
                                            <div id="admin-checkbox">
                                                <input type="checkbox" name="<?php echo "checkbox" . $userdata[$returndata[$i]]->user_id; ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            <?php else: ?>
                                No Search results found
                            <?php endif; ?>
                        </div><br>
                        <table>
                            <tr>
                                <td>
                                    <input type="submit" value="Edit" name="edit" style="float:left">
                                </td>    
                                <?php if ($returnvalue > 30 && $returnvalue < 60 && $j == 0) : ?>
                                    <td>
                                        <input type="submit" value="Next30>>" name="next30" style="float:right"><br><br>
                                    </td>
                                <?php elseif ($returnvalue > (30 + (30 * $j)) && $returnvalue > 29 && $i > 30) : ?>
                                    <td>
                                        <input type="submit" value="<<Prev30" name="prev30" style="float:right"><br><br>
                                    </td>
                                    <td>
                                        <input type="submit" value="Next30>>" name="next30" style="float:right"><br><br>
                                    </td>
                                <?php elseif ($returnvalue > 30 && $returnvalue < (30 + (30 * $j))) : ?>
                                    <td>
                                        <input type="submit" value="<<Prev30" name="prev30" style="float:right"><br><br>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        </table>
                    <?php endfor; ?>
                <?php endif; ?> 
            <?php endif; ?>
            <?php if ($searchmode == FALSE) : ?>
                <?php for ($i = 0; $i < $editvalue; $i++) : ?>
                    <table  cellpadding="2" border="0">
                        <tr>
                        <h4>Account Information for ID <?php echo $userdata[$editdata[$i]]->user_id; ?></h4>
                        </tr>
                        <tr>
                            <td>
                                <input name="<?php echo "id" . $i; ?>" type="text" style="display: none"
                                       value="<?php echo $userdata[$editdata[$i]]->user_id; ?>">
                            </td>
                            <td>
                                <input name="<?php echo "companyname" . $i; ?>" type="text" style="width: 200px" tabindex="<?php echo ($i + 1); ?>" 
                                       value="<?php echo $userdata[$editdata[$i]]->user_companyname; ?>">
                                <br><span class="error" style="color: black;">Company Name</span>
                            </td>
                            <td>
                                <input name="<?php echo "email" . $i; ?>" type="text" style="width: 200px" tabindex="<?php echo ($i + 2); ?>" 
                                       value="<?php echo $userdata[$editdata[$i]]->user_email; ?>">
                                <br><span class="error" style="color: black;">Email</span>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <input name="<?php echo "firstname" . $i; ?>" type="text" style="width: 200px" tabindex="<?php echo ($i + 3); ?>" 
                                       value="<?php echo $userdata[$editdata[$i]]->user_firstname; ?>">
                                <br><span class="error" style="color: black;">First Name</span>
                            </td>
                            <td>
                                <input name="<?php echo "lastname" . $i; ?>" type="text" style="width: 200px" tabindex="<?php echo ($i + 4); ?>" 
                                       value="<?php echo $userdata[$editdata[$i]]->user_lastname; ?>">
                                <br><span class="error" style="color: black;">Last Name</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input name="<?php echo "streetnumber" . $i; ?>" type="text" style="width: 50px" tabindex="<?php echo ($i + 6); ?>" 
                                       value="<?php echo $userdata[$editdata[$i]]->user_streetnumber; ?>">
                                <br><span class="error" style="color: black;">Street Nr</span>
                            </td>
                            <td>
                                <input name="<?php echo "street" . $i; ?>" type="text" style="width: 200px" tabindex="<?php echo ($i + 7); ?>" 
                                       value="<?php echo $userdata[$editdata[$i]]->user_street; ?>">
                                <br><span class="error" style="color: black;">Street Address</span>
                            </td>
                            <td>
                                <input name="<?php echo "telephone" . $i; ?>" type="text" style="width: 200px" tabindex="<?php echo ($i + 5); ?>" 
                                       value="<?php echo $userdata[$editdata[$i]]->user_telephone; ?>">               
                                <br><span class="error" style="color: black;">Telephone</span>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <input name="<?php echo "postcode" . $i; ?>" type="text" style="width: 200px" tabindex="<?php echo ($i + 8); ?>" 
                                       value="<?php echo $userdata[$editdata[$i]]->user_postcode; ?>">
                                <br><span class="error" style="color: black;">Postcode</span>
                            </td>
                            <td>
                                <input name="<?php echo "city" . $i; ?>" type="text" style="width: 200px" tabindex="<?php echo ($i + 9); ?>" 
                                       value="<?php echo $userdata[$editdata[$i]]->user_city; ?>">
                                <br><span class="error" style="color: black; ">City</span>
                            </td>
                        </tr>
                    <?php endfor; ?>
                </table>
                <table  cellpadding="2" border="0">
                    <tr>
                        <td>
                            <a href="admin.php?mode=user" id="button-account-admin" STYLE="text-decoration: none; color: black;">Back</a>
                        </td>
                        <td>
                            <input type="submit" value="Update" name="update">
                        </td>
                    </tr>
                </table>
            <?php endif; ?> 
        <?php endif; ?>
    </form>

</div>    

<?php include 'footer.php'; ?>
