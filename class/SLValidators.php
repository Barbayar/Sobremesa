<?php
class SLValidators
{
    const ID = '/^\d{1,10}$/';
    const SINGLE_LINE = '/^.{1,100}$/su';
    const MULTI_LINE = '/^.{1,1000}$/mu';
    const TIMESTAMP = '/^\d{10}$/';
    const PEOPLE_COUNT = '/^\d{1,2}$/';
    const DATE = '/^[0-9]{4}(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])$/';
}
