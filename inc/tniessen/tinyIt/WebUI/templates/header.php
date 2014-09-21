<!DOCTYPE html>
<html lang="<?php echo $r->escapeAttrOpt('language', 'en'); ?>">
    <head>
        <title><?php echo $r->escapeHtml($r->opt('title')); ?></title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <?php $r->includeCSS('bootstrap', 'dist/css/bootstrap.min.css'); ?>
        <?php $r->includeCSS('bootstrap', 'dist/css/bootstrap-theme.min.css'); ?>
        <?php $r->includeCSS('css/styles.css'); ?>
        <?php $r->includeScript('jquery', 'dist/jquery.min.js'); ?>
        <?php $r->includeScript('bootstrap', 'dist/js/bootstrap.min.js'); ?>
        <?php $r->includeScript('js/jsBind.js'); ?>
        <?php $r->includeScript('js/tinyIt.js'); ?>
    </head>
    <body role="document" class="<?php echo $r->escapeAttrOpt('bodyClass', ''); ?>">
        <?php if($r->opt('navigation')) $r->render('navigation'); ?>
        <div class="<?php echo $r->opt('fluidContainer') ? 'container-fluid' : 'container'; ?> <?php if($r->opt('containerLayout')) echo 'container-' . $r->opt('containerLayout'); ?>">