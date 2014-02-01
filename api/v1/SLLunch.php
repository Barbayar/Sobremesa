<?php
class SLLunch extends SLResource
{
    protected $parameters = array(
        'get' => array(
            'lunch_id' => SLValidators::ID,
        ),
        'getAll' => array(
        ),
        'post' => array(
            'lunch_id' => SLValidators::ID,
            'theme' => SLValidators::SINGLE_LINE,
            'location' => SLValidators::MULTI_LINE,
            'description' => SLValidators::MULTI_LINE,
            'beginTime' => SLValidators::TIMESTAMP,
            'endTime' => SLValidators::TIMESTAMP,
            'minPeople' => SLValidators::PEOPLE_COUNT,
            'maxPeople' => SLValidators::PEOPLE_COUNT,
        ),
        'put' => array(
            'theme' => SLValidators::SINGLE_LINE,
            'location' => SLValidators::MULTI_LINE,
            'description' => SLValidators::MULTI_LINE,
            'beginTime' => SLValidators::TIMESTAMP,
            'endTime' => SLValidators::TIMESTAMP,
            'minPeople' => SLValidators::PEOPLE_COUNT,
            'maxPeople' => SLValidators::PEOPLE_COUNT,
        ),
        'delete' => array(
            'lunch_id' => SLValidators::ID,
        ),
    );

    protected function get($parameters)
    {
        return $parameters;
    }

    protected function getAll($parameters)
    {
        return $this->parameters;
    }
}
