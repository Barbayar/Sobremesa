<?php
class SLTableLunch extends SLTable
{
    public function has($lunchId)
    {
        $result = $this->execute(
            'has',
            array(
                'lunchId' => $lunchId,
            )
        );

        return $result;
    }

    public function get($lunchId)
    {
        $result = $this->execute(
            'get',
            '*',
            array(
                'lunchId' => $lunchId,
            )
        );

        return $result;
    }

    public function getByLunchIds($lunchIds)
    {
        $result = $this->execute(
            'select',
            '*',
            array(
                'lunchId' => $lunchIds,
                'ORDER' => 'endTime',
            )
        );

        return $result;
    }

    public function getByEndTimeRange($lowerBound, $upperBound)
    {
        $result = $this->execute(
            'select',
            '*',
            array(
                'endTime[<>]' => array($lowerBound, $upperBound),
                'ORDER' => 'endTime',
            )
        );

        return $result;
    }

    public function getByUserId($userId)
    {
        $result = $this->execute(
            'select',
            '*',
            array(
                'userId' => $userId,
                'ORDER' => 'endTime',
            )
        );

        return $result;
    }

    public function getAvailable()
    {
        $result = $this->execute(
            'select',
            '*',
            array(
                'endTime[>]' => time(),
                'ORDER' => 'endTime',
            )
        );

        return $result;
    }

    public function update($lunchId, $userId, $theme, $location, $description, $beginTime, $endTime, $minPeople, $maxPeople)
    {
        $result = $this->execute(
            'update',
            array(
                'userId' => $userId,
                'theme' => $theme,
                'location' => $location,
                'description' => $description,
                'beginTime' => $beginTime,
                'endTime' => $endTime,
                'minPeople' => $minPeople,
                'maxPeople' => $maxPeople,
            ),
            array(
                'lunchId' => $lunchId,
            )
        );

        return $result;
    }

    public function add($userId, $theme, $location, $description, $beginTime, $endTime, $minPeople, $maxPeople)
    {
        $result = $this->execute(
            'insert',
            array(
                'userId' => $userId,
                'theme' => $theme,
                'location' => $location,
                'description' => $description,
                'beginTime' => $beginTime,
                'endTime' => $endTime,
                'minPeople' => $minPeople,
                'maxPeople' => $maxPeople,
            )
        );

        return $result;
    }

    public function remove($lunchId)
    {
        $result = $this->execute(
            'delete',
            array(
                'lunchId' => $lunchId,
            )
        );

        return $result;
    }
}
