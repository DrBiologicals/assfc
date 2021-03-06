<?php if ($_GET['action'] == 'overview') : ?>
    <?php if (isset($_GET['moduleid'])) : ?>
        <h2>Edit Sections of Module "<?php echo $moduleName; ?>"</h2>
        <div id="description">This Page gives you an overview about the sections in Module "<?php echo $moduleName; ?>". You can edit existing and create new Sections. Be carefull. Every change will change the System immediately.</div>
        <br /><hr>
        <h3>Create new Section</h3>
        <div id="description">If you want to create a new Section. Klick on the create Button.</div>

        <div id="admin-editmodule"><a href="admin.php?mode=section&action=newsection&moduleid=<?php echo $moduleID; ?>" id="button">Create new Section</a></div>
        <br />
<!-- Deactivated until rewritten       <hr>
        <h3>Connect a Section of a other Module</h3>
        <div id="description">If you want to connect a Section out of different Module whith this Module, please click the "Connect Section" Button.</div>

        <div id="admin-editmodule"><a href="admin.php?mode=section&action=connectsection&moduleid=<?php echo $moduleID; ?>" id="button">Connect Section</a></div>
        -->
        <br /><hr>
        <h3>Section overview for Module "<?php echo $moduleName; ?>"</h3>
        <div id="description">To edit a Section, klick on the edit button. To activate/deactive a Section, klick on the activate/deactivate button. To edit the Subsections click on the Name of a Section.</div>

        <div id="admin-showmodule">
            <?php for ($i = 0; $i < count($array); $i++) : ?>
                <?php if ($array[$i]->section_active == 1) : ?>
                    <div id="admin-module">
                    <?php else : ?>
                        <div id="admin-module-deactivated">
                        <?php endif ?>

                        <div id="module-head">
                            <div id="module-buttons">
                                <a href="admin.php?mode=section&action=deletesection&moduleid=<?php echo $moduleID; ?>&sectionid=<?php echo $array[$i]->section_id; ?>" id="button">Delete</a>
                                <a href="admin.php?mode=section&action=editsection&moduleid=<?php echo $moduleID; ?>&sectionid=<?php echo $array[$i]->section_id; ?>" id="button">Edit</a>
                                <?php if (isset($array[$i - 1]->section_id)) : ?><a href="admin.php?mode=section&action=sort&moduleid=<?php echo $moduleID; ?>&sectionid=<?php echo $array[$i]->section_id; ?>&dir=up" id="button">&uArr;</a>
                                <?php else : ?><a href="admin.php?mode=section&action=sort&moduleid=<?php echo $moduleID; ?>&sectionid=<?php echo $array[$i]->section_id; ?>&dir=up" id="button" style="visibility: hidden;">&uArr;</a>
                                <?php endif; ?>
                                <?php if (isset($array[$i + 1]->section_id)) : ?><a href="admin.php?mode=section&action=sort&moduleid=<?php echo $moduleID; ?>&sectionid=<?php echo $array[$i]->section_id; ?>&dir=down" id="button">&dArr;</a>
                                <?php else : ?><a href="admin.php?mode=section&action=sort&moduleid=<?php echo $moduleID; ?>&sectionid=<?php echo $array[$i]->section_id; ?>&dir=down" id="button" style="visibility: hidden;">&dArr;</a>
                                <?php endif; ?>               
                                <?php if ($array[$i]->section_active == 1) : ?><a href="admin.php?mode=section&action=activatesection&moduleid=<?php echo $moduleID; ?>&sectionid=<?php echo $array[$i]->section_id; ?>&active=0" id="button">Deactivate</a>
                                <?php else : ?><a href="admin.php?mode=section&action=activatesection&moduleid=<?php echo $moduleID; ?>&sectionid=<?php echo $array[$i]->section_id; ?>&active=1" id="button">Activate</a>
                                <?php endif ?>
                            </div>
                            <div id="module-name"><a href="admin.php?mode=subsection&action=overview&moduleid=<?php echo $moduleID; ?>&sectionid=<?php echo $array[$i]->section_id; ?>"><?php echo $array[$i]->section_name; ?></a></div>
                        </div>
                        <div id="module-description">
                            <?php echo $array[$i]->section_description; ?>
                        </div>      
                    </div> 
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php elseif ($_GET['action'] == 'newsection') : ?>
        <h2>Create new Section in Module "<?php echo $moduleName; ?>"</h2>
        <div id="description">Please enter the needed information about the Section.</div>
        <h3>Input data for Section</h3>
        <form action="admin.php?mode=section&action=newsection&moduleid=<?php echo $moduleID; ?>" method="post" >
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
                        <input value="Create Section" tabindex="4" type="submit" name="create">
                        <span class="error"><?php echo $loginError; ?></span>
                    </td>    
                </tr>
            </table>
        </form>
    <?php elseif ($_GET['action'] == 'connectsection') : ?>
        <h2>Connect Section to Module "<?php echo $moduleName; ?>"</h2>
        <div id="description">This Page gives you an overview about every available Section in a different Module. Please select the Sections you want to connect.</div>
        <br /><hr>
        <form action="admin.php?mode=section&action=connectsection&moduleid=<?php echo $_GET['moduleid']; ?>" method="post" >


            <?php for ($i = 0; $i < count($modules); $i++) : ?>
                <?php if (count($sections[$i]) != '0') : ?>
                    <h3>Sections of Module "<?php echo $modules[$i]->module_name; ?>":</h3>
                    <div id="admin-connect">
                        <?php for ($j = 0; $j < count($sections[$i]); $j++) : ?>
                            <div id="admin-connect-activated">
                                <div id="connect-checkbox"><input name="check[]" type="checkbox" value="<?php echo $sections[$i][$j]->section_id; ?>"></div>
                                <div id="connect-head">
                                    <div id="connect-name"><a href="admin.php?mode=newsubsection&moduleid=<?php echo $modules[$i]->module_id; ?>&sectionid=<?php echo $sections[$i][$j]->section_id; ?>"><?php echo $sections[$i][$j]->section_name; ?></a></div>
                                </div>
                                <div id="connect-description">
                                    <?php echo $sections[$i][$j]->section_description; ?>
                                </div>      
                            </div> 
                        <?php endfor; ?>

                    </div>
                <?php endif; ?>
            <?php endfor; ?>
            <input value="Connect Sections" tabindex="4" type="submit" name="connect">
        </form>
    <?php elseif ($_GET['action'] == 'editsection') : ?>
        <h2>Edit Section: "<?php echo $sectiondata->section_name; ?>"</h2>
        <div id="description">Please enter the needed information about the section.</div>
        <h3>Input data for Section</h3>
        <form action="admin.php?mode=section&action=editsection&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>" method="post" >
            <?php if ($moremodules == TRUE) : ?>
                <div id="error">
                    This Section is Used in following Modules:
                    <ul>
                        <?php for ($i = 0; $i < count($modules); $i++) : ?>
                            <li><?php echo $modules[$i]->module_name; ?></li>
                        <?php endfor; ?>
                    </ul>
                    If you change the name or the Description in here, this will also change the Section in every Module listed above.<br /><br />
                    You can separate this Section from the Modules listed above by checking this Checkbox:
                    <div><input name="separate" type="checkbox"> Separate Section</div><br/>
                    Be carefull. This action is permanent and can not be undone. When you have Clicked this Checkbox, the Section will be separated and every changeing will only apear in this Section.
                </div>
            <?php endif; ?>

            <table  cellpadding="4" border="0">
                <tr>
                    <td valign="top" style="width: 120px;">
                        Section Name:
                    </td>
                    <td>
                        <input name="name" type="text" style="width: 316px" tabindex="1" value="<?php
        if (isset($_POST['edit'])) {
            echo $_POST['name'];
        } else {
            echo $sectiondata->section_name;
        }
            ?>">
                        <br /><span class="error"><?php echo $modErrorName; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="top">
                        Section Description:
                    </td>
                    <td>
                        <textarea style="width: 500px; height: 150px;" tabindex="2" name="description"><?php
                           if (isset($_POST['edit'])) {
                               echo $_POST['description'];
                           } else {
                               echo $sectiondata->section_description;
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
                            <?php if ($sectiondata->section_active == 1) : ?>
                                <input name="active" type="checkbox" tabindex="3" checked="checked">
                            <?php else : ?>
                                <input name="active" type="checkbox" tabindex="3">
                            <?php
                            endif;
                        endif;
                        ?>

                        <span class="smalltext">Activate Module?</span>
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        &nbsp;
                    </td>
                    <td>
                        <input value="Edit Section" tabindex="4" type="submit" name="edit">
                        <span class="error"><?php echo $loginError; ?></span>
                    </td>    
                </tr>
            </table>
        </form>
    <?php elseif ($_GET['action'] == 'deletesection') : ?>
        <h2>Are you sure you want to delete the Section "<?php echo $deletesectiondata->section_name; ?>"</h2>
        <div id="description">Please be sure what you want to do! It is not possible to redo your decission.</div>
        <form action="admin.php?mode=section&action=deletesection&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>" method="post" >
            <table  cellpadding="4" border="0">
                <tr>
                    <td valign="top">
                        <input value="Delete Section" tabindex="4" type="submit" name="delete">
                    </td>
                    <td>
                        <input value="Abort" tabindex="4" type="submit" name="abort">
                    </td>    
                </tr>
            </table>
            <br /><span class="error"><?php echo $modErrorModule; ?></span>
        </form>

    <?php endif; ?>