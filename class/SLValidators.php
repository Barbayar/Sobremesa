<?php
class SLValidators
{
    const ID = '/^\d{1,10}$/';
    const SINGLE_LINE = '/^[\w\s][^\n\r]{1,100}$/';
    const MULTI_LINE = '/^[\w\s]{1,1000}$/';
    const TIMESTAMP = '/^\d{10}$/';
    const PEOPLE_COUNT = '/^\d{1,2}$/';
}
