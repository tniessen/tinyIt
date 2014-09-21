<?php $r->render('settings/header', array('section' => 'General')); ?>
    <form method="POST" action="<?php echo $r->escapeAttr($pageURL('settings/site/general')); ?>" class="form-horizontal">
        <div class="panel panel-info" data-jsbind="tinyIt.Settings.General.HomePage">
            <div class="panel-heading">
                <h3 class="panel-title">Home page</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label">Action</label>
                    <div class="col-sm-9">
                        <?php $ha = $r->opt('current:home_action', $r->opt('setting:home_action')); ?>
                        <!-- <?php var_dump($ha); ?> -->
                        <select class="form-control" name="home_action">
                            <option value="show_admin" <?php if($ha === 'show_admin') echo 'selected'; ?>>Show dashboard</option>
                            <option value="redirect" <?php if($ha === 'redirect') echo 'selected';?>>Redirect</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Target (redirect)</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="home_target" value="<?php echo $r->escapeAttrOpt('current:home_target', $r->opt('setting:home_target')); ?>" />
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <input type="submit" class="btn btn-lg btn-success" value="Save" />
        </div>
    </form>
<?php $r->render('settings/footer'); ?>