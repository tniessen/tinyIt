
window.jsBind = (function($) {

  if(!$) throw 'jsBind requires jQuery';

  var controllers = {};

  var api = {};

  api.ns = function(ns) {
    return {
      controller: function(name) {
        return api.controller(ns + '.' + name);
      },
      ns: function(name) {
        return api.ns(ns + '.' + name);
      }
    };
  };

  api.controller = function(name) {
    return {
      register: function(fn) {
        controllers[name] = fn;
      },
      remove: function() {
        delete controllers[name];
      }
    };
  };

  api.init = function($root) {
    if(!$root) $root = $(document);
    $root.find('[data-jsbind]').forEach(function(elem) {
      var cName = elem.getAttribute('data-jsbind');
      if(controllers[cName]) {
        controllers[cName](elem);
      } else {
        throw 'The controller ' + cName + ' was not found';
      }
    });
  };

  return api;

})(jQuery);
