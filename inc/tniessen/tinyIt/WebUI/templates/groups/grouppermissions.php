<?php $r->render('dashboard', array('title' => 'Group permissions')); ?>
    <?php if($g = $r->opt('groupInfo')) : ?>
        <div class="page-header">
            <h1>Permissions <small>Group: <?php echo $r->escapeHtml($g->name); ?></small></h1>
        </div>
        <?php if($r->opt('errorMessage') != null) : ?>
            <div class="alert alert-danger">
                <?php echo $r->opt('errorMessage'); ?>
            </div>
        <?php endif; ?>
        <div class="permissions">
            <?php $allperms = \tniessen\tinyIt\Security\Permissions::$all; ?>
            <?php if(($perms = $r->opt('groupPermissions')) && count($perms)) : ?>
                <ul class="list-group">
                    <?php foreach($perms as $perm) : ?>
                        <li class="list-group-item">
                            <h4 class="list-group-item-heading">
                                <?php if(isset($allperms[$perm])) : ?>
                                    <span class="label label-default"><?php echo $r->escapeHtml($allperms[$perm]['cat']); ?></span>
                                    <?php echo $r->escapeHtml($allperms[$perm]['title']); ?>
                                <?php else : ?>
                                    <?php echo $r->escapeHtml($perm); ?>
                                <?php endif; ?>
                                <?php if($r->opt('editMode')) : ?>
                                    <a href="<?php
                                        $params = array(
                                            'group' => $g->id,
                                            'edit'  => 1,
                                            'revoke-permission' => $perm,
                                            'nonce' => $theNonce
                                        );
                                        $url = $pageURL('groups/permissions', $params);
                                        echo $r->escapeAttr($url);
                                    ?>" class="btn btn-warning pull-right">
                                        <span class="glyphicon glyphicon-remove"></span> Revoke
                                    </a>
                                <?php endif; ?>
                            </h4>
                            <?php if(isset($allperms[$perm])) : ?>
                                <p class="list-group-item-text">
                                    <?php echo $r->escapeHtml($allperms[$perm]['desc']); ?>
                                </p>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <div class="alert alert-info">
                    No permissions assigned!
                </div>
            <?php endif; ?>
        </div>
        <?php if($r->opt('editMode')) : ?>
            <div class="grantable-permissions">
                <ul class="list-group">
                    <?php foreach($allperms as $perm => $info) : ?>
                        <?php if(!in_array($perm, $perms)) : ?>
                            <li class="list-group-item">
                                <h4 class="list-group-item-heading">
                                    <span class="label label-default"><?php echo $r->escapeHtml($info['cat']); ?></span> 
                                    <?php echo $r->escapeHtml($info['title']); ?>
                                    <?php if($r->opt('editMode')) : ?>
                                        <a href="<?php
                                            $params = array(
                                                'group' => $g->id,
                                                'edit'  => 1,
                                                'grant-permission' => $perm,
                                                'nonce' => $theNonce
                                            );
                                            $url = $pageURL('groups/permissions', $params);
                                            echo $r->escapeAttr($url);
                                        ?>" class="btn btn-warning pull-right">
                                            <span class="glyphicon glyphicon-plus"></span> Grant
                                        </a>
                                    <?php endif; ?>
                                </h4>
                                <p class="list-group-item-text">
                                    <?php echo $r->escapeHtml($info['desc']); ?>
                                </p>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <hr />
        <a href="<?php
            $params = array(
                'group' => $g->id,
                'edit'  => !$r->opt('editMode')
            );
            $url = $pageURL('groups/permissions', $params);
            echo $r->escapeAttr($url);
        ?>" class="btn btn-default"><?php
            if($r->opt('editMode')) : ?>
                <span class="glyphicon glyphicon-ok"></span> Done
            <?php else : ?>
                <span class="glyphicon glyphicon-pencil"></span> Edit
            <?php endif;
        ?></a>
        <?php if(!$r->opt('editMode')) : ?>
            <a class="btn btn-default" href="<?php echo $r->escapeAttr($pageURL('groups/details', array('group' => $g->id))); ?>">
                Back to group
            </a>
        <?php endif; ?>
    <?php else : ?>
        <div class="page-header">
            <h1>Permissions</h1>
        </div>
        <div class="alert alert-danger">
            Group not found
        </div>
    <?php endif; ?>
<?php $r->render('footer'); ?>