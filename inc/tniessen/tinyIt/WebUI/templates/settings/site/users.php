<?php $r->render('settings/header', array('section' => 'Users')); ?>
    <form method="POST" action="<?php echo $r->escapeAttr($pageURL('settings/site/users')); ?>" class="form-horizontal">
        <input type="hidden" name="form_submitted" value="1" />
        <div class="panel panel-info" data-jsbind="tinyIt.Settings.Users.Registration">
            <div class="panel-heading">
                <h3 class="panel-title">Registration</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label">Allow registration</label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <input type="checkbox" name="allow_registration" value="1" <?php if($r->escapeAttrOpt('current:allow_registration', $r->opt('setting:allow_registration'))) echo 'checked'; ?> />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Default group</label>
                    <div class="col-sm-9">
                        <!-- <?php echo $r->escapeAttrOpt('current:registration_user_group', $r->opt('setting:registration_user_group')); ?> -->
                        <select name="registration_user_group" class="form-control" size="1">
                            <option value="0" <?php if($r->escapeAttrOpt('current:registration_user_group', $r->opt('setting:registration_user_group')) == 0) echo 'selected'; ?>>── No group ──</option>
                            <?php foreach(($gs = $r->opt('availableGroups')) as $group) : ?>
                                <option value="<?php echo $group->id; ?>" <?php if($r->escapeAttrOpt('current:registration_user_group', $r->opt('setting:registration_user_group')) == $group->id) echo 'selected'; ?>><?php echo $r->escapeHtml($group->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Account and profile</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label">Allow name changes</label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <input type="checkbox" name="allow_name_changes" value="1" <?php if($r->escapeAttrOpt('current:allow_name_changes', $r->opt('setting:allow_name_changes'))) echo 'checked'; ?> />
                        </div>
                        <p class="help-block">Allows users to change their username. This does <b>not</b> affect changes of the display name.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <input type="submit" class="btn btn-lg btn-success" value="Save" />
        </div>
    </form>
<?php $r->render('settings/footer'); ?>