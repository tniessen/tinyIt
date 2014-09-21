<?php $r->render('dashboard', array('title' => 'Home')); ?>
    <div class="page-header">
        <h1>Welcome <small><?php echo $r->escapeHtml(\tniessen\tinyIt\Security\Authorization::user()->display_name); ?></small></h1>
    </div>
    <div class="jumbotron">
        <h1>What do you want to do?</h1>
        <p>Choose one of these possibilities to begin or use the navigation menu at the top.</p>
        <ul>
            <li><a href="<?php echo $r->escapeAttr($pageURL('links/shorten')); ?>">Shorten a link</a></li>
            <li><a href="<?php echo $r->escapeAttr($pageURL('links/list')); ?>">View shortened links</a></li>
            <li><a href="<?php echo $r->escapeAttr($pageURL('users/list')); ?>">Manage users</a></li>
            <li><a href="<?php echo $r->escapeAttr($pageURL('settings/own/account')); ?>">Change account settings</a></li>
        </ul>
    </div>
    <div class="row">
        <?php if($page->hasPermission('link.shorten')) : ?>
            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Shorten a link</h3>
                    </div>
                    <div class="panel-body">
                        <?php $r->render('links/shortenlinkform'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if($page->hasPermission('link.add_wildcard')) : ?>
            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Add a wildcard</h3>
                    </div>
                    <div class="panel-body">
                        <?php $r->render('links/addwildcardform'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php $r->render('footer'); ?>