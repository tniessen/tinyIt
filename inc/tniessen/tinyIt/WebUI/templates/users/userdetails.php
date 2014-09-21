<?php $r->render('dashboard', array('title' => 'User details', 'bodyClass' => 'user-details')); ?>
    <?php if($u = $r->opt('userInfo')) : ?>
        <div class="page-header">
            <h1>User details <small><?php echo $r->escapeHtml($u->name); ?></small></h1>
        </div>
        <?php if($r->opt('errorMessage') != null) : ?>
            <div class="alert alert-danger">
                <?php echo $r->opt('errorMessage'); ?>
            </div>
        <?php endif; ?>
        <div class="form-horizontal">
            <div class="form-group user-header">
                <div class="col-sm-3 user-image">
                    <img src="<?php
                        $url = \tniessen\tinyIt\Gravatar::getURL($u->email, true, 150);
                        echo $r->escapeAttr($url->build());
                    ?>" />
                </div>
                <div class="col-sm-9 user-title">
                    <h2><?php echo $r->escapeHtml($u->display_name); ?></h2>
                </div>
            </div>
            <hr />
            <div class="form-group">
                <label class="col-sm-2 col-sm-offset-2 control-label">Member since</label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?php echo $r->escapeHtml(date('r', $u->registered)); ?></p>
                </div>
            </div>
            <?php if($g = $r->opt('groupInfo')) : ?>
                <label class="col-sm-2 col-sm-offset-2 control-label">Group</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <a href="<?php echo $r->escapeAttr($pageURL('groups/details', array('group' => $g->id))); ?>">
                            <?php echo $r->escapeHtml($g->name); ?></p>
                        </a>
                    </p>
                </div>
            <?php endif; ?>
            <hr />
            <?php if($theUser->id === $u->id) : ?>
                <a href="<?php echo $r->escapeAttr($pageURL('settings/own/account')); ?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-pencil"></span> Edit
                </a>
            <?php endif; ?>
            <?php if($r->opt('canSwitchUser')) : ?>
                <a href="<?php
                    $params = array(
                        'user' => $u->id,
                        'nonce' => \tniessen\tinyIt\Security\Authorization::getNonce()
                    );
                    $url = $pageURL('switch-user', $params);
                    echo $r->escapeAttr($url);
                ?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-arrow-right"></span> Switch
                </a>
            <?php endif; ?>
            <?php if($page::hasPermission('user.set_group')) : ?>
                <button type="button" class="btn btn-default" data-toggle="modal" data-target=".select-group-modal">
                    Set group
                </button>
            <?php endif; ?>
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target=".confirm-deletion-modal">
                <span class="glyphicon glyphicon-trash"></span> Delete
            </button>
        </div>
        <div class="modal fade confirm-deletion-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Delete account</h4>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete the user account <em><?php echo $r->escapeHtml($u->name); ?></em>?</p>
                        <p>All associated links will be deleted.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <a href="<?php
                            $params = array(
                                'user' => $u->id,
                                'delete' => true,
                                'nonce' => $theNonce
                            );
                            $url = $pageURL('users/details', $params);
                            echo $r->escapeAttr($url);
                        ?>" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade select-group-modal" tabindex="-1" role="dialog" aria-hidden="true" data-jsbind="tinyIt.UserDetails.SelectGroupModal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Assign group to user</h4>
                    </div>
                    <div class="modal-body">
                        <p>Select the group to assign to this user:</p>
                        <p>
                            <select class="group-select form-control" size="1">
                                <option value="0" <?php if($u->group_id == 0) echo 'selected'; ?>>── No group ──</option>
                                <?php foreach(($gs = $r->opt('availableGroups')) as $group) : ?>
                                    <option value="<?php echo $group->id; ?>" <?php if($u->group_id == $group->id) echo 'selected'; ?>><?php echo $r->escapeHtml($group->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <a href="<?php
                            $params = array(
                                'user' => $u->id,
                                'nonce' => $theNonce
                            );
                            $url = $pageURL('users/details', $params);
                            echo $r->escapeAttr($url);
                        ?>" class="set-group-link btn btn-primary">Set group</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="page-header">
            <h1>User details</h1>
        </div>
        <div class="alert alert-danger">
            User not found
        </div>
    <?php endif; ?>
<?php $r->render('footer'); ?>