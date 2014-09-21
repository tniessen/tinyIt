<?php $r->render('dashboard', array('title' => 'Users')); ?>
    <div class="page-header">
        <h1>Users <small>Page <?php echo $r->opt('page'); ?></small></h1>
    </div>
    <ul class="pager" data-jsbind="tinyIt.Pager">
        <?php
            $nexturl = $pageURL('users/list', array('offset' => $r->opt('page') + 1));
            $prevurl = $pageURL('users/list', array('offset' => $r->opt('page') - 1));
        ?>
        <li class="previous <?php if(!$r->opt('hasPreviousPage')) echo 'disabled'; ?>"><a href="<?php echo  $r->escapeAttr($prevurl); ?>">&larr; Previous</a></li>
        <li class="next <?php if(!$r->opt('hasNextPage')) echo 'disabled'; ?>"><a href="<?php echo  $r->escapeAttr($nexturl); ?>">Next &rarr;</a></li>
    </ul>
    <table class="table table-hover table-condensed users-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Display name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($r->opt('users') as $user) : ?>
                <tr>
                    <?php
                        $url = $pageURL('users/details', array(
                            'user' => $user->id
                        ));
                        $anchor = '<a href="' . $r->escapeAttr($url) . '">';
                    ?>
                    <td><?php echo $anchor . $r->escapeHtml($user->name) . '</a>'; ?></td>
                    <td><?php echo $anchor . $r->escapeHtml($user->display_name) . '</a>'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php $r->render('footer'); ?>