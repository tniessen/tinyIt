<?php $r->render('dashboard', array('title' => 'No permission')); ?>
    <div class="page-header">
        <h1>You are not permitted to access this page</h1>
    </div>
    <p>You need the permission <code><?php echo $r->escapeHtml($r->opt('missingPermission')); ?></code> to access this page.</p>
<?php $r->render('footer'); ?>