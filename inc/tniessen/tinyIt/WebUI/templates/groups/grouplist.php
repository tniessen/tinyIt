<?php $r->render('dashboard', array('title' => 'Groups')); ?>
    <div class="page-header">
        <h1>Groups <small>Page <?php echo $r->opt('page'); ?></small></h1>
    </div>
    <div>
        <a href="<?php
            $url = $pageURL('groups/add-group', array(
                'nonce' => $theNonce
            ));
            echo $r->escapeAttr($url);
        ?>" class="btn btn-primary btn-sm">
            <span class="glyphicon glyphicon-plus"></span> Add group
        </a>
    </div>
    <hr />
    <ul class="pager" data-jsbind="tinyIt.Pager">
        <?php
            $nexturl = $pageURL('groups/list', array('offset' => $r->opt('page') + 1));
            $prevurl = $pageURL('groups/list', array('offset' => $r->opt('page') - 1));
        ?>
        <li class="previous <?php if(!$r->opt('hasPreviousPage')) echo 'disabled'; ?>"><a href="<?php echo  $r->escapeAttr($prevurl); ?>">&larr; Previous</a></li>
        <li class="next <?php if(!$r->opt('hasNextPage')) echo 'disabled'; ?>"><a href="<?php echo  $r->escapeAttr($nexturl); ?>">Next &rarr;</a></li>
    </ul>
    <table class="table table-hover table-condensed groups-table">
        <thead>
            <tr>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            <?php if(($groups = $r->opt('groups')) && count($groups)) : ?>
                <?php foreach($groups as $group) : ?>
                    <tr>
                        <?php
                            $url = $pageURL('groups/details', array(
                                'group' => $group->id
                            ));
                            $anchor = '<a href="' . $r->escapeAttr($url) . '">';
                        ?>
                        <td><?php echo $anchor . $r->escapeHtml($group->name) . '</a>'; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="1">No groups found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php $r->render('footer'); ?>