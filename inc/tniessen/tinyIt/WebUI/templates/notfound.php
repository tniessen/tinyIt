<?php if($theUser) : ?>
    <?php $r->render('dashboard', array('title' => 'Not found')); ?>
<?php else : ?>
    <?php $r->render('header', array('title' => 'Not found')); ?>
<?php endif; ?>
    <div class="page-header">
        <h1>The requested content could not be found</h1>
    </div>
    <p>The link you tried does not point to any existing content.</p>
<?php $r->render('footer'); ?>