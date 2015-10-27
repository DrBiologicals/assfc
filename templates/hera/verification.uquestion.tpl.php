<h2 style="margin-bottom: -10px;">Questions and Answers</h2>
<h1 style="margin-bottom: 25px;"><?php echo getSubsectionData($_GET['usubsectionid'])->subsection_name ?></h1>
<div class="progressbar" style="width: 500px;">
    <div class="progressbar-fill" style="width: <?php echo round(getSubsectionProgress($_GET['usubsectionid']) * 100) * 5; ?>px;"></div>
    <div class="progressbar-text">Accreditation progress: <?php echo round(getSubsectionProgress($_GET['usubsectionid']) * 100) . "%"; ?></div>
</div>
<?php if (count($ugroups) > 1) : ?>
<div id="group"> 
    <?php for ($i = 0; $i < count($ugroups); $i++) : ?>
        <div id="groups" style="<?php echo getGroupcolor($_GET['uquestiongroup'], $ugroups[$i]->question_group, $_GET['usubsectionid']) ?>"><a href="verification.php?mode=uquestion&umoduleid=<?php echo $_GET['umoduleid']; ?>&usectionid=<?php echo $_GET['usectionid']; ?>&usubsectionid=<?php echo $_GET['usubsectionid']; ?>&uquestiongroup=<?php echo $ugroups[$i]->question_group; ?>"><?php echo $ugroups[$i]->question_group; ?></a></div>
    <?php endfor; ?>
    <div style="clear: both;"></div>
</div>
<?php endif; ?>
<script type="text/javascript">
    function popup (url) {
        fenster = window.open(url, "Popupwindow", "width=500,height=400,resizable=yes, scrollbars=yes, top=150, left=700");
        fenster.focus();
        return false;
    }
