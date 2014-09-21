<?php $r->render('installation/header', array('step' => 'Initialize database')); ?>
    <?php if($r->opt('errorMessage') != null) : ?>
        <div class="alert alert-danger">
            <?php echo $r->opt('errorMessage'); ?>
        </div>
    <?php endif; ?>
    <form method="POST" class="init-database-form" action="<?php
        echo $r->escapeAttr($pageURL('installation/database'));
    ?>">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">User</h3>
            </div>
            <div class="panel-body">
                <p>Use this form to add the first user to your site. Remember
                   the password, you will need it to login later.</p>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">Name</span>
                    <input type="text" class="form-control" name="username" value="<?php echo $r->escapeAttrOpt('current:username'); ?>" />
                </div>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">Password</span>
                    <input type="password" class="form-control" name="password" value="" />
                </div>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">Repeat password</span>
                    <input type="password" class="form-control" name="rptpassword" value="" />
                </div>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">Email</span>
                    <input type="text" class="form-control" name="email" value="<?php echo $r->escapeAttrOpt('current:email'); ?>" />
                </div>
            </div>
        </div>
        <div class="text-center">
            <input type="submit" class="btn btn-lg btn-success" value="Continue" />
        </div>
    </form>
<?php $r->render('footer'); ?>