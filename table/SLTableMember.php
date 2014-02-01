<?php
class SLTableMember extends SLTable
{
    public function has($lunchId, $userId)
    {
        $result = $this->execute(
            'has',
            array(
                'AND' => array(
                    'lunchId' => $lunchId,
                    'userId' => $userId,
                ),
            )
        );

        return $result;
    }

    public function getByLunchId($lunchId)
    {
        $result = $this->execute(
            'select',
            '*',
            array(
                'lunchId' => $lunchId,
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
            )
        );

        return $result;
    }

    public function add($lunchId, $userId)
    {
        $result = $this->execute(
            'insert',
            array(
                'lunchId' => $lunchId,
                'userId' => $userId,
            )
        );

        return $result;
    }

    public function delete($lunchId, $userId)
    {
        $result = $this->execute(
            'delete',
            array(
                'AND' => array(
                    'lunchId' => $lunchId,
                    'userId' => $userId,
                ),
            )
        );

        return $result;
    }
}
