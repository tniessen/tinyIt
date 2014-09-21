<?php $r->render('header', array(
    'containerLayout' => 'narrow',
    'bodyClass' => $r->optAppend('bodyClass', 'installation-wizard', ' '),
    'title' => $r->opt('step') ? 'Installation - ' . $r->opt('step') : 'Installation'
)); ?>
    <div class="page-header">
        <h1>Installation <small><?php echo $r->escapeHtmlOpt('step', ''); ?></small></h1>
    </div>