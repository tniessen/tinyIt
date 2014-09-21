<?php $r->render('settings/header', array('section' => 'Links')); ?>
    <form method="POST" action="<?php echo $r->escapeAttr($pageURL('settings/site/links')); ?>" class="form-horizontal">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Automatically generated links</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label">Path length</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" name="linkgen_length" value="<?php echo $r->escapeAttrOpt('current:linkgen_length', $r->opt('setting:linkgen_length')); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Path characters</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="linkgen_chars" value="<?php echo $r->escapeAttrOpt('current:linkgen_chars', $r->opt('setting:linkgen_chars')); ?>" />
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Custom links</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label">PCRE for allowed paths</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="custom_links_regex" value="<?php echo $r->escapeAttrOpt('current:custom_links_regex', $r->opt('setting:custom_links_regex')); ?>" />
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <input type="submit" class="btn btn-lg btn-success" value="Save" />
        </div>
    </form>
<?php $r->render('settings/footer'); ?>