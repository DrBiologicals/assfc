<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $pageTitle . ' - ' . $pageNavi; ?></title>
        <style type="text/css">
            @import url(<?php echo $templateDir . 'style.css'; ?>);
        </style>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    </head>
    <body>
        <h2>Help for Question:</h2>
        <h1><?php echo htmlspecialchars($questiontmp[0]->question_name); ?></h1>
        <hr>
        <div class="question-description">
            <?php echo getHtml($questiontmp[0]->question_help); ?>
            <?php if ($errorNoHelp == TRUE && $errorNoReference == TRUE) : ?>
                Sorry. Right now there is no help file for this question.<br/> Please contact us for further information.
            <?php endif; ?>
                <?php if ($errorNoReference != TRUE) : ?>
                For additional information, please consult the reference at <p style="font-weight: bold;"><?php echo $questiontmp[0]->question_reference; ?></p>  or contact us.
                <?php endif; ?>
        </div>
    </body>
</html>