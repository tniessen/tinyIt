<?php $r->render('dashboard', array('title' => 'Add wildcard')); ?>
    <div class="page-header">
        <h1>Add wildcard</h1>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <?php if($r->opt('errorMessage') != null) : ?>
                    <div class="alert alert-danger">
                        <?php echo $r->opt('errorMessage'); ?>
                    </div>
                <?php endif; ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php $r->render('links/addwildcardform'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $r->render('footer'); ?>