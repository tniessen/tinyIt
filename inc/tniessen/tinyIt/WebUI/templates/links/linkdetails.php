<?php $r->render('dashboard', array('title' => 'Link details')); ?>
    <?php if($l = $r->opt('linkInfo')) : ?>
        <div class="page-header">
            <h1>Link details <small><?php echo $r->escapeHtml($l->path); ?></small></h1>
        </div>
        <?php if($r->opt('errorMessage') != null) : ?>
            <div class="alert alert-danger">
                <?php echo $r->opt('errorMessage'); ?>
            </div>
        <?php endif; ?>
        <form class="form-horizontal" <?php if($r->opt('editMode')) echo 'method="POST" action="' . $r->escapeAttr($pageURL('links/details', array('link' => $l->id, 'edit' => true))) . '" data-jsbind="tinyIt.LinkDetails.Edit"'; ?>>
            <div class="form-group link-type">
                <label class="col-sm-1 control-label">Type</label>
                <div class="col-sm-11">
                    <p class="form-control-static"><?php
                        switch($l->type) {
                        case 'regex':
                            $vtname = 'Wildcard';
                            $vticon = 'certificate';
                            break;
                        case 'static':
                            $vtname = 'Static';
                            $vticon = 'link';
                            break;
                        default:
                            $vtname = $r->escapeHtml($l->type);
                            $vticon = 'warning-sign';
                        }
                        echo '<span class="glyphicon glyphicon-' . $vticon . '"></span> ' . $vtname;
                    ?></p>
                </div>
            </div>
            <div class="form-group link-path">
                <label class="col-sm-1 control-label">Path</label>
                <div class="col-sm-11">
                    <?php if($r->opt('editMode')) : ?>
                        <input type="text" value="<?php echo $r->escapeAttrOpt('current:link_path', $l->path); ?>" name="link_path" class="form-control" <?php if(!$page->hasPermission('link.custom')) echo 'disabled=""'; ?> />
                        <?php if($r->opt('allowOverrideWildcards')) : ?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="override_wildcards" <?php if($r->opt('current:override_wildcards')) echo 'checked'; ?>>
                                    Override wildcards
                                </label>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <p class="form-control-static"><tt><?php echo $r->escapeHtml($l->path); ?></tt></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if(isset($l->fullURL)) : ?>
                <div class="form-group link-full-url">
                    <label class="col-sm-1 control-label">URL</label>
                    <div class="col-sm-11">
                        <p class="form-control-static"><?php echo $r->escapeHtml($l->fullURL); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group link-target">
                <label class="col-sm-1 control-label">Target</label>
                <div class="col-sm-11">
                    <?php if($r->opt('editMode')) : ?>
                        <input type="<?php echo ($l->type === 'static') ? 'url' : 'text'; ?>" value="<?php echo $r->escapeAttrOpt('current:link_target', $l->target); ?>" name="link_target" class="form-control" />
                    <?php else : ?>
                        <p class="form-control-static"><?php
                            if($l->type === 'static') {
                                echo '<a href="' . $r->escapeAttr($l->target) . '">' . $r->escapeHtml($l->target) . '</a>';
                            } else {
                                echo $r->escapeHtml($l->target);
                            }
                        ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if($l->type === 'regex') : ?>
                <div class="form-group">
                    <label class="col-sm-1 control-label" for="link_priority">Priority:</label>
                    <div class="col-sm-11">
                        <?php if($r->opt('editMode')) : ?>
                            <input type="text" class="form-control" name="link_priority" placeholder="(default: 100)" value="<?php echo $r->escapeAttrOpt('current:link_priority', $l->priority); ?>" />
                        <?php else : ?>
                            <p class="form-control-static"><?php echo $l->priority; ?></p>
                        <?php endif; ?>
                        <p class="help-block">If multiple wildcards match the requested URL, the wildcard with the highest priority will be used (default priority is 100).</p>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label class="col-sm-1 control-label">Owner</label>
                <div class="col-sm-11">
                    <p class="form-control-static"><?php
                        if($l->userInfo) {
                            $usrurl = $pageURL('users/details', array('user' => $l->owner_id));
                            echo '<a href="' . $usrurl . '">' . $r->escapeHtml($l->userInfo->display_name) . '</a>';
                        } else {
                            echo 'Unknown (' . $l->owner_id . ')';
                        }
                    ?></p>
                </div>
            </div>
            <hr />
            <a href="<?php
                $params = array(
                    'link' => $l->id,
                    'edit' => !$r->opt('editMode')
                );
                $url = $pageURL('links/details', $params);
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
            <?php endif; ?>
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target=".confirm-deletion-modal">
                <span class="glyphicon glyphicon-trash"></span> Delete
            </button>
            <a href="<?php echo $r->escapeAttr($pageURL('tools/qr-code', array(
                'qr-data' => isset($l->fullURL) ? $l->fullURL : \tniessen\tinyIt\Application::getBaseURL()->build()
            ))); ?>" class="btn btn-info">
                <span class="glyphicon glyphicon-qrcode"></span> QR code
            </a>
        </form>
        <div class="modal fade confirm-deletion-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Delete link</h4>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete this link?</p>
                        <p><code><?php echo $r->escapeHtml($l->path); ?></code> redirecting to <code><?php echo $r->escapeHtml($l->target); ?></code></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <a href="<?php
                            $params = array(
                                'link' => $l->id,
                                'delete' => true,
                                'nonce' => \tniessen\tinyIt\Security\Authorization::getNonce()
                            );
                            $url = $pageURL('links/details', $params);
                            echo $r->escapeAttr($url);
                        ?>" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="page-header">
            <h1>Link details</h1>
        </div>
        <div class="alert alert-danger">
            Link not found
        </div>
    <?php endif; ?>
<?php $r->render('footer'); ?>