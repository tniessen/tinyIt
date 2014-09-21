<?php $r->render('dashboard', array('title' => 'Group details')); ?>
    <?php if($g = $r->opt('groupInfo')) : ?>
        <div class="page-header">
            <h1>Group details <small><?php echo $r->escapeHtml($g->name); ?></small></h1>
        </div>
        <?php if($r->opt('errorMessage') != null) : ?>
            <div class="alert alert-danger">
                <?php echo $r->opt('errorMessage'); ?>
            </div>
        <?php endif; ?>
        <form class="form-horizontal" <?php if($r->opt('editMode')) : ?> method="POST" action="<?php echo $r->escapeAttr($pageURL('groups/details', array('group' => $g->id, 'edit' => true))); ?>" <?php endif; ?>>
            <div class="form-group">
                <label class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <?php if($r->opt('editMode')) : ?>
                        <input type="text" value="<?php echo $r->escapeAttr($g->name); ?>" name="group_name" class="form-control" />
                    <?php else : ?>
                        <p class="form-control-static"><?php echo $r->escapeHtml($g->name); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Members</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><?php echo $r->escapeHtml($g->nMembers); ?></p>
                </div>
            </div>
            <hr />
            <a href="<?php
                $params = array(
                    'group' => $g->id,
                    'edit'  => !$r->opt('editMode')
                );
                $url = $pageURL('groups/details', $params);
                echo $r->escapeAttr($url);
            ?>" class="btn btn-default"><?php
                if($r->opt('editMode')) : ?>
                    <span class="glyphicon glyphicon-remove"></span> Cancel
                <?php else : ?>
                    <span class="glyphicon glyphicon-pencil"></span> Edit
                <?php endif;
            ?></a>
            <?php if($r->opt('editMode')) : ?>
                <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-ok"></span> Save</button>
            <?php else : ?>
                <a class="btn btn-default" href="<?php echo $r->escapeAttr($pageURL('groups/permissions', array('group' => $g->id))); ?>">
                    <span class="glyphicon glyphicon-lock"></span> Permissions
                </a>
            <?php endif; ?>
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target=".confirm-deletion-modal">
                <span class="glyphicon glyphicon-trash"></span> Delete
            </button>
        </form>
        <div class="modal fade confirm-deletion-modal" tabindex="-1" role="dialog" aria-hidden="true" data-jsbind="tinyIt.GroupDetails.DeleteGroupModal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Delete group</h4>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete the group <em><?php echo $r->escapeHtml($g->name); ?></em>?</p>
                        <p>Move affected users to this group:</p>
                        <p>
                            <select class="group-select form-control" size="1">
                                <option value="0" selected>── No group ──</option>
                                <?php foreach(($gs = $r->opt('availableGroups')) as $group) : ?>
                                    <?php if($group->id != $g->id) : ?>
                                        <option value="<?php echo $group->id; ?>"><?php echo $r->escapeHtml($group->name); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <a href="<?php
                            $params = array(
                                'group' => $g->id,
                                'delete' => true,
                                'nonce' => $theNonce
                            );
                            $url = $pageURL('groups/details', $params);
                            echo $r->escapeAttr($url);
                        ?>" class="set-group-link btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="page-header">
            <h1>Group details</h1>
        </div>
        <div class="alert alert-danger">
            Group not found
        </div>
    <?php endif; ?>
<?php $r->render('footer'); ?>