<?php
namespace tniessen\tinyIt\WebUI;

use \tniessen\tinyIt\Application;

/**
 * Provides a simple way to render PHP templates.
 *
 * Important terms:
 * - **WebRenderer**: Used to render templates using *provided variables* and
 *   *options*.
 * - **Provided variables**: During and after the creation of a *WebRenderer*,
 *   functions can pass variables to the created instance which will be exposed
 *   to templates.
 * - **Options**: During the creation of a *WebRenderer*, functions can pass
 *   options to the created instance. Templates can access these options using
 *   WebRenderer::opt (available as `$r->opt`, see {@link WebRenderer::render}).
 *   Options should usually be preferred over *provided variables* because they
 *   will not clutter the template scope.
 */
class WebRenderer
{
    private $options;
    private $providedVariables;

    /**
     * Creates a new WebRenderer.
     *
     * @param array $options
     * @param array $variables
     */
    public function __construct($options = array(), $variables = array())
    {
        $this->options = $options;
        $this->providedVariables = $variables;
    }

    /**
     * Sets a "provided variable".
     */
    public function provide($name, $value)
    {
        $this->providedVariables[$name] = $value;
    }

    /**
     * Renders a template.
     *
     * If additional options are passed, a new WebRenderer instance will be
     * used to render the template. The new instance will inherit this
     * WebRenderer's *options* and *provided variables*. Passed additional
     * options will overwrite inherited options.
     *
     * The following variables are always available (unless overwritten by
     * *provided variables*):
     * - `$r`, `$renderer`: The used WebRenderer instance
     * - `$template`: The name (or path) of the rendered template
     *
     * The template name must be the template's file path relative to the
     * `templates` directory, excluding the `.php` extension.
     *
     * @param string $template
     * @param array  $additionalOptions
     */
    public function render($template, $additionalOptions = null)
    {
        if($additionalOptions != null) {
            $options = array_merge($this->options, $additionalOptions);
            $renderer = new WebRenderer($options, $this->providedVariables);
        } else {
            $renderer = $this;
        }
        $r = $renderer;
        if($this->providedVariables) {
            extract($this->providedVariables);
        }
        require __DIR__ . "/templates/$template.php";
    }

    /**
     * Retrieves the value of an option.
     *
     * If the option has not been set, the passed default value (or `null`)
     * will be returned.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function opt($name, $default = null)
    {
        if(isset($this->options[$name])) {
            return $this->options[$name];
        }
        return $default;
    }

    /**
     * Appends a string to the value of an option.
     *
     * @param string $name
     * @param string $append
     * @param string $delim
     * @param string $default
     * @return string
     */
    public function optAppend($name, $append, $delim = '', $default = '')
    {
        $val = $this->opt($name, $default);
        return $val . $delim . $append;
    }

    /**
     * Escapes the value of an option as content of an HTML attribute.
     *
     * @see WebRenderer::escapeAttr
     */
    public function escapeAttrOpt($name, $default = '')
    {
        return self::escapeAttr($this->opt($name, $default));
    }

    /**
     * Escapes the value of an option as HTML text.
     *
     * @see WebRenderer::escapeHtml
     */
    public function escapeHtmlOpt($name, $default = '')
    {
        return self::escapeHtml($this->opt($name, $default));
    }

    /**
     * Escapes a string as content of an HTML attribute.
     */
    public static function escapeAttr($text)
    {
        return htmlspecialchars($text, ENT_QUOTES);
    }

    /**
     * Escapes a string as HTML text.
     */
    public static function escapeHtml($text)
    {
        return htmlspecialchars($text);
    }

    /**
     * Retrieves the base URL of this application.
     *
     * This function is guaranteed to return a string including a trailing
     * slash.
     *
     * @return string
     */
    public static function rootURL()
    {
        return Application::getBaseUrl()->build();
    }

    /**
     * Outputs HTML markup to include a JavaScript file.
     *
     * @see WebRenderer::assetURL
     */
    public static function includeScript($component, $file = null)
    {
        $url = self::assetURL($component, $file);
        echo '<script type="text/javascript" src="' . self::escapeAttr($url) . '"></script>';
    }

    /**
     * Outputs HTML markup to include a CSS file.
     *
     * @see WebRenderer::assetURL
     */
    public static function includeCSS($component, $file = null)
    {
        $url = self::assetURL($component, $file);
        echo '<link rel="stylesheet" href="' . self::escapeAttr($url) . '">';
    }

    /**
     * Returns a URL to the file described by the passed parameters.
     *
     * If a single parameter is passed, this function will append it to the
     * assets folder path.
     *
     * If two parameters are passed, this function will append three paths
     * to the assets folder path: "components", the first parameter and the
     * second parameter. This usage can therefore be used to address files
     * belonging to bower components.
     *
     * The assets folder path can differ. If `TI_ADMIN_PATH` is undefined (and
     * routing is therefore considered unavailable), it is `assets`. If it is
     * defined, `/assets` will be appended to it.
     *
     * @param string $component
     * @param string $file
     * @return string
     * @see WebRenderer::fileURL
     */
    public static function assetURL($component, $file = null)
    {
        if(defined('TI_ADMIN_PATH')) {
            $path = TI_ADMIN_PATH . '/assets/';
        } else {
            $path = 'assets/';
        }
        if($file !== null) {
            $path .= 'components/' . $component . '/' . $file;
        } else {
            $path .= $component;
        }
        return self::fileURL($path);
    }

    /**
     * Returns a URL to a file.
     *
     * @see WebRenderer::rootURL
     */
    public static function fileURL($file = null)
    {
        return self::rootURL() . $file;
    }
}
