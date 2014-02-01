<?php
abstract class SLResource
{
    private $_tables = array();

    private function _validateParameters($action, $parameters)
    {
        $parameterValidator = $this->parameters[$action];

        if (count($parameters) !== count($parameterValidator)) {
            throw new SLException(SLHTTPResponseCodes::BAD_REQUEST, SLErrorMessages::INVALID_PARAMETER_COUNT);
        }

        foreach ($parameterValidator as $key => $validator) {
            if (!isset($parameters[$key])) {
                throw new SLException(SLHTTPResponseCodes::BAD_REQUEST, SLErrorMessages::MISSING_PARAMETER . " ($key)");
            }

            $result = preg_match($validator, $parameters[$key]);

            if ($result === 0) {
                throw new SLException(SLHTTPResponseCodes::BAD_REQUEST, SLErrorMessages::INVALID_PARAMETER . " (${parameters[$key]})");
            }

            if ($result === false) {
                throw new Exception("Error: regular expression is invalid\nRegular Expression: ${parameterValidator[$key]}");
            }
        }
    }

    protected function table($tableName)
    {
        if (isset($this->_tables[$tableName])) {
            $this->_tables[$tableName];
        }

        $tableClass = 'SLTable' . ucfirst($tableName);
        require_once("../table/$tableClass.php");

        $tableKlass = new ReflectionClass($tableClass);
        $table = $tableKlass->newInstanceArgs(array($tableName));
        $this->_tables[$tableName] = $table;

        return $this->_tables[$tableName];
    }

    public function run($action, $parameters)
    {
        $this->_validateParameters($action, $parameters);

        return $this->$action($parameters);
    }
}
