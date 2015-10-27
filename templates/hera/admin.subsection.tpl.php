<?php if ($_GET['action'] == 'overview') : ?>
    <?php if (isset($_GET['sectionid'])) : ?>
        <h2>Edit Subsections of Section "<?php echo $sectionName; ?>"</h2>
        <div id="description">This Page gives you an overview about the Subsections in Section "<?php echo $sectionName; ?>". You can edit existing and create new Subsections. Be carefull. Every change will change the System immediately.</div>
        <br /><hr>
        <h3>Create new Subsection</h3>
        <div id="description">If you want to create a new Subsection. Click on the create Button.</div>

        <div id="admin-editmodule"><a href="admin.php?mode=subsection&action=newsubsection&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $sectionID; ?>" id="button">Create new Subsection</a></div>
        <br />
<!--  Deactivated until rewritten      <hr>
        <h3>Connect a Subsection of a other Module</h3>
        <div id="description">If you want to connect a Subsection out of different Section whith this Section, please click the "Connect Subsection" Button.</div>

        <div id="admin-editmodule"><a href="admin.php?mode=subsection&action=connectsubsection&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $sectionID; ?>" id="button">Connect Subsection</a></div>
        -->
        <br /><hr>
        <h3>Subsection overview for Section "<?php echo $sectionName; ?>"</h3>
        <div id="description">To edit a Subsection, click on the edit button. To activate/deactive a Subsection, klick on the activate/deactivate button. To edit the Questions click on the Name of a Subsection.</div>

        <div id="admin-showmodule">
            <?php for ($i = 0; $i < count($array); $i++) : ?>
                <?php if ($array[$i]->subsection_active == 1) : ?>
                    <div id="admin-module">
                    <?php else : ?>
                        <div id="admin-module-deactivated">
                        <?php endif ?>

                        <div id="module-head">
                            <div id="module-buttons">
                                <a href="admin.php?mode=subsection&action=deletesubsection&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $sectionID; ?>&subsectionid=<?php echo $array[$i]->subsection_id; ?>" id="button">Delete</a>
                                <a href="admin.php?mode=subsection&action=editsubsection&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $sectionID; ?>&subsectionid=<?php echo $array[$i]->subsection_id; ?>" id="button">Edit</a>
                                <?php if (isset($array[$i - 1]->subsection_id)) : ?><a href="admin.php?mode=subsection&action=sort&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $sectionID; ?>&subsectionid=<?php echo $array[$i]->subsection_id; ?>&dir=up" id="button">&uArr;</a>
                                <?php else : ?><a href="admin.php?mode=subsection&action=sort&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $sectionID; ?>&subsectionid=<?php echo $array[$i]->subsection_id; ?>&dir=up" id="button" style="visibility: hidden;">&uArr;</a>
                                <?php endif; ?>
                                <?php if (isset($array[$i + 1]->subsection_id)) : ?><a href="admin.php?mode=subsection&action=sort&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $sectionID; ?>&subsectionid=<?php echo $array[$i]->subsection_id; ?>&dir=down" id="button">&dArr;</a>
                                <?php else : ?><a href="admin.php?mode=subsection&action=sort&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $sectionID; ?>&subsectionid=<?php echo $array[$i]->subsection_id; ?>&dir=down" id="button" style="visibility: hidden;">&dArr;</a>
                                <?php endif; ?>               
                                <?php if ($array[$i]->subsection_active == 1) : ?><a href="admin.php?mode=subsection&action=activatesubsection&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $sectionID; ?>&subsectionid=<?php echo $array[$i]->subsection_id; ?>&active=0" id="button">Deactivate</a>
                                <?php else : ?><a href="admin.php?mode=subsection&action=activatesubsection&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $sectionID; ?>&subsectionid=<?php echo $array[$i]->subsection_id; ?>&active=1" id="button">Activate</a>
                                <?php endif ?>
                            </div>
                            <div id="module-name"><a href="admin.php?mode=question&action=overview&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $sectionID; ?>&subsectionid=<?php echo $array[$i]->subsection_id; ?>"><?php echo $array[$i]->subsection_name; ?></a></div>
                        </div>
                        <div id="module-description">
                            <?php echo $array[$i]->subsection_description; ?>
                        </div>      
                    </div> 
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php elseif ($_GET['action'] == 'newsubsection') : ?>
        <h2>Create new Subsection in Section "<?php echo $sectionName; ?>"</h2>
        <div id="description">Please enter the needed information about the Subsection.</div>
        <h3>Input data for Subsection</h3>
        <form action="admin.php?mode=subsection&action=newsubsection&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $sectionID; ?>" method="post" >
            <table  cellpadding="4" border="0">
                <tr>
                    <td valign="top" style="width: 120px;">
                        Section Name:
                    </td>
                    <td>
                        <input name="name" type="text" style="width: 316px" tabindex="1" value="<?php echo $_POST['name']; ?>">
                        <br /><span class="error"><?php echo $modErrorName; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="top">
                        Section Description:
                    </td>
                    <td>
                        <textarea style="width: 500px; height: 150px;" tabindex="2" name="description"><?php echo $_POST['description']; ?></textarea>
                        <br /><span class="error"><?php echo $modErrorDescription; ?></span>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        &nbsp;
                    </td>
                    <td valign="top">
                        <input name="active" type="checkbox" tabindex="3"> <span class="smalltext">Activate Section?</span>
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        &nbsp;
                    </td>
                    <td>
                        <input value="Create Subsection" tabindex="4" type="submit" name="create">
                        <span class="error"><?php echo $loginError; ?></span>
                    </td>    
                </tr>
            </table>
        </form>
    <?php elseif ($_GET['action'] == 'connectsubsection') : ?>
        <h2>Connect Subsection to Section "<?php echo $sectionName; ?>"</h2>
        <div id="description">This Page gives you an overview about every available Subsection in a different Section. Please select the Subsections you want to connect.</div>
        <br /><hr>
        <form action="admin.php?mode=subsection&action=connectsubsection&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>" method="post" >


            <?php for ($i = 0; $i < count($sections); $i++) : ?>
                <?php if (count($subsections[$i]) != '0') : ?>
                    <h3>Subsections of Section "<?php echo $sections[$i]->section_name; ?>":</h3>
                    <div id="admin-connect">
                        <?php for ($j = 0; $j < count($subsections[$i]); $j++) : ?>
                            <div id="admin-connect-deactivated">
                                <div id="connect-checkbox"><input name="check[]" type="checkbox" value="<?php echo $subsections[$i][$j]->subsection_id; ?>"></div>
                                <div id="connect-head">
                                    <div id="connect-name"><a href="admin.php?mode=question&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $sections[$i]->section_id; ?>&subsectionid=<?php echo $subsections[$i][$j]->subsection_id; ?>"><?php echo $subsections[$i][$j]->subsection_name; ?></a></div>
                                </div>
                                <div id="connect-description">
                                    <?php echo $subsections[$i][$j]->subsection_description; ?>
                                </div>      
                            </div> 
                        <?php endfor; ?>

                    </div>
                <?php endif; ?>
            <?php endfor; ?>
            <input value="Connect Subsections" tabindex="4" type="submit" name="connect">
        </form>
    <?php elseif ($_GET['action'] == 'editsubsection') : ?>
        <h2>Edit Subsection: "<?php echo $subsectiondata->subsection_name; ?>"</h2>
        <div id="description">Please enter the needed information about the Subsection.</div>
        <h3>Input data for Subsection</h3>
        <form action="admin.php?mode=subsection&action=editsubsection&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $_GET['subsectionid']; ?>" method="post" >
            <?php if ($moresections == TRUE) : ?>
                <div id="error">
                    This Subsection is Used in following Sections:
                    <ul>
                        <?php for ($i = 0; $i < count($sections); $i++) : ?>
                            <li><?php echo $sections[$i]->section_name; ?></li>
                        <?php endfor; ?>
                    </ul>
                    If you change the name or the description in here, this will also change the Subsection in every Section listed above.<br /><br />
                    You can separate this Subsection from the Sections listed above by checking this Checkbox:
                    <div><input name="separate" type="checkbox"> Separate Subsection</div><br/>
                    Be carefull. This action is permanent and can not be undone. When you have Clicked this Checkbox, the Subsection will be separated and every changeing will only apear in this Subsection.
                </div>
            <?php endif; ?>

            <table  cellpadding="4" border="0">
                <tr>
                    <td valign="top" style="width: 120px;">
                        Subsection Name:
                    </td>
                    <td>
                        <input name="name" type="text" style="width: 316px" tabindex="1" value="<?php
        if (isset($_POST['edit'])) {
            echo $_POST['name'];
        } else {
            echo $subsectiondata->subsection_name;
        }
            ?>">
                        <br /><span class="error"><?php echo $modErrorName; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="top">
                        Subsection Description:
                    </td>
                    <td>
                        <textarea style="width: 500px; height: 150px;" tabindex="2" name="description"><?php
                           if (isset($_POST['edit'])) {
                               echo $_POST['description'];
                           } else {
                               echo $subsectiondata->subsection_description;
                           }
            ?></textarea>
                        <br /><span class="error"><?php echo $modErrorDescription; ?></span>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        &nbsp;
                    </td>
                    <td valign="top">
                        <?php if (isset($_POST['edit'])) : ?>
                            <?php if ($_POST['active'] == on) : ?>
                                <input name="active" type="checkbox" tabindex="3" checked="checked">
                            <?php else : ?>
                                <input name="active" type="checkbox" tabindex="3">
                            <?php endif; ?>
                        <?php else : ?>
                            <?php if ($subsectiondata->subsection_active == 1) : ?>
                                <input name="active" type="checkbox" tabindex="3" checked="checked">
                            <?php else : ?>
                                <input name="active" type="checkbox" tabindex="3">
                            <?php
                            endif;
                        endif;
                        ?>

                        <span class="smalltext">Activate Subsection?</span>
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        &nbsp;
                    </td>
                    <td>
                        <input value="Edit Subsection" tabindex="4" type="submit" name="edit">
                        <span class="error"><?php echo $loginError; ?></span>
                    </td>    
                </tr>
            </table>
        </form>
    <?php elseif ($_GET['action'] == 'deletesubsection') : ?>
        <h2>Are you sure you want to delete the Subsection "<?php echo $deletesubsectiondata->subsection_name; ?>"</h2>
        <div id="description">Please be sure what you want to do! It is not possible to redo your decission.</div>
        <form action="admin.php?mode=subsection&action=deletesubsection&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $_GET['subsectionid']; ?>" method="post" >
            <table  cellpadding="4" border="0">
                <tr>
                    <td valign="top">
                        <input value="Delete Subsection" tabindex="4" type="submit" name="delete">
                    </td>
                    <td>
                        <input value="Abort" tabindex="4" type="submit" name="abort">
                    </td>    
                </tr>
            </table>
            <br /><span class="error"><?php echo $modErrorModule; ?></span>
        </form>

    <?php endif; ?>