<?php
namespace tniessen\tinyIt\Database;

abstract class TableAdapter
{
    /**
     * The connection this adapter is bound to.
     *
     * @var DatabaseConnection
     */
    protected $dbc;

    /**
     * The table name of this adapter.
     *
     * @var string
     */
    public $tblname;

    /**
     * Creates a new adapter.
     *
     * The adapter will be bound to the given connection.
     *
     * @param DatabaseConnection $dbc
     * @param string             $tblname
     */
    public function __construct($dbc, $tblname)
    {
        $this->dbc = $dbc;
        $this->tblname = $tblname;
    }

    /**
     * @see DatabaseConnection::select
     */
    public function select($columns = '*', $where = '', $whereArgs = array(), $orderBy = null, $limit = null)
    {
        return $this->dbc->select($this->tblname, $columns, $where, $whereArgs, $orderBy, $limit);
    }

    /**
     * @see DatabaseConnection::update
     */
    public function update($valspec, $valueArgs = null, $where = '', $whereArgs = null)
    {
        return $this->dbc->update($this->tblname, $valspec, $valueArgs, $where, $whereArgs);
    }

    /**
     * @see DatabaseConnection::insert
     */
    public function insert($valspec, $valueArgs = null, $onDuplicate = null, $onDuplicateArgs = null)
    {
        return $this->dbc->insert($this->tblname, $valspec, $valueArgs, $onDuplicate, $onDuplicateArgs);
    }

    /**
     * @see DatabaseConnection::delete
     */
    public function delete($where = '', $whereArgs = null, $limit = null)
    {
        return $this->dbc->delete($this->tblname, $where, $whereArgs, $limit);
    }
}
