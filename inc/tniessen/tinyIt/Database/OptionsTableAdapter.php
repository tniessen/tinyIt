<?php
namespace tniessen\tinyIt\Database;

class OptionsTableAdapter extends TableAdapter
{
    /**
     * Sets an option.
     *
     * If the value is a boolean, it will be stored as `'1'` or `'0'`.
     *
     * @param string $key
     * @param mixed $value
     * @throws \PDOException
     */
    public function setOption($key, $value)
    {
        if(is_bool($value))
            $value = $value ? 1 : 0;
        if($value !== null)
            $value = strval($value);

        $this->insert('name=?, value=?', array($key, $value), 'value=?', array($value));
    }

    /**
     * Sets an array of options.
     *
     * @param array $options
     * @throws \PDOException
     */
    public function setOptions($options)
    {
        foreach($options as $key => $value)
        {
            $this->setOption($key, $value);
        }
    }

    /**
     * Retrieves the value of an option.
     *
     * @param string $key
     * @return string
     * @throws \PDOException
     */
    public function getOption($key)
    {
        $stmt = $this->select('value', 'name=?', array($key));
        $value = $stmt->fetchColumn();
        $stmt->closeCursor();
        return $value;
    }

    /**
     * Retrieves the value of multiple options.
     *
     * This function will use a single query to retrieve the values and is
     * therefore more efficient than multiple calls to {@link getOption}.
     *
     * @param array $keys
     * @return array
     * @throw \PDOException
     */
    public function getOptions($keys)
    {
        $where = '';
        foreach($keys as $key)
        {
            if($where) $where .= ' OR ';
            $where .= 'name=?';
        }
        $stmt = $this->select('name, value', $where, $keys);
        $result = array();
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[$row['name']] = $row['value'];
        }
        $stmt->closeCursor();
        return $result;
    }
}
