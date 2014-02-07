<?php
class SLTableUser extends SLTable
{
    public function has($userId)
    {
        $result = $this->execute(
            'has',
            array(
                'userId' => $userId,
            )
        );

        return $result;
    }

    public function get($userId)
    {
        $result = $this->execute(
            'get',
            '*',
            array(
                'userId' => $userId,
            )
        );

        return $result;
    }

    public function getByUserIds($userIds)
    {
        $users = $this->execute(
            'select',
            '*',
            array(
                'userId' => $userIds,
            )
        );

        $result = array();
        foreach ($users as $user) {
            $userId = $user['userId'];
            $result[$userId] = $user;
        }

        return $result;
    }

    public function getByUsername($username)
    {
        $result = $this->execute(
            'get',
            '*',
            array(
                'username' => $username,
            )
        );

        return $result;
    }

    public function add($username, $email, $displayName, $data)
    {
        $result = $this->execute(
            'insert',
            array(
                'username' => $username,
                'email' => $email,
                'displayName' => $displayName,
                'data' => $data,
            )
        );

        return $result;
    }
}
