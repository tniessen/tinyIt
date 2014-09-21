<?php $r->render('dashboard', array(
    'bodyClass' => $r->optAppend('bodyClass', 'settings-page', ' '),
    'title'     => $r->opt('section') ? 'Settings - ' . $r->opt('section') : 'Settings'
)); ?>
    <div class="row settings">
        <div class="col-md-3 settings-nav-col">
            <ul class="settings-nav nav nav-pills nav-stacked">
                <?php $an = $r->opt('section'); ?>
                <?php if($page->hasPermission('settings.change_site_settings')) : ?>
                    <li class="<?php if($an === 'General') echo 'active'; ?>"><a href="<?php echo $r->escapeAttr($pageURL('settings/site/general')); ?>">General</a></li>
                    <li class="<?php if($an === 'Links') echo 'active'; ?>"><a href="<?php echo $r->escapeAttr($pageURL('settings/site/links')); ?>">Links</a></li>
                    <li class="<?php if($an === 'Users') echo 'active'; ?>"><a href="<?php echo $r->escapeAttr($pageURL('settings/site/users')); ?>">Users</a></li>
                <?php endif; ?>
                <li class="<?php if($an === 'Account') echo 'active'; ?>"><a href="<?php echo $r->escapeAttr($pageURL('settings/own/account')); ?>">Account</a></li>
            </ul>
        </div>
        <div class="col-md-9 settings-body">
            <div class="page-header">
                <h1>Settings <small><?php echo $r->escapeHtmlOpt('section', ''); ?></small></h1>
            </div>
            <?php if($r->opt('errorMessage') != null) : ?>
                <div class="alert alert-danger">
                    <?php echo $r->opt('errorMessage'); ?>
                </div>
            <?php endif; ?>