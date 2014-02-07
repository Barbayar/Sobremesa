<?php
/**
 * @SWG\Model(id="Comment",required="commentId,content,createdTime,userId,displayName,data")
 */
class SWGModelComment
{
    /**
     * @SWG\Property(name="commentId",type="integer",description="comment id")
     */
    public $commentId;

    /**
     * @SWG\Property(name="content",type="string",description="comment")
     */
    public $content;

    /**
     * @SWG\Property(name="createdTime",type="integer",description="created time in unix timestamp")
     */
    public $createdTime;

    /**
     * @SWG\Property(name="userId",type="integer",description="author's user id")
     */
    public $userId;

    /**
     * @SWG\Property(name="displayName",type="string",description="author's display name")
     */
    public $displayName;

    /**
     * @SWG\Property(name="data",type="string",description="author's other data")
     */
    public $data;
}

/**
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   basePath="/api/v1",
 *   resourcePath="comment"
 * )
 */
class SLComment extends SLResource
{
    private $_lunchId;

    protected $parameters = array(
        'getAll' => array(
        ),
        'post' => array(
            'commentId' => SLValidators::ID,
            'content' => SLValidators::SINGLE_LINE,
        ),
        'put' => array(
            'content' => SLValidators::SINGLE_LINE,
        ),
        'delete' => array(
            'commentId' => SLValidators::ID,
        ),
    );

    /**
     * @SWG\Api(
     *   path="/lunch/{lunchId}/comment/all",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="gets all comments",
     *     notes="returns list of comments",
     *     type="array",
     *     items="$ref:Comment",
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
        $comments = $this->table('comment')->getByLunchId($this->_lunchId);

        $userIds = array();
        foreach ($comments as $comment) {
            $userIds[] = $comment['userId'];
        }

        $users = $this->table('user')->getByUserIds($userIds);

        $result = array();
        foreach ($comments as $comment) {
            $userId = $comment['userId'];

            $result[] = array(
                'commentId' => $comment['commentId'],
                'content' => $comment['content'],
                'createdTime' => strtotime($comment['createdTime']),
                'userId' => $userId,
                'displayName' => $users[$userId]['displayName'],
                'data' => $users[$userId]['data'],
            );
        }

        return $result;
    }

    /**
     * @SWG\Api(
     *   path="/lunch/{lunchId}/comment",
     *   @SWG\Operation(
     *     method="POST",
     *     summary="updates a comment",
     *     notes="returns always true",
     *     type="boolean",
     *     @SWG\Parameter(
     *       name="lunchId",
     *       description="lunch id",
     *       required=true,
     *       type="integer",
     *       paramType="path"
     *     ),
     *     @SWG\Parameter(
     *       name="commentId",
     *       description="comment id",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="content",
     *       description="new comment",
     *       required=true,
     *       type="string",
     *       paramType="query"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=403, message="has no permission"),
     *     @SWG\ResponseMessage(code=404, message="invalid lunch id or comment id"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function post($parameters)
    {
        $me = SLAuthentication::getMe();
        $commentId = $parameters['commentId'];
        $comment = $this->table('comment')->get($commentId);
        $content = $parameters['content'];

        if ($comment === false) {
            throw new SLException(SLHTTPResponseCodes::BAD_REQUEST, SLErrorMessages::INVALID_RESOURCE_ID . " ($commentId)");
        }

        if ($comment['userId'] !== $me['userId']) {
            throw new SLException(SLHTTPResponseCodes::FORBIDDEN, SLErrorMessages::PERMISSION_ERROR);
        }

        $this->table('comment')->update($commentId, $content);

        return true;
    }

    /**
     * @SWG\Api(
     *   path="/lunch/{lunchId}/comment",
     *   @SWG\Operation(
     *     method="PUT",
     *     summary="adds a new comment",
     *     notes="returns comment id",
     *     type="integer",
     *     @SWG\Parameter(
     *       name="lunchId",
     *       description="lunch id",
     *       required=true,
     *       type="integer",
     *       paramType="path"
     *     ),
     *     @SWG\Parameter(
     *       name="content",
     *       description="comment",
     *       required=true,
     *       type="string",
     *       paramType="query"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=404, message="invalid lunch id"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function put($parameters)
    {
        $me = SLAuthentication::getMe();
        $content = $parameters['content'];

        return $this->table('comment')->add($this->_lunchId, $me['userId'], $content);
    }

    /**
     * @SWG\Api(
     *   path="/lunch/{lunchId}/comment",
     *   @SWG\Operation(
     *     method="DELETE",
     *     summary="deletes a comment",
     *     notes="returns always true",
     *     type="boolean",
     *     @SWG\Parameter(
     *       name="lunchId",
     *       description="lunch id",
     *       required=true,
     *       type="integer",
     *       paramType="path"
     *     ),
     *     @SWG\Parameter(
     *       name="commentId",
     *       description="comment id",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=403, message="has no permission"),
     *     @SWG\ResponseMessage(code=404, message="invalid lunch id or comment id"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function delete($parameters)
    {
        $me = SLAuthentication::getMe();
        $commentId = $parameters['commentId'];
        $comment = $this->table('comment')->get($commentId);

        if ($comment === false) {
            throw new SLException(SLHTTPResponseCodes::NOT_FOUND, SLErrorMessages::INVALID_RESOURCE_ID . " ($commentId)");
        }

        if ($comment['userId'] !== $me['userId']) {
            throw new SLException(SLHTTPResponseCodes::FORBIDDEN, SLErrorMessages::PERMISSION_ERROR);
        }

        $this->table('comment')->remove($commentId);

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
