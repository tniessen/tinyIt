<?php
namespace tniessen\tinyIt\WebUI\Links;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\WebUI\WebRenderer;
use tniessen\tinyIt\Security\Authorization;

class LinkDetailsPage extends Page
{
    private $linkId;
    private $linkInfo;

    private $errorMessage;
    private $currentParams;

    private $allowOverrideWildcards;

    private $deleteMode;
    private $editMode;

    public function init($params)
    {
        self::requireLogin();

        $dbc = Application::dbConnection();

        if($lid = $this->linkId) {
            $this->linkInfo = $dbc->links()->getLink($lid);
            if($this->linkInfo) {
                if($this->editMode) {
                    $allowed = self::hasPermission('link.edit_links');
                    $allowed |= ($this->linkInfo->owner_id === Authorization::user()->id)
                             && self::hasPermission('link.edit_own_links');
                    if($allowed) {
                        $postData = \tniessen\tinyIt\HttpParams::_POST();
                        if($postData && !$postData->isEmpty()) {
                            $this->currentParams = $postData;
                            $this->tryProcessEditPostData($postData);
                        }
                    } else {
                        $this->editMode = false;
                        $this->errorMessage = 'You are not permitted to edit this link.';
                    }
                } elseif($this->deleteMode) {
                    self::requireNonce();
                    $allowed = self::hasPermission('link.delete_links');
                    $allowed |= ($this->linkInfo->owner_id === Authorization::user()->id)
                             && self::hasPermission('link.delete_own_links');
                    if($allowed) {
                        if($dbc->links()->removeLink($lid)) {
                            self::redirectTo('links/list');
                            exit;
                        } else {
                            $this->errorMessage = 'Internal error while deleting link';
                        }
                    } else {
                        $this->errorMessage = 'You are not permitted to delete this link.';
                    }
                }
                if($oid = $this->linkInfo->owner_id) {
                    $this->linkInfo->userInfo = $dbc->users()->getUser($oid);
                }
                if($this->linkInfo->type === 'static') {
                    $this->linkInfo->fullURL = Application::getBaseURL()->build() . $this->linkInfo->path;
                }
            }
        }
    }

    private function tryProcessEditPostData($postData)
    {
        $regex = ($this->linkInfo->type === 'regex');

        $reqfields = array(
            'link_path',
            'link_target'
        );
        if($regex) {
            $reqfields[] = 'link_priority';
        }
        if(!$postData->hasValues($reqfields)) {
            $this->errorMessage = 'Please fill out all required fields.';
            return;
        }

        $fields = $reqfields;
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
            'custom_links_regex'
        ));
        extract($opts);

        if(!$regex && $this->linkInfo->path !== $link_path) {
            if(!preg_match("/$custom_links_regex/", $link_path)) {
                $this->errorMessage = 'The chosen short path is not allowed due to administrative restrictions.';
                return;
            }

            $conflict = $dbc->links()->checkConflictsStatic($link_path);
            if($conflict) {
                if($conflict->type === 'static') {
                    $this->errorMessage = 'Another link with the same path or a conflicting path already exists.';
                    return;
                }
                if($conflict->type === 'regex') {
                    $this->allowOverrideWildcards = self::hasPermission('link.override_wildcards');
                    if(!$override_wildcards || !$this->allowOverrideWildcards) {
                        $url = self::getURL('links/details', array('link' => $conflict->id))->build();
                        $this->errorMessage = 'This path would override <a href="' . WebRenderer::escapeAttr($url) . '">a defined wildcard</a>.';
                        return;
                    }
                }
            }
        }

        if($this->linkInfo->path !== $link_path || $this->linkInfo->target !== $link_target) {
            if(!self::hasPermission('link.custom_path')) {
                $this->errorMessage = 'You are not permitted to use custom paths.';
                return;
            }
            $success = $dbc->links()->updateLink($this->linkInfo->id, $link_path, $link_target);
            if(!$success) {
                $this->errorMessage = 'An internal error occurred while saving the changes. Please try again or ask an administrator for help.';
                return;
            }
        }

        if($regex && $this->linkInfo->priority !== $link_priority) {
            $s = $dbc->links()->setPriority($this->linkInfo->id, $link_priority);
            if(!$s) {
                $this->errorMessage = 'The priority could not be changed.';
                return;
            }
        }

        self::redirectTo('links/details', array('link' => $this->linkInfo->id));
        exit;
    }

    public function render()
    {
        $opts = array(
            'linkInfo' => $this->linkInfo,
            'editMode' => $this->editMode
        );
        if($this->errorMessage !== null) {
            $opts['errorMessage'] = $this->errorMessage;
        }
        if($this->currentParams !== null) {
            foreach($this->currentParams->values() as $field => $value) {
                $opts['current:' . $field] = $value;
            }
        }
        $opts['allowOverrideWildcards'] = $this->allowOverrideWildcards;
        $this->renderTemplate('links/linkdetails', $opts);
    }

    public static function getInstance($path, $params)
    {
        $page = parent::getInstance($path, $params);
        $page->linkId = $params['link'];
        $page->editMode = isset($params['edit']) && $params['edit'];
        $page->deleteMode = isset($params['delete']) && $params['delete'];
        return $page;
    }
}
