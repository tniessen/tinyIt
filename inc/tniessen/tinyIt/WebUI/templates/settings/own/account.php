<?php $r->render('settings/header', array('section' => 'Account')); ?>
    <?php $u = $r->opt('userInfo'); ?>
    <form method="POST" action="<?php echo $r->escapeAttr($pageURL('settings/own/account')); ?>" class="form-horizontal">
        <div class="panel panel-info">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label">User name</label>
                    <div class="col-sm-9">
                        <?php if($r->opt('allowNameChange')) : ?>
                            <input type="text" value="<?php echo $r->escapeAttr($u->name); ?>" name="username" class="form-control" maxlength="20" />
                        <?php else : ?>
                            <p class="form-control-static"><?php echo $r->escapeHtml($u->name); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Public name</label>
                    <div class="col-sm-9">
                        <?php if($r->opt('allowDisplayNameChange')) : ?>
                            <input type="text" class="form-control" name="display_name" value="<?php echo $r->escapeAttr($u->display_name); ?>" maxlength="32" />
                        <?php else : ?>
                            <p class="form-control-static"><?php echo $r->escapeHtml($u->display_name); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Email</label>
                    <div class="col-sm-9">
                        <?php if($r->opt('allowEmailChange')) : ?>
                            <input type="text" class="form-control" name="email" value="<?php echo $r->escapeAttr($u->email); ?>" maxlength="40" />
                        <?php else : ?>
                            <p class="form-control-static"><?php echo $r->escapeHtml($u->email); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Avatar</h3>
            </div>
            <div class="panel-body">
                <div class="form-group text-center">
                    <a class="btn btn-primary" href="http://gravatar.com/connect" target="_blank">
                        <span class="glyphicon glyphicon-edit"></span>
                        Change your Gravatar
                    </a>
                    <a class="btn btn-default" href="http://gravatar.com/support/what-is-gravatar/" target="_blank">
                        <span class="glyphicon glyphicon-question-sign"></span>
                        Learn more about Gravatar
                    </a>
                </div>
            </div>
        </div>
        <div class="text-center">
            <input type="submit" class="btn btn-lg btn-success" value="Save" />
        </div>
    </form>
<?php $r->render('settings/footer'); ?>