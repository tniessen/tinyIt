<?php $r->render('installation/header', array('step' => 'Configuration')); ?>
    <?php if($r->opt('errorMessage') != null) : ?>
        <div class="alert alert-danger">
            <?php echo $r->opt('errorMessage'); ?>
        </div>
    <?php endif; ?>
    <form method="POST" class="create-configuration-form" action="<?php
        echo $r->escapeAttr($pageURL('installation/config'))
    ?>">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Database</h3>
            </div>
            <div class="panel-body">
                <p>tinyIt requires a MySQL database. Please create a database now or use an
                   existing database.</p>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">Server</span>
                    <input type="text" class="form-control" name="server" value="<?php echo $r->escapeAttrOpt('current:server', '127.0.0.1'); ?>" placeholder="MySQL server" />
                </div>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">Port</span>
                    <input type="text" class="form-control" name="port" value="<?php echo $r->escapeAttrOpt('current:port', '3306'); ?>" placeholder="MySQL port (3306)" />
                </div>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">Username</span>
                    <input type="text" class="form-control" name="username" value="<?php echo $r->escapeAttrOpt('current:username'); ?>" placeholder="MySQL user" />
                </div>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">Password</span>
                    <input type="password" class="form-control" name="password" value="<?php echo $r->escapeAttrOpt('current:password'); ?>" placeholder="MySQL password" />
                </div>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">Database</span>
                    <input type="text" class="form-control" name="dbname" value="<?php echo $r->escapeAttrOpt('current:dbname'); ?>" placeholder="Database name" />
                </div>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">Table prefix</span>
                    <input type="text" class="form-control" name="tblprefix" value="<?php echo $r->escapeAttrOpt('current:tblprefix'); ?>" placeholder="Table name prefix (optional)" />
                </div>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Server</h3>
            </div>
            <div class="panel-body">
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">Admin path</span>
                    <input type="text" class="form-control" name="adminpath" value="<?php echo $r->escapeAttrOpt('current:adminpath', '~'); ?>" placeholder="Administration path" />
                </div>
            </div>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Advanced</h3>
            </div>
            <div class="panel-body">
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">.htaccess key</span>
                    <input type="text" class="form-control" name="requrlkey" value="<?php
                        echo $r->escapeAttrOpt('current:requrlkey', chr( mt_rand(ord('a'), ord('f')) ) . substr( md5(microtime()), 0, 19 ));
                    ?>" placeholder="Secret key used in .htaccess file" />
                </div>
            </div>
        </div>
        <div class="text-center">
            <input type="submit" class="btn btn-lg btn-success" value="Continue" />
        </div>
    </form>
<?php $r->render('footer'); ?>