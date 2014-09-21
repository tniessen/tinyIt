<?php
namespace tniessen\tinyIt\WebUI\Links;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\WebUI\WebRenderer;
use tniessen\tinyIt\Security\Authorization;

class ShortenLinkPage extends Page
{
    private $errorMessage;
    private $currentParams;

    private $allowOverrideWildcards;

    public function init($params)
    {
        self::requireLogin();
        self::requirePermission('link.shorten');

        $postData = \tniessen\tinyIt\HttpParams::_POST();
        if($postData && !$postData->isEmpty()) {
            $this->currentParams = $postData;
            $this->tryProcessPostData($postData);
        }
    }

    private function tryProcessPostData($postData)
    {
        $reqfields = array(
            'target_link'
        );
        if(!$postData->hasValues($reqfields)) {
            $this->errorMessage = 'Please enter a target link.';
            return;
        }

        $fields = $reqfields;
        $fields[] = 'use_custom_path';
        $fields[] = 'custom_path';
        $fields[] = 'override_wildcards';
        extract($postData->filter($fields));

        if($override_wildcards) {
            if(!self::hasPermission('link.override_wildcards')) {
                $this->errorMessage = 'You are not permitted to override wildcards.';
                return;
            }
        }

        $dbc = Application::dbConnection();
        $opts = $dbc->options()->getOptions(array(
            'linkgen_chars',
            'linkgen_length',
            'custom_links_regex'
        ));
        extract($opts);

        if($use_custom_path) {
            if(!$custom_path) {
                $this->errorMessage = 'Please enter a valid short path or uncheck the custom path option.';
                return;
            }

            if(!self::hasPermission('link.custom_path')) {
                $this->errorMessage = 'You are not permitted to use custom paths.';
                return;
            }

            if(!preg_match("/$custom_links_regex/", $custom_path)) {
                $this->errorMessage = 'The chosen short path is not allowed due to administrative restrictions.';
                return;
            }
            $shortpath = $custom_path;
        } else {
            $linkgen_length = intval($linkgen_length);
            $shortpath = $dbc->links()->findAvailablePath($linkgen_length, $linkgen_chars);
        }

        $conflict = $dbc->links()->checkConflictsStatic($shortpath);
        $this->allowOverrideWildcards = !!$conflict && self::hasPermission('link.override_wildcards');
        if($conflict) {
            if($conflict->type === 'static') {
                $this->errorMessage = 'Another link with the same path or a conflicting path already exists.';
                return;
            }
            if($conflict->type === 'regex' && !$override_wildcards) {
                $url = self::getURL('links/details', array('link' => $conflict->id))->build();
                $this->errorMessage = 'This path would override <a href="' . WebRenderer::escapeAttr($url) . '">a defined wildcard</a>.';
                return;
            }
        }

        $entry = $dbc->links()->addLink('static', $shortpath, $target_link, Authorization::user()->id);
        if(!$entry) {
            $this->errorMessage = 'An internal error occurred while creating the short URL. Please try again or ask an administrator for help.';
            return;
        }

        self::redirectTo('links/details', array('link' => $entry->id));
        exit;
    }

    public function render()
    {
        $opts = array();
        if($this->errorMessage !== null) {
            $opts['errorMessage'] = $this->errorMessage;
        }
        if($this->currentParams !== null) {
            foreach($this->currentParams->values() as $field => $value) {
                $opts['current:' . $field] = $value;
            }
        }
        $opts['allowOverrideWildcards'] = $this->allowOverrideWildcards;
        $this->renderTemplate('links/shortenlink', $opts);
    }
}
