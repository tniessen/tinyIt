<?php $r->render('dashboard', array('title' => 'Links')); ?>
    <div class="page-header">
        <h1>Links <small>Page <?php echo $r->opt('page'); ?></small></h1>
    </div>
    <div>
        <form class="form-inline" action="<?php
            echo $r->escapeAttr($pageURL('links/resolve'));
        ?>" method="GET">
            <div class="form-group">
                <input type="text" class="form-control" name="path" placeholder="Search by path..." autocomplete="off" />
            </div>
        </form>
    </div>
    <hr />
    <ul class="pager" data-jsbind="tinyIt.Pager">
        <?php
            $nexturl = $pageURL('links/list', array('offset' => $r->opt('page') + 1));
            $prevurl = $pageURL('links/list', array('offset' => $r->opt('page') - 1));
        ?>
        <li class="previous <?php if(!$r->opt('hasPreviousPage')) echo 'disabled'; ?>"><a href="<?php echo  $r->escapeAttr($prevurl); ?>">&larr; Previous</a></li>
        <li class="next <?php if(!$r->opt('hasNextPage')) echo 'disabled'; ?>"><a href="<?php echo  $r->escapeAttr($nexturl); ?>">Next &rarr;</a></li>
    </ul>
    <table class="table table-hover table-condensed links-table">
        <thead>
            <tr>
                <th>Type</th>
                <th>Path</th>
                <th>Target</th>
                <th>Owner</th>
            </tr>
        </thead>
        <tbody>
            <?php if(($links = $r->opt('links')) && count($links)) : ?>
                <?php foreach($links as $link) : ?>
                    <tr>
                        <?php
                            $url = $pageURL('links/details', array(
                                'link' => $link->id
                            ));
                            $anchor = '<a href="' . $r->escapeAttr($url) . '">';
                        ?>
                        <td><?php
                            switch($link->type) {
                            case 'regex':
                                $vtname = 'Wildcard';
                                $vticon = 'certificate';
                                break;
                            case 'static':
                                $vtname = 'Static';
                                $vticon = 'link';
                                break;
                            default:
                                $vtname = $r->escapeHtml($link->type);
                                $vticon = 'warning-sign';
                            }
                            echo $anchor . '<span class="glyphicon glyphicon-' . $vticon . '"></span> ' . $vtname . '</a>';
                        ?></td>
                        <td><?php echo $anchor . '<tt>' . $r->escapeHtml($link->path) . '</tt></a>'; ?></td>
                        <td><?php echo $anchor . $r->escapeHtml($link->target) . '</a>'; ?></td>
                        <td><?php echo $anchor . $r->escapeHtml($link->userInfo->display_name) . '</a>'; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4">No links found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php $r->render('footer'); ?>