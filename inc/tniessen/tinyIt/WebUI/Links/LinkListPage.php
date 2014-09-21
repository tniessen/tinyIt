<?php
namespace tniessen\tinyIt\WebUI\Links;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\WebUI\Page;

class LinkListPage extends Page
{
    private $page;
    private $links;
    private $hasNextPage;
    private $hasPreviousPage;

    private $errorMessage;


    public function init($params)
    {
        self::requireLogin();

        $dbc = Application::dbConnection();

        $perPage = 20;
        $offset = ($this->page - 1) * $perPage;

        $all = $dbc->links()->getLinks($offset, $perPage + 1);
        $this->hasNextPage = count($all) > $perPage;
        $this->hasPreviousPage = $this->page > 1;
        $this->links = array_slice($all, 0, $perPage);

        $users = array();
        foreach($this->links as $link) {
            $ak = strval($link->owner_id);
            if(!isset($users[$ak])) {
                $users[$ak] = $dbc->users()->getUser($link->owner_id);
            }
            $link->userInfo = $users[$ak];
        }
    }

    public function render()
    {
        $opts = array(
            'links' => $this->links,
            'page' => $this->page,
            'hasNextPage' => $this->hasNextPage,
            'hasPreviousPage' => $this->hasPreviousPage
        );
        if($this->errorMessage !== null) {
            $opts['errorMessage'] = $this->errorMessage;
        }
        $this->renderTemplate('links/linklist', $opts);
    }

    public static function getInstance($path, $params)
    {
        $page = parent::getInstance($path, $params);
        $page->page = isset($params['offset']) ? intval($params['offset']) : 1;
        if($page->page <= 0) $page->page = 1;
        return $page;
    }
}
