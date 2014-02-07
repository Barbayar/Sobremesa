<?php
class SLTableComment extends SLTable
{
    public function has($commentId)
    {
        $result = $this->execute(
            'has',
            array(
                'commentId' => $commentId,
            )
        );

        return $result;
    }

    public function get($commentId)
    {
        $result = $this->execute(
            'get',
            '*',
            array(
                'commentId' => $commentId,
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
                'ORDER' => 'commentId',
            )
        );

        return $result;
    }

    public function update($commentId, $content)
    {
        $result = $this->execute(
            'update',
            array(
                'content' => $content,
            ),
            array(
                'commentId' => $commentId,
            )
        );

        return $result;
    }

    public function add($lunchId, $userId, $content)
    {
        $result = $this->execute(
            'insert',
            array(
                'lunchId' => $lunchId,
                'userId' => $userId,
                'content' => $content,
            )
        );

        return $result;
    }

    public function remove($commentId)
    {
        $result = $this->execute(
            'delete',
            array(
                'commentId' => $commentId,
            )
        );

        return $result;
    }

    public function removeByLunchId($lunchId)
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
