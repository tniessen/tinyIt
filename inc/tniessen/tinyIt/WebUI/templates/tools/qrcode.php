<?php $r->render('dashboard', array('title' => 'QR code generator')); ?>
    <div class="page-header">
        <h1>QR code generator</h1>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <?php if($r->opt('errorMessage') != null) : ?>
                    <div class="alert alert-danger">
                        <?php echo $r->opt('errorMessage'); ?>
                    </div>
                <?php endif; ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form class="form" method="GET">
                            <div class="form-group">
                                <label for="qr-data">URL</label>
                                <input type="text" class="form-control input-lg" name="qr-data" value="<?php echo $r->escapeAttrOpt('current:qr-data'); ?>" />
                            </div>
                            <div class="form-group">
                                <label for="qr-size">Size</label>
                                <input type="text" class="form-control" name="qr-size" value="<?php echo $r->escapeAttrOpt('current:qr-size', 400); ?>" placeholder="size in pixels (1-1000)" autocomplete="off" />
                            </div>
                            <div class="form-group">
                                <label for="qr-bgcolor">Background color</label>
                                <div class="input-group">
                                    <span class="input-group-addon">#</span>
                                    <input type="text" class="form-control" name="qr-bgcolor" value="<?php echo $r->escapeAttrOpt('current:qr-bgcolor', 'fff'); ?>" placeholder="hex color" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="qr-fgcolor">Foreground color</label>
                                <div class="input-group">
                                    <span class="input-group-addon">#</span>
                                    <input type="text" class="form-control" name="qr-fgcolor" value="<?php echo $r->escapeAttrOpt('current:qr-fgcolor', '000'); ?>" placeholder="hex color" />
                                </div>
                            </div>
                            <button type="submit" class="btn btn-block btn-lg btn-primary">
                                <span class="glyphicon glyphicon-qrcode"></span> Create QR code
                            </button>
                            <p class="help-block">
                                This tool uses the <a href="http://goqr.me/api/">goqr.me API</a>.
                                By using this tool, you agree to the applicable Terms of Service
                                as specified on <a href="http://goqr.me">goqr.me</a>.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- .row -->
        <?php if($qrCodeUrl = $r->opt('qrCodeURL')) : ?>
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <img class="col-md-8 col-md-offset-2" src="<?php echo $r->escapeAttr($qrCodeUrl); ?>" />
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php $r->render('footer'); ?>