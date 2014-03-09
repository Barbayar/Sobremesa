<?php
/**
 * @SWG\Model(id="Member",required="userId,displayName,data")
 */
class SWGModelMember
{
    /**
     * @SWG\Property(name="userId",type="integer",description="user id")
     */
    public $userId;

    /**
     * @SWG\Property(name="displayName",type="string",description="display name")
     */
    public $displayName;

    /**
     * @SWG\Property(name="data",type="string",description="other data")
     */
    public $data;
}

/**
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   basePath="/api/v1",
 *   resourcePath="member"
 * )
 */
class SLMember extends SLResource
{
    private $_lunchId;

    protected $parameters = array(
        'getAll' => array(
        ),
        'put' => array(
        ),
        'delete' => array(
        ),
    );

    /**
     * @SWG\Api(
     *   path="/lunch/{lunchId}/member/all",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="gets all members",
     *     notes="returns list of members",
     *     type="array",
     *     items="$ref:Member",
     *     @SWG\Parameter(
     *       name="lunchId",
     *       description="lunch id",
     *       required=true,
     *       type="integer",
     *       paramType="path"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=404, message="invalid lunch id"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function getAll()
    {
        $members = $this->table('member')->getByLunchId($this->_lunchId);

        $userIds = array();
        foreach ($members as $member) {
            $userIds[] = $member['userId'];
        }

        $users = $this->table('user')->getByUserIds($userIds);

        $result = array();
        foreach ($users as $user) {
            $result[] = array(
                'userId' => $user['userId'],
                'displayName' => $user['displayName'],
                'data' => $user['data'],
            );
        }

        return $result;
    }

    /**
     * @SWG\Api(
     *   path="/lunch/{lunchId}/member",
     *   @SWG\Operation(
     *     method="PUT",
     *     summary="joins a lunch",
     *     notes="returns always true",
     *     type="boolean",
     *     @SWG\Parameter(
     *       name="lunchId",
     *       description="lunch id",
     *       required=true,
     *       type="integer",
     *       paramType="path"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=404, message="invalid lunch id"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function put()
    {
        $me = SLAuthentication::getMe();

        if ($this->table('member')->has($this->_lunchId, $me['userId'])) {
            return true;
        }

        $this->table('member')->add($this->_lunchId, $me['userId']);

        $lunch = $this->table('lunch')->get($this->_lunchId);
        $this->notify('memberAdded', array($lunch['userId']), $lunch);

        return true;
    }

    /**
     * @SWG\Api(
     *   path="/lunch/{lunchId}/member",
     *   @SWG\Operation(
     *     method="DELETE",
     *     summary="cancels joining a lunch",
     *     notes="returns always true",
     *     type="boolean",
     *     @SWG\Parameter(
     *       name="lunchId",
     *       description="lunch id",
     *       required=true,
     *       type="integer",
     *       paramType="path"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=404, message="invalid lunch id"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function delete()
    {
        $me = SLAuthentication::getMe();

        if (!$this->table('member')->has($this->_lunchId, $me['userId'])) {
            return true;
        }

        $this->table('member')->remove($this->_lunchId, $me['userId']);

        $lunch = $this->table('lunch')->get($this->_lunchId);
        $this->notify('memberRemoved', array($lunch['userId']), $lunch);

        return true;
    }

    public function __construct($lunchId)
    {
        if (!$this->table('lunch')->has($lunchId)) {
            throw new SLException(SLHTTPResponseCodes::NOT_FOUND, SLErrorMessages::INVALID_RESOURCE_ID . " ($lunchId)");
        }

        $this->_lunchId = $lunchId;
    }
}
