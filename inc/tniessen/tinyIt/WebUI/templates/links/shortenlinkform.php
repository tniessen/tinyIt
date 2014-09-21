<form action="<?php echo $r->escapeAttr($pageURL('links/shorten')); ?>" method="POST" data-jsbind="tinyIt.Links.ShortenForm">
    <input type="url" class="form-control input-lg append-vspace-big" name="target_link" placeholder="Link" value="<?php echo $r->escapeAttrOpt('current:target_link'); ?>" />
    <?php if($page->hasPermission('link.custom')) : ?>
        <div class="checkbox">
            <label>
                <input type="checkbox" value="1" name="use_custom_path" <?php if($r->opt('current:use_custom_path')) echo 'checked'; ?>>
                Use custom short URL
            </label>
        </div>
        <div class="input-group">
            <span class="input-group-addon"><?php echo $r->escapeHtml(\tniessen\tinyIt\Application::getBaseURL()->build()); ?></span>
            <input type="text" class="form-control" name="custom_path" placeholder="Custom path" value="<?php echo $r->escapeAttrOpt('current:custom_path'); ?>" />
        </div>
    <?php endif; ?>
    <?php if($r->opt('allowOverrideWildcards')) : ?>
        <div class="checkbox">
            <label>
                <input type="checkbox" value="1" name="override_wildcards" <?php if($r->opt('current:override_wildcards')) echo 'checked'; ?>>
                Override wildcards
            </label>
        </div>
    <?php endif; ?>
    <input type="submit" class="btn btn-lg btn-primary btn-block prepend-vspace-huge" value="Shorten it" />
</form>