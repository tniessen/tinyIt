<?php $r->render('installation/header', array('showProgress' => false)); ?>
    <div class="jumbotron installation-intro">
        <h1>Welcome to tinyIt</h1>
        <p>tinyIt is a minimal URL shortening application. Before you can use
           the awesomeness, here are some installation steps you'll have to
           follow. Don't worry, it won't take more than a few minutes.</p>
        <a class="btn btn-lg btn-success" href="<?php echo $r->escapeAttr($pageURL('installation/config')); ?>">Start installation</a>
    </div>
    <div class="alert alert-warning">
        The installation process requires write access in the directory
        <code><?php echo $r->escapeHtml(\tniessen\tinyIt\Application::$rootDir); ?></code>
        to create necessary files. Please ensure to set at least read+write permissions
        to the above directory before continuing.
    </div>
<?php $r->render('footer'); ?>