<?php
class SLComment extends SLResource
{
    private $lunch_id;

    protected $parameters = array(
        'get' => array(
        ),
        'post' => array(
            'comment_id' => SLValidators::ID,
            'text' => SLValidators::SINGLE_LINE,
        ),
        'put' => array(
            'text' => SLValidators::SINGLE_LINE,
        ),
        'delete' => array(
            'comment_id' => SLValidators::ID,
        ),
    );

    protected function get()
    {
        return array();
    }

    public function __construct($lunch_id)
    {
        $this->lunch_id = $lunch_id;
    }
}
