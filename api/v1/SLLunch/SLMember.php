<?php
class SLMember extends SLResource
{
    private $lunchId;

    protected $parameters = array(
        'getAll' => array(
        ),
        'put' => array(
        ),
        'delete' => array(
        ),
    );

    protected function getAll($parameters)
    {
        $this->table('member')->delete($this->lunchId, 52);
        $result = $this->table('member')->getByLunchId($this->lunchId);

        return $result;
    }

    protected function put()
    {
    }

    protected function delete()
    {
    }

    public function __construct($lunchId)
    {
        $this->lunchId = $lunchId;
    }
}
