<?php if(!isset($_GET['action']) ) : ?>
<h1>Select Module and Execution Class</h1>
<div id="user-textbox">
    This system leads you step by step trough the verification process. It is not necessary to complete this verification at once. You can always save or undo your input.
</div>
<form action="verification.php?mode=main" method="post">
    <div id="test" style="margin-top: 20px; position: relative">
        <h2>Step 1: Which Module do you wish to verify?</h2>
        <div id="user-textbox">
            The first Question is, which Module of your Company do you want to verify? This decission is not binding. It limits the questions to the Module you need. This makes the process as fast as possible for you. 
            You can always change the Module selection by clicking on "Verification".
        </div>
        <?php for ($i = 0; $i < count($modules); $i++) : ?>
            <div id="user-module">
                <h2><?php echo $modules[$i]->module_name ?></h2>
                <input type="checkbox" style="position: absolute; top: 5px; right: 10px;" name="modulescheckbox[]" value="<?php echo $modules[$i]->module_id; ?>" <?php if(checklist($modules[$i]->module_id, $modulesettings) == '1') : ?>CHECKED<?php endif; ?>>
                <div id="user-textbox">
                    <?php echo $modules[$i]->module_description; ?>
                </div>
                <h3>This Module contains:</h3>
                <ul>
                    <?php for ($j = 0; $j < count($sections[$i]); $j++) : ?>
                        <li><?php echo $sections[$i][$j]->section_name; ?></li>
                    <?php endfor; ?>
                </ul>
            </div>
        <?php endfor; ?>
    </div> 
    <h2>Step 2: Which Execution Class do you want?</h3>
        <div id="user-textbox">
            The information is important, that the verification system can limit the questions to the execution class you want. If you don't know which execution class you need or want, choose the highest execution class. The system will show you at the end of the process which execution class is the best for you.
            You can always change the Module selection by clicking on "Verification".
        </div>
        <div id="execution">
            <?php for ($i = 0; $i < count($exclasses); $i++) : ?>
                <div id="user-module" style="width: auto;height: auto;text-align: center; margin-left: 20px; margin-right: 20px;font-weight: bold;">
                    <input type="radio" name="execution" value="<?php echo $exclasses[$i]->exclass_id; ?>" <?php if($userexclass == $exclasses[$i]->exclass_id) : ?>CHECKED<?php endif; ?>>
                    <?php echo $exclasses[$i]->exclass_name; ?>
                </div>
            <?php endfor; ?>
            <div style="clear:both;"></div>            
        </div>         
        <input type="submit" style="width:70px;height:35px;"value="Start" name="next">
</form>
<?php endif; if($_GET['action'] == 'success') : ?>
<h1>Module update Successful</h1>
<div id="user-textbox">
    You have now selected the Modules. You can find these Modules in the menu on the left. Choose a Module and click on it to start.
</div>
<?php endif; ?>
