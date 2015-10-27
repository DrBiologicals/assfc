<?php
$pageNavi = 'Revision';
include 'header.php';
?>

<?php require_once 'navbar.php'; ?>
<div id="main">
    <h1>Revision of <?php echo $name; ?></h1>
    <div class="user-textbox">
        This Revision helps you to answer the questions correct. Please read the Helpsection provided under the question. The wrong answered question modules are marked red. If you still have problems feel free and contact us.
    </div>
    <script type="text/javascript">
        function popup (url) {
            fenster = window.open(url, "Popupfenster", "width=500,height=400,resizable=yes, scrollbars=yes, top=150, left=700");
            fenster.focus();
            return false;
        }
    </script>

    <form action="revision.php?type=<?php echo $_GET['type']; ?>&moduleid=<?php echo $_GET['moduleid']; ?>&sectionid=<?php echo $_GET['sectionid']; ?>&subsectionid=<?php echo $_GET['subsectionid']; ?>" method="post" enctype="multipart/form-data">
        <?php for ($i = 0; $i < count($data); $i++) : ?>
            <div class="user-question">
                <div class="question-title"><?php echo $data[$i]->question_name; ?></div>
                <div class="question-description"><?php echo getHtml($data[$i]->question_description); ?></div>
                <?php for ($j = 0; $j < count($qmodules = $data[$i]->questionmodule); $j++) : ?>
                    <?php if ($qmodules[$j]->questionmodule_type == '1') : ?>
                        <div class="question-module">
                            <div class="question-module-radiobutton">
                                <div class="question-module-radiobutton-description"><?php echo $qmodules[$j]->questionmodule_description; ?></div>
                                <?php for ($k = 0; $k < count($qvalues = $qmodules[$j]->questionvalue); $k++) : ?>
                                    <?php if ($qvalues[$k]->questionvalue_revision == '1') : ?><div class="revision-question-wrongoption"><?php else : ?><div class="revision-question-rightoption"><?php endif; ?>
                                            <input type="radio" name="radio[<?php echo $data[$i]->question_id ?>]" <?php if ($qvalues[$k]->answer_checked == '1'): ?>checked="checked"<?php endif; ?> value="<?php echo $qvalues[$k]->questionvalue_id; ?>"> 
                                            <?php echo $qvalues[$k]->questionvalue_value; ?> 
                                            <?php if ($qvalues[$k]->questionvalue_revision == '1') : ?><div class="revision-question-errortext revision-question-errortext-options">Please consult <a href="help.php?subsectionid=<?php echo $_GET['subsectionid'] ?>&questionid=<?php echo $data[$i]->question_id; ?>" target="_blank" onclick="return popup(this.href);">help</a>.</div><?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($qmodules[$j]->questionmodule_type == '2') : ?>
                            <div class="question-module">
                                <div class="question-module-checkbox">
                                    <div class="question-module-checkbox-description"><?php echo $qmodules[$j]->questionmodule_description; ?></div>
                                    <?php for ($k = 0; $k < count($qvalues = $qmodules[$j]->questionvalue); $k++) : ?>
                                        <?php if ($qvalues[$k]->questionvalue_revision == '1') : ?><div class="revision-question-wrongoption"><?php else : ?><div class="revision-question-rightoption"><?php endif; ?>
                                                <input type="checkbox" name="checkbox[<?php echo $data[$i]->question_id ?>][<?php echo $qvalues[$k]->questionvalue_id; ?>]" <?php if ($qvalues[$k]->answer_checked == '1'): ?>checked="checked"<?php endif; ?> value="1">
                                                <?php echo $qvalues[$k]->questionvalue_value; ?>
                                                <?php if ($qvalues[$k]->questionvalue_revision == '1') : ?><div class="revision-question-errortext revision-question-errortext-options">Is needed. Please consult <a href="help.php?subsectionid=<?php echo $_GET['subsectionid'] ?>&questionid=<?php echo $data[$i]->question_id; ?>" target="_blank" onclick="return popup(this.href);">help</a>.</div><?php endif; ?>
                                            </div>
                                        <?php endfor; ?>
                                        <input type="hidden" name="checkboxexists" value="1">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($qmodules[$j]->questionmodule_type == '3') : ?>
                                <div class="question-module">
                                    <div class="question-module-single">
                                        <div class="question-module-single-description"><?php echo $qmodules[$j]->questionmodule_description; ?></div>
                                        <div style="display: table">
                                            <?php for ($k = 0; $k < count($qvalues = $qmodules[$j]->questionvalue); $k++) : ?>  
                                                <div style="display: table-row">
                                                    <div style="display: table-cell; width: 280px; padding-bottom: 20px;"><span class="smalltext"><?php echo $qvalues[$k]->questionvalue_value; ?></span></div>
                                                    <div style="display: table-cell"><input type="text" style="width: 240px;" name="single[<?php echo $data[$i]->question_id ?>][<?php echo $qvalues[$k]->questionvalue_id; ?>]" value="<?php if (isset($qvalues[$k]->answer_value)) : ?><?php echo $qvalues[$k]->answer_value; ?><?php endif; ?>" <?php if ($qvalues[$k]->questionvalue_revision == '1') : ?>class="revision-question-wrongtextfield"<?php endif; ?>><?php if ($qvalues[$k]->questionvalue_revision == '1') : ?><div class="revision-question-errortext">Missing statement. Consult <a href="help.php?subsectionid=<?php echo $_GET['sectionid'] ?>&questionid=<?php echo $data[$i]->question_id; ?>" target="_blank" onclick="return popup(this.href);">help</a> for more information.</div><?php endif; ?></div>                                           
                                                </div>                       
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($qmodules[$j]->questionmodule_type == '4') : ?>
                                <div class="question-module">
                                    <div class="question-module-multi">
                                        <div class="question-module-multi-description"><?php echo $qmodules[$j]->questionmodule_description; ?></div>
                                        <textarea style="width: 528px;height: 120px;" name="multi[<?php echo $data[$i]->question_id ?>][<?php echo $qmodules[$j]->questionvalue[0]->questionvalue_id; ?>]" <?php if ($qmodules[$j]->questionvalue[0]->questionvalue_revision == '1') : ?>class="revision-question-wrongtextfield"<?php endif; ?>><?php if (isset($qmodules[$j]->questionvalue[0]->answer_value)) : ?><?php echo $qmodules[$j]->questionvalue[0]->answer_value; ?><?php endif; ?></textarea>   
                                        <?php if ($qmodules[$j]->questionvalue[0]->questionvalue_revision == '1') : ?><div class="revision-question-errortext">Missing statement. Consult <a href="help.php?subsectionid=<?php echo $_GET['subsectionid'] ?>&questionid=<?php echo $data[$i]->question_id; ?>" target="_blank" onclick="return popup(this.href);">help</a> for more information.</div><?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($qmodules[$j]->questionmodule_type == '5') : ?>
                                <div class="question-module">
                                    <div class="question-module-upload">
                                        <div class="question-module-upload-description"><?php echo $qmodules[$j]->questionmodule_description; ?></div>
                                        <div <?php if ($qmodules[$j]->questionvalue[0]->questionvalue_revision == '1') : ?>class="revision-question-wrongoption"<?php endif; ?>>
                                            <input name="<?php echo $data[$i]->question_id ?>[]" type="file" multiple>
                                            <input type="submit" value="Upload" name="next">
                                            <?php if ($qmodules[$j]->questionvalue[0]->questionvalue_revision == '1') : ?><div class="revision-question-errortext">Upload necessary. See <a href="help.php?subsectionid=<?php echo $_GET['subsectionid'] ?>&questionid=<?php echo $data[$i]->question_id; ?>" target="_blank" onclick="return popup(this.href);">help</a>.</div><?php endif; ?>
                                        </div>
                                        <span class="smallertext">You can select more than one File by using the STRG-key. Maximum Upload size is <?php echo (int) (@ini_get('post_max_size')) . ' MB'; ?>.</span>
                                        <br /><br />
                                        <span class="error" style="font-weight: bold;font-size: small;"><?php echo $uploaderror[$data[$i]->question_id]; ?></span>
                                        <?php if (isset($qmodules[$j]->questionvalue[0]->answer_value)) : ?>
                                            <div class="question-module-upload-description">Uploaded Files:</div>
                                            <div style="display: table; margin-bottom: 10px;">
                                                <div style="display: table-row">
                                                    <div id="table-cell" style="width: 30px; font-weight: bold; text-align: center;">Nr</div>
                                                    <div id="table-cell" style="width: 400px; font-weight: bold;">Name</div>
                                                    <div id="table-cell" style="width: 75px; border-right: 0px; font-weight: bold;">Delete</div>
                                                    <div style="clear:both;"></div>
                                                </div>
                                                <?php
                                                $filelist = getFilelist($qmodules[$j]->questionvalue[0]->answer_value);
                                                for ($l = 0; $l < count($filelist['dsname']); $l++) :
                                                    ?>
                                                    <div style="display: table-row">
                                                        <div id="table-cell" style="width: 30px; text-align: center;"><?php echo $l + 1 ?></div>
                                                        <div id="table-cell" style="width: 400px;"><a href="https://<?php echo $website; ?>upload/<?php echo $filelist['dsname'][$l] ?>" target="_blank"><?php echo $filelist['name'][$l] ?>.<?php echo $filelist['extension'][$l] ?></a></div>
                                                        <div id="table-cell" style="width: 75px; border-right: 0px;"><input type="submit" value="Delete" name="next[<?php echo $data[$i]->question_id . '-' . $l ?>]"></div>
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


            </div>    

            <?php include 'footer.php'; ?>