</script>
<form action="verification.php?mode=uquestion&umoduleid=<?php echo $_GET['umoduleid']; ?>&usectionid=<?php echo $_GET['usectionid']; ?>&usubsectionid=<?php echo $_GET['usubsectionid']; ?>&uquestiongroup=<?php echo $_GET['uquestiongroup']; ?>" method="post" enctype="multipart/form-data">
    <?php for ($i = 0; $i < count($uquestions); $i++) : ?>
        <div class="user-question">
            <div class="question-title"><?php echo $uquestions[$i]->question_name; ?></div>
            <div class="question-help"><a href="help.php?subsectionid=<?php echo $_GET['usubsectionid']; ?>&questionid=<?php echo $uquestions[$i]->question_id; ?>" id="button" target="_blank" onclick="return popup(this.href);">Help</a></div>
            <div class="question-description"><?php echo getHtml($uquestions[$i]->question_description); ?></div>

            <?php for ($j = 0; $j < count($uquestionmodules[$i]); $j++) : ?>

                <?php if ($uquestionmodules[$i][$j]->questionmodule_type == '1') : ?>
                    <div class="question-module">
                        <div class="question-module-radiobutton">
                            <div class="question-module-radiobutton-description"><?php echo $uquestionmodules[$i][$j]->questionmodule_description; ?></div>
                            <?php for ($k = 0; $k < count($uquestionmodulevalues[$i][$j]); $k++) : ?>
                                <input type="radio" name="radio[<?php echo $uquestions[$i]->question_id ?>]" <?php if (!isset($_POST['next']) && $uquestionmodulevalues[$i][$j][$k]->questionvalue_active == '1' && $isanswered[$i][$j] == FALSE): ?>checked="checked"<?php elseif (!isset($_POST['next']) && $isanswered[$i][$j] == TRUE && $uanswermodulevalues[$i][$j][$k]->answer_checked == '1') : ?>checked="checked"<?php elseif (isset($_POST['next']) && $_POST['radio'][$uquestions[$i]->question_id] == $uquestionmodulevalues[$i][$j][$k]->questionvalue_id) : ?>checked="checked"<?php endif; ?> value="<?php echo $uquestionmodulevalues[$i][$j][$k]->questionvalue_id; ?>"> <?php echo $uquestionmodulevalues[$i][$j][$k]->questionvalue_value; ?><br />
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($uquestionmodules[$i][$j]->questionmodule_type == '2') : ?>
                    <div class="question-module">
                        <div class="question-module-checkbox">
                            <div class="question-module-checkbox-description"><?php echo $uquestionmodules[$i][$j]->questionmodule_description; ?></div>
                            <?php for ($k = 0; $k < count($uquestionmodulevalues[$i][$j]); $k++) : ?>
                                <input type="checkbox" name="checkbox[<?php echo $uquestions[$i]->question_id ?>][<?php echo $uquestionmodulevalues[$i][$j][$k]->questionvalue_id; ?>]" <?php if (!isset($_POST['next']) && $uquestionmodulevalues[$i][$j][$k]->questionvalue_active == '1' && $isanswered[$i][$j] == FALSE): ?>checked="checked"<?php elseif (!isset($_POST['next']) && $isanswered[$i][$j] == TRUE && $uanswermodulevalues[$i][$j][$k]->answer_checked == '1') : ?>checked="checked"<?php elseif (isset($_POST['next']) && $_POST['checkbox'][$uquestions[$i]->question_id][$uquestionmodulevalues[$i][$j][$k]->questionvalue_id] == '1') : ?>checked="checked"<?php endif; ?> value="1"> <?php echo $uquestionmodulevalues[$i][$j][$k]->questionvalue_value; ?>
                                <br />
                            <?php endfor; ?>
                                <input type="hidden" name="checkboxexists" value="1">
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($uquestionmodules[$i][$j]->questionmodule_type == '3') : ?>
                    <div class="question-module">
                        <div class="question-module-single">
                            <div class="question-module-single-description"><?php echo $uquestionmodules[$i][$j]->questionmodule_description; ?></div>
                            <div style="display: table">
                                <?php for ($k = 0; $k < count($uquestionmodulevalues[$i][$j]); $k++) : ?>  
                                    <div style="display: table-row">
                                        <div style="display: table-cell; width: 280px; padding-bottom: 20px;"><span class="smalltext"><?php echo $uquestionmodulevalues[$i][$j][$k]->questionvalue_value; ?></span></div>
                                        <div style="display: table-cell"><input type="text" style="width: 240px;" name="single[<?php echo $uquestions[$i]->question_id ?>][<?php echo $uquestionmodulevalues[$i][$j][$k]->questionvalue_id; ?>]" value="<?php
                    if (!isset($_POST['next'])) : echo $uanswermodulevalues[$i][$j][$k]->answer_value;
                    else : echo $_POST['single'][$uquestions[$i]->question_id][$uquestionmodulevalues[$i][$j][$k]->questionvalue_id];
                    endif;
                                    ?>"> </div>
                                    </div>                       
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($uquestionmodules[$i][$j]->questionmodule_type == '4') : ?>
                    <div class="question-module">
                        <div class="question-module-multi">
                            <div class="question-module-multi-description"><?php echo $uquestionmodules[$i][$j]->questionmodule_description; ?></div>
                            <textarea style="width: 528px;height: 120px;" name="multi[<?php echo $uquestions[$i]->question_id ?>][<?php echo $uquestionmodulevalues[$i][$j][0]->questionvalue_id; ?>]"><?php
            if (!isset($_POST['next'])) : echo $uanswermodulevalues[$i][$j][0]->answer_value;
            else : echo $_POST['multi'][$uquestions[$i]->question_id][$uquestionmodulevalues[$i][$j][0]->questionvalue_id];
            endif;
                    ?></textarea>   
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($uquestionmodules[$i][$j]->questionmodule_type == '5') : ?>
                    <div class="question-module">
                        <div class="question-module-upload">
                            <div class="question-module-upload-description"><?php echo $uquestionmodules[$i][$j]->questionmodule_description; ?></div>
                            <input name="<?php echo $uquestions[$i]->question_id ?>[]" type="file" multiple>
                            <input type="submit" value="Upload" name="next">
                            <br />
                            <span class="smallertext">You can select more than one File by using the CTRL-key. Maximum Upload size is <?php echo (int) (@ini_get('post_max_size')) . ' MB'; ?>.</span>
                            <br /><br />
                            <span class="error" style="font-weight: bold;font-size: small;"><?php echo $uploaderror[$uquestions[$i]->question_id]; ?></span>
                            <?php if ($isanswered[$i][$j] == TRUE && $uanswermodulevalues[$i][$j][0]->answer_value != null) : ?>
                                <div class="question-module-upload-description">Uploaded Files:</div>
                                <div style="display: table; margin-bottom: 10px;">
                                    <div style="display: table-row">
                                        <div id="table-cell" style="width: 30px; font-weight: bold; text-align: center;">Nr</div>
                                        <div id="table-cell" style="width: 400px; font-weight: bold;">Name</div>
                                        <div id="table-cell" style="width: 75px; border-right: 0px; font-weight: bold;">Delete</div>
                                        <div style="clear:both;"></div>
                                    </div>
                                    <?php
                                    $filelist = getFilelist($uanswermodulevalues[$i][$j][0]->answer_value);
                                    for ($l = 0; $l < count($filelist['dsname']); $l++) :
                                        ?>
                                        <div style="display: table-row">
                                            <div id="table-cell" style="width: 30px; text-align: center;"><?php echo $l + 1 ?></div>
                                            <div id="table-cell" style="width: 400px;"><a href="https://<?php echo $website; ?>upload/<?php echo $filelist['dsname'][$l] ?>" target="_blank"><?php echo $filelist['name'][$l] ?>.<?php echo $filelist['extension'][$l] ?></a></div>
                                            <div id="table-cell" style="width: 75px; border-right: 0px;"><input type="submit" value="Delete" name="next[<?php echo $uquestions[$i]->question_id . '-' . $l ?>]"></div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endif; ?>

            <?php endfor; ?>
        </div>

    <?php endfor; ?>
    <br /><br />
    <input type="submit" value="Save & Next" name="next">
</form>