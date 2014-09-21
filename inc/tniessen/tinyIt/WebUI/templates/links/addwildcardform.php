<form action="<?php echo $r->escapeAttr($pageURL('links/add-wildcard')); ?>" method="POST">
    <div class="form-group">
        <input type="text" class="form-control input-lg append-vspace" name="link_path" placeholder="Path" value="<?php echo $r->escapeAttrOpt('current:link_path'); ?>" />
        <input type="text" class="form-control input-lg" name="link_target" placeholder="Target" value="<?php echo $r->escapeAttrOpt('current:link_target'); ?>" />
        <p class="help-block">tinyIt uses <abbr title="Perl Compatible Regular Expressions">PCRE</abbr>. The <em>path</em> should be a valid regular expression.
        Use <code>$1 ... $n</code> in the <em>target</em> to refer to <em>capture groups</em> in the path.</p>
    </div>
    <div class="form-group">
        <label for="link_priority">Priority:</label>
        <input type="text" class="form-control" name="link_priority" placeholder="(default: 100)" value="<?php echo $r->escapeAttrOpt('current:link_priority', 100); ?>" />
        <p class="help-block">If multiple wildcards match the requested URL, the wildcard with the highest priority will be used (default priority is 100).</p>
    </div>
    <input type="submit" class="btn btn-lg btn-primary btn-block prepend-vspace-huge" value="Create wildcard" />
</form>