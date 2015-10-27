<h1><?php echo $modules[0]->module_name; ?></h1>
<div class="user-textbox">
    <?php echo $modules[0]->module_description; ?> 
</div>
<h2>Assessment progress</h2>
<div class="user-textbox"> The progress of the assessment is shown in the current progression below. Completing questions will increase the percentage complete in the status bar below
    The progression of the "Overall Assessment Progress of <?php echo $modules[0]->module_name; ?>" bar is based on the subsections listed below.</div>
<div class="question-progress">
    <div>Overall Assessment Progress of <?php echo $modules[0]->module_name; ?></div>
    <div class="progressbar" style="width: 500px;">
        <div class="progressbar-fill" style="width: <?php echo round(getModuleProgress($modules[0]->module_id) * 100) * 5; ?>px;"></div>
        <div class="progressbar-text"><?php echo round(getModuleProgress($modules[0]->module_id) * 100) . "%"; ?></div>
    </div>
</div>
<br/><br/>
<?php for ($i = 0; $i < count($sections); $i++) : ?>
    <div class="question-progress">
        <div><?php echo $sections[$i]->section_name ?></div>
        <div class="progressbar" style="width: 500px;">
            <div class="progressbar-fill" style="width: <?php echo round(getSectionProgress($sections[$i]->section_id) * 100) * 5; ?>px;"></div>
            <div class="progressbar-text"><?php echo round(getSectionProgress($sections[$i]->section_id) * 100) . "%"; ?></div>
        </div>
    </div>
<?php endfor; ?>

<h2>Execution Class Progress</h2>
<div class="user-textbox">
    The Execution Class Progress shows the mixture of the different execution classes in the different modules and sections. It shows how many questions of a particular execution class are answered. 
    This should give an overview, how much work is left, to apply for the different execution classes. Note: For an application in an execution class all lower execution class questions have to be answered.
</div>
<?php $exclasses = getExecutiondata(); ?>
<div id="exdia">
    <div class="exdia_data">   
        <div class="exdia_description">Fabrication</div>
        <div class="ex_wrapper">
            <?php for ($i = 0; $i < count($exclasses); $i++) : ?>
                <?php if (exClasssExists($modules[0]->module_id, $exclasses[$i]->exclass_sort) == true) : ?>
                    <div class="ex_bar">
                        <div class="ex_bar_element" style="min-height: 20px; height: <?php echo round(getModuleProgress($modules[0]->module_id, $exclasses[$i]->exclass_sort) * 100) * 3; ?>px;"><?php echo round(getModuleProgress($modules[0]->module_id, $exclasses[$i]->exclass_sort) * 100); ?>%</div>
                    </div>
                <?php else : ?>
                    <div class="ex_bar">
                        <div class="ex_bar_element" style="height:300px;">No<br/>quest.<br/>in<br/>class</div>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    </div>
    <div class="ex_description">
        <div class="ex_wrapper">
            <?php for ($i = 0; $i < count($exclasses); $i++) : ?>
                <div class="ex_bar">
                    <div class="ex_bar_description">EXC <?php echo $exclasses[$i]->exclass_sort; ?></div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>
<h2>Revision needed</h2>
<div class="user-textbox">
    This part of the statistics shows if a revision in any section is required. A statistic is only possible if every question in the assessment progress is answered.
</div>
<div id="revision">
    <?php for ($i = 0; $i < count($sections); $i++) : ?>
        <div class="revision-section <?php if (($SectionRevision = checkforRevision(1, $sections[$i]->section_id)) == 0) : ?>revision-needed-box<?php elseif ($SectionRevision == 1) : ?>revision-not-needed-box<?php elseif ($SectionRevision == 2) : ?>revision-not-possible-box<?php endif; ?>">
            <div style="display: table-row;">
                <div style="display: table-cell; vertical-align: middle;"><?php echo $sections[$i]->section_name ?></div>
                <?php if($SectionRevision == 0) : ?>
                <div class="revision-needed-text"><a href="revision.php?type=1&moduleid=<?php echo $_GET['umoduleid'] ?>&sectionid=<?php echo $sections[$i]->section_id ?>">Revision needed</a></div>
                <?php elseif ($SectionRevision == 1) : ?>
                <div class="revision-not-needed-text">No revision needed</div>
                <?php elseif ($SectionRevision == 2) : ?>
                <div class="revision-not-possible-text">Not all question processed yet</div>
                <?php endif; ?>
            </div>
        </div>
        <?php for ($j = 0; $j < count($subsections = getSubsections(TRUE, $sections[$i]->section_id)); $j++) : ?>
            <div class="revision-subsection <?php if (($SubectionRevision = checkforRevision(2, $subsections[$j]->subsection_id)) == 0) : ?>revision-needed-box<?php elseif ($SubectionRevision == 1) : ?>revision-not-needed-box<?php elseif ($SubectionRevision == 2) : ?>revision-not-possible-box<?php endif; ?>">
                <div style="display: table-row;">
                    <div style="display: table-cell; vertical-align: middle;"><?php echo $subsections[$j]->subsection_name ?></div>
                    <?php if($SubectionRevision == 0) : ?>
                <div class="revision-needed-text"><a href="revision.php?type=2&moduleid=<?php echo $_GET['umoduleid'] ?>&sectionid=<?php echo $sections[$i]->section_id ?>&subsectionid=<?php echo $subsections[$j]->subsection_id ?>">Revision needed</a></div>
                <?php elseif ($SubectionRevision == 1) : ?>
                <div class="revision-not-needed-text">No revision needed</div>
                <?php elseif ($SubectionRevision == 2) : ?>
                <div class="revision-not-possible-text">Not all question processed yet</div>
                <?php endif; ?>
                </div>
            </div>
        <?php endfor; ?>
    <?php endfor; ?>
</div>