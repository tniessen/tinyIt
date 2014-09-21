<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#dashbar-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo $pageURL('home'); ?>">tinyIt</a>
        </div><!-- .navbar-header -->
        <div class="collapse navbar-collapse" id="dashbar-navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-link"></span> Links <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $r->escapeAttr($pageURL('links/shorten')); ?>">Shorten</a></li>
                        <li><a href="<?php echo $r->escapeAttr($pageURL('links/add-wildcard')); ?>">Add wildcard</a></li>
                        <li><a href="<?php echo $r->escapeAttr($pageURL('links/list')); ?>">View links</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> Users <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $r->escapeAttr($pageURL('users')); ?>">Users</a></li>
                        <li><a href="<?php echo $r->escapeAttr($pageURL('groups')); ?>">Groups</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-wrench"></span> Tools <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $r->escapeAttr($pageURL('tools/qr-code')); ?>">QR code generator</a></li>
                    </ul>
                </li>
                <li><a href="<?php echo $r->escapeAttr($pageURL('settings')); ?>"><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Welcome, <?php echo $r->escapeHtml($theUser->display_name); ?> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $r->escapeAttr($pageURL('users/details/', array('user' => $theUser->id))); ?>">My Profile</a></li>
                        <li><a href="<?php echo $r->escapeAttr($pageURL('settings/own/account')); ?>">Account settings</a></li>
                        <li class="divider"></li>
                        <?php if($ru = \tniessen\tinyIt\Security\Authorization::realUser()) : ?>
                            <li><a href="<?php
                                $url = $pageURL('switch-user', array(
                                    'revert' => true,
                                    'nonce' => $theNonce
                                ));
                                echo $r->escapeAttr($url);
                            ?>">Switch back to <?php echo $r->escapeHtml($ru->display_name); ?></a></li>
                        <?php endif; ?>
                        <li><a href="<?php
                            $nonce = \tniessen\tinyIt\Security\Authorization::getNonce();
                            $url = $pageURL('logout', array('nonce' => $nonce));
                            echo $r->escapeAttr($url);
                        ?>">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div><!-- .navbar-collapse -->
    </div><!-- .container-fluid -->
</div><!-- .navbar -->