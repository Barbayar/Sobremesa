<?php
class SLUser extends SLResource
{
    protected $parameters = array(
        'get' => array(
        ),
        'post' => array(
            'user_id' => SLValidators::ID,
            'name' => SLValidators::SINGLE_LINE,
            'department' => SLValidators::SINGLE_LINE,
            'chatwork_id' => SLValidators::ID,
        ),
        'put' => array(
            'user_id' => SLValidators::ID,
            'name' => SLValidators::SINGLE_LINE,
            'department' => SLValidators::SINGLE_LINE,
            'chatwork_id' => SLValidators::ID,
        ),
    );

    protected function get($parameters)
    {
        return $parameters;
    }
}
