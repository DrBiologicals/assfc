<?php if ($_GET['action'] == 'overview') : ?>
    <?php if (isset($_GET['subsectionid'])) : ?>
        <h2>Edit Questions of Subsection "<?php echo htmlspecialchars($subsectionName); ?>"</h2>
        <div id="description">This Page gives you an overview about the Questions in Subsection "<?php echo getHtml($subsectionName); ?>". You can edit existing and create new Questions. Be carefull. Every change will change the System immediately.</div>
        <br /><hr>
        <h3>Create new Question</h3>
        <div id="description">If you want to create a new Question. Click on the create Button.</div>

        <div id="admin-editmodule"><a href="admin.php?mode=question&action=newquestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>" id="button">Create new Question</a></div>
        <br />

        <!--  Deactivated until rewritten      <hr>
                <h3>Connect a Question of a other Subsection</h3>
                <div id="description">If you want to connect a Question out of different Subsection whith this Subsection, please click the "Connect Question" Button.</div>

                <div id="admin-editmodule"><a href="admin.php?mode=question&action=connectquestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>" id="button">Connect Question</a></div>
        -->
        <br /><hr>
        <h3>Question overview for Subsection "<?php echo htmlspecialchars($subsectionName); ?>"</h3>
        <div id="description">To edit a Question, click on the edit button. To activate/deactivate a Question, click on the activate/deactivate button.</div>

        <div id="admin-showmodule">
            <?php for ($i = 0; $i < count($array); $i++) : ?>
                <?php if ($array[$i]->question_delete == 0) : ?>
                    <?php if ($array[$i]->question_active == 1) : ?>
                        <div id="admin-module">
                        <?php else : ?>
                            <div id="admin-module-deactivated">
                            <?php endif ?>
                            <div id="module-head">
                                <div style="float:left;">                                
                                    <?php if ($array[$i]->question_version > 1) : ?>                                
                                        <a href="admin.php?mode=question&action=versionquestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>" id="button">Version</a>
                                        v<?php echo $array[$i]->question_version; ?>.0
                                    <?php endif; ?>
                                </div>
                                <div id="module-buttons">
                                    <a href="admin.php?mode=question&action=deletequestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>" id="button">Delete</a>
                                    <a href="admin.php?mode=question&action=duplicatequestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>" id="button">Duplicate</a>
                                    <a href="admin.php?mode=question&action=editquestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>" id="button">Edit</a>
                                    <?php if (isset($array[$i - 1]->question_id)) : ?><a href="admin.php?mode=question&action=sort&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>&dir=up" id="button">&uArr;</a>
                                    <?php else : ?><a href="admin.php?mode=question&action=sort&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>&dir=up" id="button" style="visibility: hidden;">&uArr;</a>
                                    <?php endif; ?>
                                    <?php if (isset($array[$i + 1]->question_id)) : ?><a href="admin.php?mode=question&action=sort&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>&dir=down" id="button">&dArr;</a>
                                    <?php else : ?><a href="admin.php?mode=question&action=sort&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>&dir=down" id="button" style="visibility: hidden;">&dArr;</a>
                                    <?php endif; ?>               
                                    <?php if ($array[$i]->question_active == 1) : ?><a href="admin.php?mode=question&action=activatequestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>&active=0" id="button">Deactivate</a>
                                    <?php else : ?><a href="admin.php?mode=question&action=activatequestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>&active=1" id="button">Activate</a>
                                    <?php endif ?>
                                </div>
                                <div id="module-name"><?php echo getHtml($array[$i]->question_name); ?></div>
                            </div>
                            <div id="module-description">
                                <?php echo getHtml($array[$i]->question_description); ?>
                            </div>      
                        </div>
                    <?php endif ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php elseif ($_GET['action'] == 'versionquestion') : ?>
        <?php if (isset($_GET['subsectionid'])) : ?>
            <h2>Different Versions of Question</h2>
            <div id="description">This page displays the different versions of the question (insert question name), To revert a question to an older version or convert to a newer version hit activate.
                To view the version of a question hit view button on the corresponding question. To delete a question permanently hit delete.</div>
            <br /><hr>
            <div id="admin-showmodule">
                <?php for ($i = 0; $i < count($array); $i++) : ?>
                    <?php if ($questiongroup == 0) : ?>
                        <?php if ($array[$i]->question_active == 1) : ?>
                            <div id="admin-module">
                            <?php else : ?>
                                <div id="admin-module-deactivated">
                                <?php endif ?>
                                <div id="module-head">
                                    <div style="float:left;">                                                                
                                        v<?php echo $array[$i]->question_version; ?>.0
                                    </div>
                                    <div id="module-buttons">
                                        <a href="admin.php?mode=question&action=deletequestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>" id="button">Delete</a>
                                        <a href="admin.php?mode=question&action=viewquestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>" id="button">View</a>
                                        <?php if ($array[$i]->question_active == 1) : ?><a href="admin.php?mode=question&action=activatequestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>&active=0" id="button">Deactivate</a>
                                        <?php else : ?><a href="admin.php?mode=question&action=activatequestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>&questionid=<?php echo $array[$i]->question_id; ?>&active=1" id="button">Activate</a>
                                        <?php endif ?>
                                    </div>
                                    <div id="module-name"><?php echo getHtml($array[$i]->question_name); ?></div>
                                </div>
                                <div id="module-description">
                                    <?php echo getHtml($array[$i]->question_description); ?>
                                </div>      
                            </div>
                         <?php endif ?>
                    <?php endfor; ?>                    
                         <br>
                    <div>
                        <a href="admin.php?mode=question&action=overview&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>" id="button">Back</a>                
                    </div>
                </div>                
            <?php endif; ?>
        <?php elseif ($_GET['action'] == 'newquestion') : ?>
            <h2>Create new Question in Subsection "<?php echo htmlspecialchars($subsectionName); ?>"</h2>
            <div id="description">Please enter the needed information about the Question.</div>
            <h3>Input data for Question</h3>
            <form action="admin.php?mode=question&action=newquestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsectionID; ?>" method="post" >
                <table  cellpadding="4" border="0">
                    <tr>
                        <td valign="top" style="width: 120px;">
                            Execution Class:
                        </td>
                        <td>
                            <select name="execution" size="1">
                                <?php for ($i = 0; $i < count($execution); $i++) : ?>
                                    <option value="<?php echo $execution[$i]->exclass_id; ?>" <?php if ($_POST['execution'] == $execution[$i]->exclass_id) : ?>selected="selected"<?php endif; ?>><?php echo htmlspecialchars($execution[$i]->exclass_name); ?></option>
                                <?php endfor; ?>
                            </select>
                        </td>    
                    </tr>
                    <tr>
                        <td valign="top" style="width: 120px;">
                            Group Number:
                        </td>
                        <td>
                            <select name="group" size="1">
                                <option value="<?php echo $maxgroup; ?>">New Group</option>
                                <?php for ($i = 0; $i < count($group); $i++) : ?>
                                    <option value="<?php echo $group[$i]->question_group; ?>" <?php if ($_POST['group'] == $group[$i]->question_group) : ?>selected="selected"<?php endif; ?>>Group <?php echo htmlspecialchars($group[$i]->question_group); ?></option>
                                <?php endfor; ?>
                            </select>
                        </td>    
                    </tr>
                    <tr>
                        <td valign="top" style="width: 120px;">
                            Question:
                        </td>
                        <td>
                            <input name="name" type="text" style="width: 492px" value="<?php echo htmlspecialchars($_POST['name']); ?>">
                            <br /><span class="error"><?php echo $modErrorName; ?></span>
                        </td>    
                    </tr>
                    <tr>
                        <td valign="top">
                            Question Description(Optional):
                        </td>
                        <td>
                            <textarea style="width: 500px; height: 150px;" name="description"><?php echo htmlspecialchars($_POST['description']); ?></textarea>
                            <br /><span class="error"><?php echo $modErrorDescription; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            Standard Reference:
                        </td>
                        <td>
                            <input name="reference" type="text" style="width: 492px" value="<?php echo htmlspecialchars($_POST['reference']); ?>">
                            <br /><span class="error"><?php echo $modErrorReference; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            Help Description:
                        </td>
                        <td>
                            <textarea style="width: 500px; height: 150px;" name="help"><?php echo htmlspecialchars($_POST['help']); ?></textarea>
                            <br /><span class="error"><?php echo $modErrorHelp; ?></span>
                        </td>
                    </tr>
                    <?php if (isset($_POST['refresh']) || isset($_POST['deletemodule']) || isset($_POST['radiodelete']) || isset($_POST['singledelete']) || isset($_POST['checkdelete']) || isset($_POST['create'])) : ?>
                        <tr>
                            <td valign="top">
                                Selected Modules:
                            </td>
                            <td valign="top">
                                <?php if (isset($_POST['checkradio'])) : ?>                         
                                    <div id="question-module">
                                        <input value="Delete" type="submit" name="deletemodule[<?php echo $_POST['checkradio']; ?>]" style="position: absolute;top:  10px; right: 10px">
                                        <h3>Radiobuttons</h3>
                                        <div>
                                            Questionmodule description for radiobuttons (optional):<br />
                                            <textarea style="width: 480px; height: 50px;" name="descriptionradio"><?php echo htmlspecialchars($_POST['descriptionradio']); ?></textarea>
                                        </div>
                                        <div style="margin-top: 15px;">
                                            Please define every radio option and select the radio option which should be preselected. Which radiobutton is required which is optional?<br /><br />

                                            <div style="display: table;">

                                                <div style="display: table-row;">
                                                    <div style="display: table-cell; width: 60px; vertical-align: central; text-align: center;">
                                                        Required
                                                    </div>
                                                    <div style="display: table-cell; width: 60px; vertical-align: central; text-align: center;">
                                                        PreSelected
                                                    </div>
                                                    <div style="display: table-cell;">

                                                    </div>
                                                    <div style="display: table-cell">

                                                    </div>

                                                </div>

                                                <?php for ($i = 0; $i < $radiocount; $i++) : ?>

                                                    <div style="display: table-row">  

                                                        <div style="display: table-cell;vertical-align: central; text-align: center;">

                                                            <input name="radio_req[<?php echo $i; ?>]" type="checkbox" value="1" <?php if ($_POST['radio_req'][$i] == '1') : ?> checked="checked" <?php endif; ?>> 
                                                        </div>
                                                        <div style="display: table-cell;vertical-align: central; text-align: center;">
                                                            <input name="radio" type="radio" value="<?php echo $i; ?>" <?php if ($_POST['radio'] == $i) : ?>CHECKED<?php endif; ?>>
                                                        </div>

                                                        <div style="display: table-cell;vertical-align: central; text-align: center;" >
                                                            <input name="radioinput[<?php echo $i; ?>]" type="text" style="width: 300px;" value="<?php echo htmlspecialchars($radioinput[$i]); ?>">
                                                        </div>

                                                        <div style="display: table-cell">
                                                            <input value="X" type="submit" name="radiodelete[<?php echo $i; ?>]">
                                                        </div>

                                                    </div>
                                                <?php endfor; ?>
                                                <input type="hidden" value="<?php echo $radiocount; ?>" name="radiocount">
                                                <input type="hidden" value="radio" name="checkradio">
                                                <br /><span class="error"><?php echo $modErrorRadio; ?></span>
                                            </div>
                                            <div>
                                                Add <input name="radiomore" type="text" style="width: 20px"> more Options.
                                                <input value="Refresh" tabindex="13" type="submit" name="refresh">
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($_POST['checkcheck'])) : ?>                         
                                    <div id="question-module">
                                        <input value="Delete" type="submit" name="deletemodule[<?php echo $_POST['checkcheck']; ?>]" style="position: absolute;top:  10px; right: 10px">
                                        <h3>Checkboxes</h3>
                                        <div>
                                            Questionmodule description for checkboxes (optional):<br />
                                            <textarea style="width: 480px; height: 50px;" name="descriptioncheck"><?php echo htmlspecialchars($_POST['descriptioncheck']); ?></textarea>
                                        </div>
                                        <div style="margin-top: 15px;">
                                            Please define every checkbox option and preselect the checkboxes. Which checkbox is required which is optional?<br /><br />
                                            <div style="display: table;">

                                                <div style="display: table-row;">
                                                    <div style="display: table-cell; width: 60px; vertical-align: central; text-align: center;">
                                                        Required
                                                    </div>
                                                    <div style="display: table-cell; width: 60px; vertical-align: central; text-align: center;">
                                                        PreSelected
                                                    </div>
                                                    <div style="display: table-cell;">

                                                    </div>
                                                    <div style="display: table-cell">

                                                    </div>

                                                </div>

                                                <?php for ($i = 0; $i < $checkcount; $i++) : ?>
                                                    <div style="display: table-row;">

                                                        <div style="display: table-cell;vertical-align: central; text-align: center;">
                                                            <input name="check_req[<?php echo $i; ?>]" type="checkbox" value="1" <?php if ($_POST['check_req'][$i] == '1') : ?> checked="checked" <?php endif; ?>> 
                                                        </div>

                                                        <div style="display: table-cell;vertical-align: central; text-align: center;">
                                                            <input name="check[<?php echo $i; ?>]" type="checkbox" value="1" <?php if ($_POST['check'][$i] == '1') : ?>CHECKED<?php endif; ?>> 
                                                        </div>
                                                        <div style="display: table-cell;">
                                                            <input name="checkinput[<?php echo $i; ?>]" type="text" style="width: 300px;" value="<?php echo htmlspecialchars($checkinput[$i]); ?>"> 
                                                        </div>

                                                        <div style="display: table-cell;">
                                                            <input value="X" type="submit" name="checkdelete[<?php echo $i; ?>]">
                                                        </div>    
                                                    </div>
                                                <?php endfor; ?>
                                            </div>

                                            <input type="hidden" value="<?php echo $checkcount; ?>" name="checkcount">
                                            <input type="hidden" value="check" name="checkcheck">
                                            <br /><span class="error"><?php echo $modErrorCheck; ?></span>

                                            <div>
                                                Add <input name="checkmore" type="text" style="width: 20px"> more Options.
                                                <input value="Refresh" tabindex="13" type="submit" name="refresh">
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($_POST['checksingle'])) : ?>                         
                                    <div id="question-module">
                                        <input value="Delete" type="submit" name="deletemodule[<?php echo $_POST['checksingle']; ?>]" style="position: absolute;top:  10px; right: 10px">
                                        <h3>Single Line Textfield</h3>
                                        <div>
                                            Questionmodule description for single line textfields (optional):<br />
                                            <textarea style="width: 480px; height: 50px;" name="descriptionsingle"><?php echo htmlspecialchars($_POST['descriptionsingle']); ?></textarea>
                                        </div>
                                        <div style="margin-top: 15px;">
                                            Please define every single line textfield. The text will be prompted in front of every textfield.<br /><br />
                                            <div style="display: table;">

                                                <div style="display: table-row;">
                                                    <div style="display: table-cell; width: 60px; vertical-align: central; text-align: center;">
                                                        Required
                                                    </div>
                                                    <div style="display: table-cell;">

                                                    </div>
                                                    <div style="display: table-cell">

                                                    </div>

                                                </div>
                                                <?php for ($i = 0; $i < $singlecount; $i++) : ?>
                                                    <div style="display: table-row;">
                                                        <div style="display: table-cell;vertical-align: central; text-align: center;">
                                                            <input name="single_req[<?php echo $i; ?>]" type="checkbox" value="1" <?php if ($_POST['single_req'][$i] == '1') : ?> checked="checked" <?php endif; ?>> 
                                                        </div>
                                                        <div style="display: table-cell;vertical-align: central; text-align: center;">
                                                            <input name="singleinput[<?php echo $i; ?>]" type="text" style="width: 340px;" value="<?php echo htmlspecialchars($singleinput[$i]); ?>">
                                                        </div>
                                                        <div style="display: table-cell;vertical-align: central; text-align: center;">    
                                                            <input value="X" type="submit" name="singledelete[<?php echo $i; ?>]">
                                                        </div>
                                                    </div> 
                                                <?php endfor; ?>
                                                <input type="hidden" value="<?php echo $singlecount; ?>" name="singlecount">
                                                <input type="hidden" value="single" name="checksingle">
                                                <br /><span class="error"><?php echo $modErrorSingle; ?></span>
                                            </div>
                                            <div>
                                                Add <input name="singlemore" type="text" style="width: 20px"> more Options.
                                                <input value="Refresh" tabindex="13" type="submit" name="refresh">
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($_POST['checkmulti'])) : ?>                         
                                    <div id="question-module">
                                        <input value="Delete" type="submit" name="deletemodule[<?php echo $_POST['checkmulti']; ?>]" style="position: absolute;top:  10px; right: 10px">
                                        <h3>Multi Line Textfield</h3>
                                        <div>
                                            Questionmodule description for multi line textfields (optional):<br />
                                            <textarea style="width: 480px; height: 50px;" name="descriptionmulti"><?php echo htmlspecialchars($_POST['descriptionmulti']); ?></textarea>
                                            Required: <input type="checkbox" value="1" name="multi_req" <?php if ($_POST['multi_req'] == '1') : ?> checked="checked" <?php endif; ?>>
                                            <input type="hidden" value="multi" name="checkmulti">
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($_POST['checkupload'])) : ?>                         
                                    <div id="question-module">
                                        <input value="Delete" type="submit" name="deletemodule[<?php echo $_POST['checkupload']; ?>]" style="position: absolute;top:  10px; right: 10px">
                                        <h3>Upload Module</h3>
                                        <div>
                                            Questionmodule description for uploads (optional):<br />
                                            <textarea style="width: 480px; height: 50px;" name="descriptionupload"><?php echo htmlspecialchars($_POST['descriptionupload']); ?></textarea>
                                            Required: <input type="checkbox" value="1" name="upload_req" <?php if ($_POST['upload_req'] == '1') : ?> checked="checked" <?php endif; ?>>
                                            <input type="hidden" value="upload" name="checkupload">
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <br /><span class="error"><?php echo $modErrorquestionmodule; ?></span>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <td valign="top">
                            &nbsp;
                        </td>
                        <td valign="top">
                            <input name="active" type="checkbox" tabindex="3"> <span class="smalltext">Activate Question?</span>
                        </td>    
                    </tr>
                    <?php if (isset($_POST['checkradio']) && isset($_POST['checkcheck']) && isset($_POST['checksingle']) && isset($_POST['checkmulti']) && isset($_POST['checkupload'])) : ?>
                    <?php else : ?>
                        <tr>
                            <td valign="top">
                                Question Modules:
                            </td>
                            <td>
                                Which questionmodules do you need in this question? Please click "refresh" after selection.
                                <?php if (!isset($_POST['checkradio'])) : ?>
                                    <div id="question-module">
                                        <input name="checkradio" type="checkbox" tabindex="4" value="radio"> Radio Buttons with
                                        <input name="radiocount" type="text" style="width: 20px" value="2" tabindex="5"> options.
                                    </div>
                                <?php endif; ?>
                                <?php if (!isset($_POST['checkcheck'])) : ?>
                                    <div id="question-module">
                                        <input name="checkcheck" type="checkbox" tabindex="6" value="check"> Checkboxes with
                                        <input name="checkcount" type="text" style="width: 20px" value="2" tabindex="7"> options.
                                    </div>
                                <?php endif; ?>
                                <?php if (!isset($_POST['checksingle'])) : ?>
                                    <div id="question-module">
                                        <input name="checksingle" type="checkbox" tabindex="8" value="single"> Single-line Textfield.
                                        <input name="singlecount" type="text" style="width: 20px" value="2" tabindex="9"> Textfileds.
                                    </div>
                                <?php endif; ?>
                                <?php if (!isset($_POST['checkmulti'])) : ?>
                                    <div id="question-module">
                                        <input name="checkmulti" type="checkbox" tabindex="10" value="multi"> Multiline Textfield.
                                    </div>
                                <?php endif; ?>
                                <?php if (!isset($_POST['checkupload'])) : ?>
                                    <div id="question-module">
                                        <input name="checkupload" type="checkbox" tabindex="11" value="upload"> Uploadoption.
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td valign="center">
                            &nbsp;
                        </td>
                        <td>
                            <input value="Create Question" type="submit" name="create">
                            <input value="Refresh" type="submit" name="refresh">
                            <span class="error"><?php echo $loginError; ?></span>
                        </td>    
                    </tr>
                </table>
            </form>
        <?php elseif ($_GET['action'] == 'connectquestion') : ?>
            <h2>Connect Question to Subsection "<?php echo getHtml($subsectionName); ?>"</h2>
            <div id="description">This Page gives you an overview about every available Question in a different Subsection. Please select the Question you want to connect.</div>
            <br /><hr>
            <form action="admin.php?mode=question&action=connectquestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $_GET['subsectionid']; ?>" method="post" >


                <?php for ($i = 0; $i < count($subsections); $i++) : ?>
                    <?php if (count($questions[$i]) != '0') : ?>
                        <h3>Questions of Subsection "<?php echo getHtml($subsections[$i]->subsection_name); ?>":</h3>
                        <div id="admin-connect">
                            <?php for ($j = 0; $j < count($questions[$i]); $j++) : ?>
                                <div id="admin-connect-deactivated">
                                    <div id="connect-checkbox"><input name="check[]" type="checkbox" value="<?php echo $questions[$i][$j]->question_id; ?>"></div>
                                    <div id="connect-head">
                                        <div id="connect-name"><a href="admin.php?mode=question&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $subsections[$i]->subsection_id; ?>&questionid=<?php echo $questions[$i][$j]->question_id; ?>"><?php echo htmlspecialchars($questions[$i][$j]->question_name); ?></a></div>
                                    </div>
                                    <div id="connect-description">
                                        <?php echo getHtml($questions[$i][$j]->question_description); ?>
                                    </div>      
                                </div> 
                            <?php endfor; ?>

                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
                <input value="Connect Question" tabindex="4" type="submit" name="connect">
            </form>
        <?php elseif ($_GET['action'] == 'editquestion') : ?>
            <h2>Edit Question: "<?php echo htmlspecialchars($questionname); ?>"</h2>
            <div id="description">Please enter the needed information about the Question.</div>
            <h3>Input data for Question</h3>
            <form action="admin.php?mode=question&action=editquestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $_GET['subsectionid']; ?>&questionid=<?php echo $_GET['questionid']; ?>" method="post" >
                <?php if ($moresubsections == TRUE) : ?>
                    <div id="error">
                        This Question is Used in following Subsections:
                        <ul>
                            <?php for ($i = 0; $i < count($subsections); $i++) : ?>
                                <li><?php echo getHtml($subsections[$i]->subsection_name); ?></li>
                            <?php endfor; ?>
                        </ul>
                        If you change the name or the description in here, this will also change the Question in every Subsection listed above.<br /><br />
                        You can separate this Question from the Question listed above by checking this Checkbox:
                        <div><input name="separate" type="checkbox" <?php
                    if ($_POST['separate'] == on) : echo 'checked="checked"';
                    endif;
                            ?>>Separate Question</div><br/>
                        Be carefull. This action is permanent and can not be undone. When you have Clicked this Checkbox, the Question will be separated and every changeing will only apear in this Question.
                    </div>
                <?php endif; ?>

                <table  cellpadding="4" border="0">
                    <tr>
                        <td valign="top" style="width: 120px;">
                            Question Name:
                        </td>
                        <td>
                            <input name="name" type="text" style="width: 316px" tabindex="1" value="<?php echo htmlspecialchars($questionname); ?>">
                            <br /><span class="error"><?php echo $modErrorName; ?></span>
                        </td>    
                    </tr>
                    <tr>
                        <td valign="top" style="width: 120px;">
                            Execution Class:
                        </td>
                        <td>
                            <select name="execution" size="1">
                                <?php for ($i = 0; $i < count($questionexclass); $i++) : ?>
                                    <option value="<?php echo $questionexclass[$i]->exclass_id; ?>" <?php if ($questionexclassselect == $questionexclass[$i]->exclass_id) : ?>selected="selected"<?php endif; ?>><?php echo $questionexclass[$i]->exclass_name; ?></option>
                                <?php endfor; ?>
                            </select>
                        </td>    
                    </tr>
                    <tr>
                        <td valign="top" style="width: 120px;">
                            Group Number:
                        </td>
                        <td>
                            <select name="group" size="1">
                                <option value="<?php echo $maxgroup; ?>">New Group</option>
                                <?php for ($i = 0; $i < count($questiongroup); $i++) : ?>
                                    <option value="<?php echo $questiongroup[$i]->question_group; ?>" <?php if ($questiongroupselect == $questiongroup[$i]->question_group) : ?>selected="selected"<?php endif; ?>>Group <?php echo $questiongroup[$i]->question_group; ?></option>
                                <?php endfor; ?>
                            </select>
                        </td>    
                    </tr>
                    <tr>
                        <td valign="top">
                            Question Description:
                        </td>
                        <td>
                            <textarea style="width: 500px; height: 150px;" tabindex="2" name="description"><?php echo htmlspecialchars($questiondescription); ?></textarea>
                            <br /><span class="error"><?php echo $modErrorDescription; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            Standard reference:
                        </td>
                        <td>
                            <input name="reference" type="text" style="width: 492px" value="<?php echo htmlspecialchars($questionreference); ?>">
                            <br /><span class="error"><?php echo $modErrorReference; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            Help Description:
                        </td>
                        <td>
                            <textarea style="width: 500px; height: 150px;" tabindex="2" name="help"><?php echo htmlspecialchars($questionhelp); ?></textarea>
                            <br /><span class="error"><?php echo $modErrorHelp; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            Selected Modules:
                        </td>
                        <td valign="top">
                            <?php if (isset($checkradio)) : ?>                         
                                <div id="question-module">
                                    <input value="Delete" type="submit" name="deletemodule[<?php echo $checkradio; ?>]" style="position: absolute;top:  10px; right: 10px">
                                    <h3>Radiobuttons</h3>
                                    <div>
                                        Module Description for Radiobuttons (optional):<br />
                                        <textarea style="width: 480px; height: 50px;" name="descriptionradio"><?php echo $radiodescription; ?></textarea>
                                    </div>
                                    <div style="margin-top: 15px;">
                                        Please define every radio option and Select the radio option which should be preselected.<br /><br />

                                        <div style="display: table;">

                                            <div style="display: table-row;">
                                                <div style="display: table-cell; width: 60px; vertical-align: central; text-align: center;">
                                                    Required
                                                </div>
                                                <div style="display: table-cell; width: 60px; vertical-align: central; text-align: center;">
                                                    PreSelected
                                                </div>
                                                <div style="display: table-cell;">

                                                </div>
                                                <div style="display: table-cell">

                                                </div>

                                            </div>

                                            <?php for ($i = 0; $i < $radiocount; $i++) : ?>

                                                <div style="display: table-row">    

                                                    <div style="display: table-cell;vertical-align: central; text-align: center;">

                                                        <input name="radio_req[<?php echo $i; ?>]" type="checkbox" value="1" <?php if ($radioreq[$i] == '1') : ?> checked="checked" <?php endif; ?>> 
                                                    </div>

                                                    <div style="display: table-cell;vertical-align: central; text-align: center;">
                                                        <input name="radio" type="radio" value="<?php echo $i; ?>" <?php if ($radiocheck == $i) : ?> <?php echo 'checked="checked"'; ?><?php endif; ?>>
                                                    </div>

                                                    <div style="display: table-cell;vertical-align: central; text-align: center;" >
                                                        <input name="radioinput[<?php echo $i; ?>]" type="text" style="width: 300px;" value="<?php echo htmlspecialchars($radioinput[$i]); ?>"> 
                                                    </div>

                                                    <div style="display: table-cell">
                                                        <input value="X" type="submit" name="deletevalue[<?php echo $checkradio; ?>][<?php echo $i; ?>]">
                                                    </div>
                                                </div>

                                            <?php endfor; ?>

                                            <input type="hidden" value="<?php echo $radiocount; ?>" name="radiocount">
                                            <input type="hidden" value="radio" name="checkradio">
                                            <br /><span class="error"><?php echo $modErrorRadio; ?></span>
                                        </div>
                                        <div>
                                            Add <input name="radiomore" type="text" style="width: 20px;"> more Options.
                                            <input value="Refresh" tabindex="13" type="submit" name="refresh">
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($checkcheck)) : ?>                         
                                <div id="question-module">
                                    <input value="Delete" type="submit" name="deletemodule[<?php echo $checkcheck; ?>]" style="position: absolute;top:  10px; right: 10px">
                                    <h3>Checkboxes</h3>
                                    <div>
                                        Module Description for Checkboxes (optional):<br />
                                        <textarea style="width: 480px; height: 50px;" name="descriptioncheck"><?php echo htmlspecialchars($checkdescription); ?></textarea>
                                    </div>
                                    <div style="margin-top: 15px;">
                                        Please define every checkbox option and select the checkbox options which should be preselected.<br /><br />
                                        <div style="display: table;">

                                            <div style="display: table-row;">
                                                <div style="display: table-cell; width: 60px; vertical-align: central; text-align: center;">
                                                    Required
                                                </div>
                                                <div style="display: table-cell; width: 60px; vertical-align: central; text-align: center;">
                                                    PreSelected
                                                </div>
                                                <div style="display: table-cell;">

                                                </div>
                                                <div style="display: table-cell">

                                                </div>

                                            </div>

                                            <?php for ($i = 0; $i < $checkcount; $i++) : ?>
                                                <div style="display: table-row;">

                                                    <div style="display: table-cell;vertical-align: central; text-align: center;">
                                                        <input name="check_req[<?php echo $i; ?>]" type="checkbox" value="1" <?php if ($checkreq[$i] == '1') : ?> checked="checked" <?php endif; ?>> 
                                                    </div>

                                                    <div style="display: table-cell;vertical-align: central; text-align: center;">
                                                        <input name="check[<?php echo $i; ?>]" type="checkbox" value="1" <?php if ($checkactive[$i] == '1') : ?> checked="checked" <?php endif; ?>> 
                                                    </div>

                                                    <div style="display: table-cell;">
                                                        <input name="checkinput[<?php echo $i; ?>]" type="text" style="width: 300px;" value="<?php echo $checkinput[$i]; ?>">
                                                    </div>

                                                    <div style="display: table-cell;">
                                                        <input value="X" type="submit" name="deletevalue[<?php echo $checkcheck; ?>][<?php echo $i; ?>]">
                                                    </div>
                                                </div>

                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <input type="hidden" value="<?php echo $checkcount; ?>" name="checkcount">
                                    <input type="hidden" value="check" name="checkcheck">
                                    <br /><span class="error"><?php echo $modErrorRadio; ?></span>

                                    <div>
                                        Add <input name="checkmore" type="text" style="width: 20px;"> more Options.
                                        <input value="Refresh" tabindex="13" type="submit" name="refresh">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($checksingle)) : ?>                         
                                <div id="question-module">
                                    <input value="Delete" type="submit" name="deletemodule[<?php echo $checksingle; ?>]" style="position: absolute;top:  10px; right: 10px">
                                    <h3>Single line Textfields</h3>
                                    <div>
                                        Module Description for Single Line Textfields:<br />
                                        <textarea style="width: 480px; height: 50px;" name="descriptionsingle"><?php echo htmlspecialchars($singledescription); ?></textarea>
                                    </div>
                                    <div style="margin-top: 15px;">
                                        Please define every single line option.<br /><br />
                                        <div style="display: table;">

                                            <div style="display: table-row;">
                                                <div style="display: table-cell; width: 60px; vertical-align: central; text-align: center;">
                                                    Required
                                                </div>
                                                <div style="display: table-cell;">

                                                </div>
                                                <div style="display: table-cell">

                                                </div>

                                            </div>
                                            <?php for ($i = 0; $i < $singlecount; $i++) : ?>
                                                <div style="display: table-row;">
                                                    <div style="display: table-cell;vertical-align: central; text-align: center;">
                                                        <input name="single_req[<?php echo $i; ?>]" type="checkbox" value="1" <?php if ($singlereq[$i] == '1') : ?> checked="checked" <?php endif; ?>> 
                                                    </div>
                                                    <div style="display: table-cell;vertical-align: central; text-align: center;">
                                                        <input name="singleinput[<?php echo $i; ?>]" type="text" style="width: 300px;" value="<?php echo htmlspecialchars($singleinput[$i]); ?>">
                                                    </div>
                                                    <div style="display: table-cell;vertical-align: central; text-align: center;">
                                                        <input value="X" type="submit" name="deletevalue[<?php echo $checksingle; ?>][<?php echo $i; ?>]">
                                                    </div>
                                                </div>                                   
                                            <?php endfor; ?>
                                        </div>
                                        <input type="hidden" value="<?php echo $singlecount; ?>" name="singlecount">
                                        <input type="hidden" value="single" name="checksingle">
                                        <br /><span class="error"><?php echo $modErrorRadio; ?></span>
                                    </div>
                                    <div>
                                        Add <input name="singlemore" type="text" style="width: 20px;"> more Options.
                                        <input value="Refresh" tabindex="13" type="submit" name="refresh">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($checkmulti)) : ?>                         
                                <div id="question-module">
                                    <input value="Delete" type="submit" name="deletemodule[<?php echo $checkmulti; ?>]" style="position: absolute;top:  10px; right: 10px">
                                    <h3>Multi Line Textfield</h3>
                                    <div>
                                        Module Description for Multi Line Textfields (optional):<br />
                                        <textarea style="width: 480px; height: 50px;" name="descriptionmulti"><?php echo htmlspecialchars($multidescription); ?></textarea>
                                        Required: <input type="checkbox" value="1" name="multi_req" <?php if ($multireq == '1') : ?> checked="checked" <?php endif; ?>>
                                        <input type="hidden" value="multi" name="checkmulti">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($checkupload)) : ?>                         
                                <div id="question-module">
                                    <input value="Delete" type="submit" name="deletemodule[<?php echo $checkupload; ?>]" style="position: absolute;top:  10px; right: 10px">
                                    <h3>Upload Module</h3>
                                    <div>
                                        Module Description for Uploads (optional):<br />
                                        <textarea style="width: 480px; height: 50px;" name="descriptionupload"><?php echo htmlspecialchars($uploaddescription); ?></textarea>
                                        Required: <input type="checkbox" value="1" name="upload_req" <?php if ($uploadreq == '1') : ?> checked="checked" <?php endif; ?>>
                                        <input type="hidden" value="upload" name="checkupload">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </td>    
                    </tr>
                    <?php if (isset($checkradio) && isset($checkcheck) && isset($checksingle) && isset($checkmulti) && isset($checkupload)) : ?>
                    <?php else : ?>            
                        <tr>
                            <td valign="top">
                                Question Modules:
                            </td>
                            <td>
                                Which Questionmodules do you need in this Question? Plaese click "refresh" after selection.
                                <?php if (!isset($checkradio)) : ?>
                                    <div id="question-module">
                                        <input name="checkradio" type="checkbox" tabindex="4" value="radio"> Radio Buttons with
                                        <input name="radiocount" type="text" style="width: 20px" value="2" tabindex="5"> options.
                                    </div>
                                <?php endif; ?>
                                <?php if (!isset($checkcheck)) : ?>
                                    <div id="question-module">
                                        <input name="checkcheck" type="checkbox" tabindex="6" value="check"> Checkboxes with
                                        <input name="checkcount" type="text" style="width: 20px" value="2" tabindex="7"> options.
                                    </div>
                                <?php endif; ?>
                                <?php if (!isset($checksingle)) : ?>
                                    <div id="question-module">
                                        <input name="checksingle" type="checkbox" tabindex="8" value="single"> Single-line Textfield.
                                        <input name="singlecount" type="text" style="width: 20px" value="2" tabindex="9"> Textfileds.
                                    </div>
                                <?php endif; ?>
                                <?php if (!isset($checkmulti)) : ?>
                                    <div id="question-module">
                                        <input name="checkmulti" type="checkbox" tabindex="10" value="multi"> Multiline Textfield.
                                    </div>
                                <?php endif; ?>
                                <?php if (!isset($checkupload)) : ?>
                                    <div id="question-module">
                                        <input name="checkupload" type="checkbox" tabindex="11" value="upload"> Uploadoption.
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td valign="top">
                            &nbsp;
                        </td>
                        <td valign="top">

                            <input name="active" type="checkbox" <?php if ($questionactive == TRUE) : ?>checked="checked"<?php endif; ?>>

                            <span class="smalltext">Activate Question?</span>
                        </td>    
                    </tr>
                    <tr>
                        <td valign="center">
                            &nbsp;
                        </td>
                        <td>
                            <input value="Edit Question" tabindex="4" type="submit" name="edit">
                            <input value="Refresh" tabindex="13" type="submit" name="refresh">
                            <span class="error"><?php echo $loginError; ?></span>
                        </td>    
                    </tr>
                </table>
            </form>
        <?php elseif ($_GET['action'] == 'deletequestion') : ?>
            <h2>Are you sure you want to delete the Question "<?php echo htmlspecialchars($questiondatadelete->question_name); ?>"</h2>
            <div id="description">Please be sure what you want to do! It is not possible to redo your decission.</div>
            <form action="admin.php?mode=question&action=deletequestion&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $_GET['subsectionid']; ?>&questionid=<?php echo $questiondatadelete->question_id; ?>" method="post" >
                <table  cellpadding="4" border="0">
                    <tr>
                        <td valign="top">
                            <input value="Delete Question" tabindex="4" type="submit" name="delete">
                        </td>
                        <td>
                            <input value="Abort" tabindex="4" type="submit" name="abort">
                        </td>    
                    </tr>
                </table>
                <br /><span class="error"><?php echo $modErrorModule; ?></span>
            </form>

        <?php endif; ?>
