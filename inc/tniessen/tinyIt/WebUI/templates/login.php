<?php $r->render('header', array('title' => 'Login', 'bodyClass' => 'login-page', 'containerLayout' => 'super-narrow')); ?>
    <form action="<?php echo $r->escapeAttr($pageURL('login')); ?>" method="POST">
        <h1>Login</h1>
        <?php if($r->opt('errorMessage') != null) : ?>
            <div class="alert alert-danger">
                <?php echo $r->opt('errorMessage'); ?>
            </div>
        <?php endif; ?>
        <input type="text" class="form-control input-lg angular-bottom" name="username" placeholder="User name" />
        <input type="password" class="form-control input-lg angular-top" name="password" placeholder="Password" />
        <input type="submit" class="btn btn-lg btn-primary btn-block prepend-vspace" value="Sign in" />
    </form>
    <?php if($r->opt('allowRegistration')) : ?>
        <div class="text-right prepend-vspace-tiny">
            <a href="<?php echo $r->escapeAttr($pageURL('register')); ?>">Register</a>
        </div>
    <?php endif; ?>
<?php $r->render('footer'); ?>