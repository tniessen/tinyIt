<?php $r->render('header', array('title' => 'Register', 'bodyClass' => 'registration-page', 'containerLayout' => 'super-narrow')); ?>
    <form action="<?php echo $r->escapeAttr($pageURL('register')); ?>" method="POST">
        <h1>Register</h1>
        <?php if($r->opt('errorMessage') != null) : ?>
            <div class="alert alert-danger">
                <?php echo $r->opt('errorMessage'); ?>
            </div>
        <?php endif; ?>
        <input type="text" class="form-control input-lg angular-bottom" name="username" value="<?php echo $r->escapeAttrOpt('current:username'); ?>" placeholder="User name" />
        <input type="text" class="form-control input-lg angular-top angular-bottom" name="email" value="<?php echo $r->escapeAttrOpt('current:email'); ?>" placeholder="e-mail" />
        <input type="password" class="form-control input-lg angular-top angular-bottom" name="password" placeholder="Password" />
        <input type="password" class="form-control input-lg angular-top" name="rptpassword" placeholder="Repeat password" />
        <input type="submit" class="btn btn-lg btn-primary btn-block prepend-vspace" value="Sign up" />
    </form>
    <div class="text-right prepend-vspace-tiny">
        <a href="<?php echo $r->escapeAttr($pageURL('login')); ?>">Login</a>
    </div>
<?php $r->render('footer'); ?>