<?php
class SLTable
{
    private $_database;
    private $_tableName;

    public function __construct($tableName)
    {
        $this->_database = SLDatabase::getInstance();
        $this->_tableName = $tableName;
    }

    public function execute()
    {
        $arguments = func_get_args();
        $method = array_shift($arguments);
        array_unshift($arguments, $this->_tableName);

        $result = call_user_func_array(array($this->_database, $method), $arguments);
        $error = $this->_database->error();

        if (!is_null($error[2])) {
            throw new Exception('Last Query: ' . $this->_database->last_query() . "\nError: $error[2]");
        }

        return $result;
    }
}
