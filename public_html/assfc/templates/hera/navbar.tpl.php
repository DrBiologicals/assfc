<div id="navbar">

    <?php if ($globalusergroup == 3 || $globalusergroup == 2 || $globalusergroup == 1) : ?>
        <div class="navbar-title"><a href="verification.php?mode=main">Verification</a></div>

       <div class="navbar-items">
            <?php for ($i = 0; $i < count($usermenuemodules); $i++) : ?>
                <?php if (checklist($usermenuemodules[$i]->module_id, $modulesettings) == '1') : ?>
                    <div id="user-module-menue">
                        <a href="verification.php?mode=umodule&umoduleid=<?php echo $usermenuemodules[$i]->module_id; ?>" style="color: #000000;"><?php echo $usermenuemodules[$i]->module_name; ?></a>
                        <?php if ($_GET['umoduleid'] == $usermenuemodules[$i]->module_id) : ?>
                            <ul style="padding-left: 15px; padding-bottom: 5px; padding-top: 5px; list-style-type: disc;">
                                <?php for ($j = 0; $j < count($usermenuesections[$i]); $j++) : ?>
                                    <li style="padding-bottom: 3px;">
                                        <!-- 
                                            <?php if ($_GET['usectionid'] == $usermenuesections[$i][$j]->section_id) : ?>
                                            <a href="verification.php?mode=usubsection&action=overview&umoduleid=<?php echo $usermenuemodules[$i]->module_id; ?>&usectionid=<?php echo $usermenuesections[$i][$j]->section_id; ?>" style="color: #000000;"><?php echo $usermenuesections[$i][$j]->section_name; ?></a>
                                        <?php else : ?>
                                            <a href="verification.php?mode=usubsection&action=overview&umoduleid=<?php echo $usermenuemodules[$i]->module_id; ?>&usectionid=<?php echo $usermenuesections[$i][$j]->section_id; ?>"><?php echo $usermenuesections[$i][$j]->section_name; ?></a>
                                        <?php endif; ?> 
                                        -->
                                            <?php echo $usermenuesections[$i][$j]->section_name; ?>
                                    </li>
                                        <ul style="padding-left: 15px; padding-bottom: 5px; padding-top: 5px; list-style-type: circle;">
                                            <?php for ($k = 0; $k < count($usermenuesubsections[$i][$j]); $k++) : ?>
                                                <li style="padding-bottom: 3px;">
                                                    <?php if ($_GET['usubsectionid'] == $usermenuesubsections[$i][$j][$k]->subsection_id) : ?>
                                                        <a href="verification.php?mode=uquestion&umoduleid=<?php echo $usermenuemodules[$i]->module_id; ?>&usectionid=<?php echo $usermenuesections[$i][$j]->section_id; ?>&usubsectionid=<?php echo $usermenuesubsections[$i][$j][$k]->subsection_id; ?>&uquestiongroup=1" style="color: #000000;"><?php echo $usermenuesubsections[$i][$j][$k]->subsection_name; ?></a>
                                                    <?php else : ?>
                                                        <a href="verification.php?mode=uquestion&umoduleid=<?php echo $usermenuemodules[$i]->module_id; ?>&usectionid=<?php echo $usermenuesections[$i][$j]->section_id; ?>&usubsectionid=<?php echo $usermenuesubsections[$i][$j][$k]->subsection_id; ?>&uquestiongroup=1"><?php echo $usermenuesubsections[$i][$j][$k]->subsection_name; ?></a>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                <?php endfor; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
            </ul>
                <li style="padding-bottom: 3px;"><a href="report.php">Create Report</a></li>
            </ul>
        </div> 






        <?php
    endif;
    if ($globalusergroup == 2 || $globalusergroup == 1) :
        ?>
        <!--        <div id="navbar-title"><a href="backend.php?mode=main">ASSFC Backend</a></div>
                <div id="navbar-items">
                    <ul style="padding-left: 20px;">
                        <li style="padding-bottom: 3px;"><a href="admin.php?mode=help">Edit Help</a></li>
                        <li style="padding-bottom: 3px;"><a href="admin.php?mode=user">Activate Users</a></li>
                    </ul>
                </div>-->
        <?php
    endif;
    if ($globalusergroup == 1) :
        ?>
        <div class="navbar-title"><a href="admin.php?mode=main">Administration</a></div>
        <div class="navbar-items">
            <ul style="padding-left: 10px; padding-bottom: 5px;list-style-type: none;">
                <li style="padding-bottom: 3px;"><a href="admin.php?mode=module&action=overview">Modules</a></li>
                <ul style="padding-left: 15px; padding-bottom: 5px; padding-top: 5px;list-style-type: disc;">
                    <?php for ($i = 0; $i < count($menuemodules); $i++) : ?>
                        <li style="padding-bottom: 3px;">
                            <?php if ($_GET['moduleid'] == $menuemodules[$i]->module_id) : ?>
                                <a href="admin.php?mode=section&action=overview&moduleid=<?php echo $menuemodules[$i]->module_id; ?>" style="color: #000000;"><?php echo $menuemodules[$i]->module_name; ?></a>                       
                            <?php else : ?>
                                <a href="admin.php?mode=section&action=overview&moduleid=<?php echo $menuemodules[$i]->module_id; ?>"><?php echo $menuemodules[$i]->module_name; ?></a>
                            <?php endif; ?>
                        </li>
                        <?php if ($_GET['moduleid'] == $menuemodules[$i]->module_id) : ?>
                            <ul style="padding-left: 15px; padding-bottom: 5px; padding-top: 5px; list-style-type: circle">
                                <?php for ($j = 0; $j < count($menuesections); $j++) : ?>
                                    <li style="padding-bottom: 3px;">
                                        <?php if ($_GET['sectionid'] == $menuesections[$j]->section_id) : ?>
                                            <a href="admin.php?mode=subsection&action=overview&moduleid=<?php echo $menuemodules[$i]->module_id; ?>&sectionid=<?php echo $menuesections[$j]->section_id; ?>" style="color: #000000;"><?php echo $menuesections[$j]->section_name; ?></a>
                                        <?php else : ?>
                                            <a href="admin.php?mode=subsection&action=overview&moduleid=<?php echo $menuemodules[$i]->module_id; ?>&sectionid=<?php echo $menuesections[$j]->section_id; ?>"><?php echo $menuesections[$j]->section_name; ?></a>
                                        <?php endif; ?>
                                    </li>
                                    <?php if ($_GET['sectionid'] == $menuesections[$j]->section_id) : ?>
                                        <ul style="padding-left: 15px; padding-bottom: 5px; padding-top: 5px; list-style-type: square">
                                            <?php for ($k = 0; $k < count($menuesubsections); $k++) : ?>
                                                <li style="padding-bottom: 3px;">
                                                    <?php if ($_GET['subsectionid'] == $menuesubsections[$k]->subsection_id) : ?>
                                                        <a href="admin.php?mode=question&action=overview&moduleid=<?php echo $menuemodules[$i]->module_id; ?>&sectionid=<?php echo $menuesections[$j]->section_id; ?>&subsectionid=<?php echo $menuesubsections[$k]->subsection_id; ?>" style="color: #000000;"><?php echo $menuesubsections[$k]->subsection_name; ?></a>
                                                    <?php else : ?>
                                                        <a href="admin.php?mode=question&action=overview&moduleid=<?php echo $menuemodules[$i]->module_id; ?>&sectionid=<?php echo $menuesections[$j]->section_id; ?>&subsectionid=<?php echo $menuesubsections[$k]->subsection_id; ?>"><?php echo $menuesubsections[$k]->subsection_name; ?></a>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </ul>
                        <?php endif; ?>
                    <?php endfor; ?>
                </ul>
                <li style="padding-bottom: 3px;"><a href="report.php">Create Report</a></li>
                <li style="padding-bottom: 3px;"><a href="admin.php?mode=user">Users</a></li>
            </ul>
        </div>
    <?php endif; ?>
</div>