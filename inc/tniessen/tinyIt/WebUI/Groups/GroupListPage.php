<?php
namespace tniessen\tinyIt\WebUI\Groups;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\WebUI\Page;

class GroupListPage extends Page
{
    private $page;
    private $groups;
    private $hasNextPage;
    private $hasPreviousPage;

    private $errorMessage;


    public function init($params)
    {
        self::requireLogin();

        $dbc = Application::dbConnection();

        $perPage = 20;
        $offset = ($this->page - 1) * $perPage;

        $all = $dbc->groups()->getGroups($offset, $perPage + 1);
        $this->hasNextPage = count($all) > $perPage;
        $this->hasPreviousPage = $this->page > 1;
        $this->groups = array_slice($all, 0, $perPage);
    }

    public function render()
    {
        $opts = array(
            'groups' => $this->groups,
            'page' => $this->page,
            'hasNextPage' => $this->hasNextPage,
            'hasPreviousPage' => $this->hasPreviousPage
        );
        if($this->errorMessage !== null) {
            $opts['errorMessage'] = $this->errorMessage;
        }
        $this->renderTemplate('groups/grouplist', $opts);
    }

    public static function getInstance($path, $params)
    {
        $page = parent::getInstance($path, $params);
        $page->page = isset($params['offset']) ? intval($params['offset']) : 1;
        if($page->page <= 0) $page->page = 1;
        return $page;
    }
}
