<?php if ($_GET['action'] == 'overview') : ?>
    <h2>Edit Modules</h2>
    <div id="description">This Page gives you an overview about every module. You can edit existing and create new Modules. Be carefull. Every change will change the System immediately.</div>
    <br /><hr>
    <h3>Create new Module</h3>
    <div id="description">If you want to create a new Module. Klick on the create Button.</div>

    <div id="admin-editmodule"><a href="admin.php?mode=module&action=newmodule" id="button">Create new Module</a></div>
    <br /><hr>
    <h3>Module overview</h3>
    <div id="description">To edit a module, klick on the edit button. To activate/deactive a module, klick on the activate/deactivate button. To edit the sections click on the Name of a module.</div>

    <div id="admin-showmodule">
        <?php for ($i = 0; $i < count($array); $i++) : ?>
            <?php if ($array[$i]->module_active == 1) : ?>
                <div id="admin-module">
                <?php else : ?>
                    <div id="admin-module-deactivated">
                    <?php endif ?>

                    <div id="module-head">
                        <div id="module-buttons">
                            <a href="admin.php?mode=module&action=deletemodule&moduleid=<?php echo $array[$i]->module_id; ?>" id="button">Delete</a>
                            <a href="admin.php?mode=module&action=editmodule&moduleid=<?php echo $array[$i]->module_id; ?>" id="button">Edit</a>
                            <?php if (isset($array[$i - 1]->module_id)) : ?><a href="admin.php?mode=module&action=sort&id=<?php echo $array[$i]->module_id; ?>&dir=up" id="button">&uArr;</a>
                            <?php else : ?><a href="admin.php?mode=module&action=sort&id=<?php echo $array[$i]->module_id; ?>&dir=up" id="button" style="visibility: hidden;">&uArr;</a>
                            <?php endif; ?>
                            <?php if (isset($array[$i + 1]->module_id)) : ?><a href="admin.php?mode=module&action=sort&id=<?php echo $array[$i]->module_id; ?>&dir=down" id="button">&dArr;</a>
                            <?php else : ?><a href="admin.php?mode=module&action=sort&id=<?php echo $array[$i]->module_id; ?>&dir=down" id="button" style="visibility: hidden;">&dArr;</a>
                            <?php endif; ?>                            
                            <?php if ($array[$i]->module_active == 1) : ?><a href="admin.php?mode=module&action=activatemodule&id=<?php echo $array[$i]->module_id; ?>&active=0" id="button">Deactivate</a>
                            <?php else : ?><a href="admin.php?mode=module&action=activatemodule&id=<?php echo $array[$i]->module_id; ?>&active=1" id="button">Activate</a>
                            <?php endif; ?>
                        </div>
                        <div id="module-name"><a href="admin.php?mode=section&action=overview&moduleid=<?php echo $array[$i]->module_id; ?>"><?php echo $array[$i]->module_name; ?></a></div>
                    </div>
                    <div id="module-description">
                        <?php echo $array[$i]->module_description; ?>
                    </div>      
                </div> 
            <?php endfor; ?>
        </div>
    <?php elseif ($_GET['action'] == 'newmodule') : ?>
        <h2>Create new modules</h2>
        <div id="description">Please enter the needed information about the module.</div>
        <h3>Input data for module</h3>
        <form action="admin.php?mode=module&action=newmodule" method="post" >
            <table  cellpadding="4" border="0">
                <tr>
                    <td valign="top" style="width: 120px;">
                        Module Name:
                    </td>
                    <td>
                        <input name="name" type="text" style="width: 316px" tabindex="1" value="<?php echo $_POST['name']; ?>">
                        <br /><span class="error"><?php echo $modErrorName; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="top">
                        Module Description:
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
                        <input name="active" type="checkbox" tabindex="3"> <span class="smalltext">Activate Module?</span>
                    </td>    
                </tr>
                <tr>
                    <td valign="center">
                        &nbsp;
                    </td>
                    <td>
                        <input value="Create Module" tabindex="4" type="submit" name="create">
                        <span class="error"><?php echo $loginError; ?></span>
                    </td>    
                </tr>
            </table>
        </form>

    <?php elseif ($_GET['action'] == 'editmodule') : ?>
        <h2>Edit modules</h2>
        <div id="description">Please enter the needed information about the module.</div>
        <h3>Input data for module</h3>
        <form action="admin.php?mode=module&action=editmodule&moduleid=<?php echo $_GET['moduleid']; ?>" method="post" >
            <table  cellpadding="4" border="0">
                <tr>
                    <td valign="top" style="width: 120px;">
                        Module Name:
                    </td>
                    <td>
                        <input name="name" type="text" style="width: 316px" tabindex="1" value="<?php
    if (isset($_POST['edit'])) {
        echo $_POST['name'];
    } else {
        echo $moduledata->module_name;
    }
        ?>">
                        <br /><span class="error"><?php echo $modErrorName; ?></span>
                    </td>    
                </tr>
                <tr>
                    <td valign="top">
                        Module Description:
                    </td>
                    <td>
                        <textarea style="width: 500px; height: 150px;" tabindex="2" name="description"><?php
                           if (isset($_POST['edit'])) {
                               echo $_POST['description'];
                           } else {
                               echo $moduledata->module_description;
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
                            <?php if ($moduledata->module_active == 1) : ?>
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
                        <input value="Edit Module" tabindex="4" type="submit" name="edit">
                        <span class="error"><?php echo $loginError; ?></span>
                    </td>    
                </tr>
            </table>
        </form>
    <?php elseif ($_GET['action'] == 'deletemodule') : ?>
        <h2>Are you sure you want to delete the Module "<?php echo $moduledeletedata->module_name; ?>"</h2>
        <div id="description">Please be sure what you want to do! It is not possible to redo your decision.</div>
        <form action="admin.php?mode=module&action=deletemodule&moduleid=<?php echo $_GET['moduleid']; ?>" method="post" >
            <table  cellpadding="4" border="0">
                <tr>
                    <td valign="top">
                        <input value="Delete Module" tabindex="4" type="submit" name="delete">
                    </td>
                    <td>
                        <input value="Abort" tabindex="4" type="submit" name="abort">
                    </td>    
                </tr>
            </table>
            <br /><span class="error"><?php echo $modErrorModule; ?></span>
        </form>

    <?php endif; ?>