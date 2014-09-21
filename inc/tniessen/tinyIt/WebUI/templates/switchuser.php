<?php $r->render('dashboard', array('title' => 'Switch user')); ?>
    <?php if($u = $r->opt('userInfo')) : ?>
        <div class="page-header">
            <h1>Switch user</h1>
        </div>
        <?php if($r->opt('errorMessage') != null) : ?>
            <div class="alert alert-danger">
                <?php echo $r->opt('errorMessage'); ?>
            </div>
        <?php endif; ?>
        <p>Click the button below to switch to the user account of <b><?php echo $r->escapeHtml($u->display_name); ?></b>.</p>
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">User switching</h3>
            </div>
            <div class="panel-body">
                <p>tinyIt does not allow every user to access everything,
                especially not if it belongs to another user. Although
                adminstrators can gain access to most functions through
                permissions, it can be useful to temporarily login as another
                user.</p>
                <p>You will <strong>not</strong> inherit the permissions of the
                account you are switching to.</p>
                <p>This function is provided for administrative tasks only as
                it bypasses common authorization.</p>
            </div>
        </div>
        <a href="<?php
            $params = array(
                'user' => $u->id,
                'nonce' => $theNonce,
                'confirmed' => 1
            );
            $url = $pageURL('switch-user', $params);
            echo $r->escapeAttr($url);
        ?>" class="btn btn-success">
            <span class="glyphicon glyphicon-arrow-right"></span> Switch
        </a>
        <a href="<?php echo $r->escapeAttr($pageURL('home')); ?>" class="btn btn-danger">
            <span class="glyphicon glyphicon-remove"></span> Cancel
        </a>
    <?php else : ?>
        <div class="page-header">
            <h1>Switch user</h1>
        </div>
        <div class="alert alert-danger">
            User not found
        </div>
        <p>The user you attempted to switch to does not exist.</p>
    <?php endif; ?>
<?php $r->render('footer'); ?>