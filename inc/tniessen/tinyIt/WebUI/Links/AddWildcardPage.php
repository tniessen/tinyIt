<?php
namespace tniessen\tinyIt\WebUI\Links;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\WebUI\WebRenderer;
use tniessen\tinyIt\Security\Authorization;

class AddWildcardPage extends Page
{
    private $errorMessage;
    private $currentParams;

    public function init($params)
    {
        self::requireLogin();
        self::requirePermission('link.add_wildcard');

        $postData = \tniessen\tinyIt\HttpParams::_POST();
        if($postData && !$postData->isEmpty()) {
            $this->currentParams = $postData;
            $this->tryProcessPostData($postData);
        }
    }

    private function tryProcessPostData($postData)
    {
        $reqfields = array(
            'link_path',
            'link_target',
            'link_priority'
        );
        if(!$postData->hasValues($reqfields)) {
            $this->errorMessage = 'Please specify:
                                   <ul>
                                     <li>Path</li>
                                     <li>Target</li>
                                     <li>Priority</li>
                                   </ul>';
            return;
        }

        $fields = $reqfields;
        extract($postData->filter($fields));

        $link_priority = intval($link_priority);
        if($link_priority < 0 || $link_priority > 1000) {
            $this->errorMessage = 'Priority must be between 0 and 1000';
            return;
        }

        $dbc = Application::dbConnection();

        $entry = $dbc->links()->addLink('regex', $link_path, $link_target, Authorization::user()->id);
        if(!$entry) {
            $this->errorMessage = 'An internal error occurred while creating the short URL. Please try again or ask an administrator for help.';
            return;
        }
        $success = $dbc->links()->setPriority($entry->id, $link_priority);
        if(!$success) {
            $url = self::getURL('links/details', array('link' => $entry->id));
            $this->errorMessage = 'The link was created, but the priority could not be set. Please <a href="' . WebRenderer::escapeAttr($url) . '">try again</a>';
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
        $this->renderTemplate('links/addwildcard', $opts);
    }
}
