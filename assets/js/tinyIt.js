
(function() {

  var withVal = function($e, fn) {
    var cb;
    if($e.is('input[type=checkbox]')) {
      cb = function() {
        fn($e.is(':checked'));
      };
    } else {
      cb = function() {
        fn($e.val());
      };
    }
    $e.change(cb);
    cb();
  };

  var tins = jsBind.ns('tinyIt');

  tins.controller('Links.ShortenForm').register(function(elem) {
    var $elem = $(elem);

    var $useCustom  = $elem.find('[name=use_custom_path]'),
        $customPath = $elem.find('[name=custom_path]');

    withVal($useCustom, function(val) {
      $customPath.prop('disabled', !val);
    });
  });

  tins.controller('Settings.General.HomePage').register(function(elem) {
    var $elem = $(elem);

    var $action = $elem.find('[name=home_action]'),
        $target = $elem.find('[name=home_target]');

    withVal($action, function(val) {
      $target.prop('disabled', val !== 'redirect');
    });
  });

  tins.controller('Settings.Users.Registration').register(function(elem) {
    var $elem = $(elem);

    var $allowed  = $elem.find('[name=allow_registration]'),
        $defGroup = $elem.find('[name=registration_user_group]');

    withVal($allowed, function(val) {
      console.log('Setting disabled to ' + !val);
      $defGroup.prop('disabled', !val);
    });
  });

  tins.controller('LinkDetails.Edit').register(function(elem) {
    var $elem = $(elem);

    var $fullURL = $elem.find('.link-full-url .form-control-static'),
        $path    = $elem.find('.link-path input[type=text]');

    if($path.val() && $fullURL.text()) {
      var path = $path.val(),
          url  = $fullURL.text();

      var urlBase = url.substr(0, url.length - path.length);

      var upd = function() {
        $fullURL.text(urlBase + $path.val());
      };

      $path.change(upd).keyup(upd);
    }
  });

  tins.controller('UserDetails.SelectGroupModal').register(function(elem) {
    var $elem = $(elem);

    var $link   = $elem.find('.set-group-link'),
        $select = $elem.find('.group-select');

    var link = $link.attr('href');

    function updateLink() {
      var group = $select.val();
      $link.attr('href', link + '&setGroup=' + group);
    }

    $select.change(updateLink);
    updateLink();
  });

  tins.controller('GroupDetails.DeleteGroupModal').register(function(elem) {
    var $elem = $(elem);

    var $link   = $elem.find('.set-group-link'),
        $select = $elem.find('.group-select');

    var link = $link.attr('href');

    function updateLink() {
      var group = $select.val();
      $link.attr('href', link + '&setGroup=' + group);
    }

    $select.change(updateLink);
    updateLink();
  });

  tins.controller('Pager').register(function(elem) {
    var $ul;
    if(!($ul = $(elem)).is('ul.pager')) {
      $ul = $ul.find('ul.pager');
    }
    $ul.find('li.disabled a')
    // Modern browsers
    .css('pointer-events', 'none')
    // IE
    .attr('disabled', 'true')
    // Others (old / mobile)
    .click(function(event) {
      event.preventDefault();
    });
  });

  $(jsBind.init);
})();
