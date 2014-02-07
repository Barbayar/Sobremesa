<?php
require_once(dirname(__FILE__) . '/SLLunch/SLMember.php');
require_once(dirname(__FILE__) . '/SLLunch/SLComment.php');

/**
 * @SWG\Model(id="Lunch",required="lunchId,theme,location,description,beginTime,endTime,minPeople,maxPeople,createdTime,userId,displayName,data,members,comments")
 */
class SWGModelLunch
{
    /**
     * @SWG\Property(name="lunchId",type="integer",description="lunch id")
     */
    public $lunchId;

    /**
     * @SWG\Property(name="theme",type="string",description="lunch theme")
     */
    public $theme;

    /**
     * @SWG\Property(name="location",type="string",description="location")
     */
    public $location;

    /**
     * @SWG\Property(name="description",type="string",description="description")
     */
    public $description;

    /**
     * @SWG\Property(name="beginTime",type="integer",description="begin time in unix timestamp")
     */
    public $beginTime;

    /**
     * @SWG\Property(name="endTime",type="integer",description="end time in unix timestamp")
     */
    public $endTime;

    /**
     * @SWG\Property(name="minPeople",type="integer",description="minimum number of people")
     */
    public $minPeople;

    /**
     * @SWG\Property(name="maxPeople",type="integer",description="maximum number of people")
     */
    public $maxPeople;

    /**
     * @SWG\Property(name="createdTime",type="integer",description="created time in unix timestamp")
     */
    public $createdTime;

    /**
     * @SWG\Property(name="userId",type="integer",description="creator's user id")
     */
    public $userId;

    /**
     * @SWG\Property(name="displayName",type="string",description="creator's display name")
     */
    public $displayName;

    /**
     * @SWG\Property(name="data",type="string",description="creator's other data")
     */
    public $data;

    /**
     * @SWG\Property(name="members",type="array",items="$ref:Member",description="list of members")
     */
    public $members;

    /**
     * @SWG\Property(name="comments",type="array",items="$ref:Comment",description="list of comments")
     */
    public $comments;
}

/**
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   basePath="/api/v1",
 *   resourcePath="lunch"
 * )
 */
class SLLunch extends SLResource
{
    protected $parameters = array(
        'get' => array(
            'lunchId' => SLValidators::ID,
        ),
        'getByCreatorId' => array(
            'userId' => SLValidators::ID,
        ),
        'getJoined' => array(
        ),
        'getByDate' => array(
            'date' => SLValidators::DATE,
        ),
        'getAvailable' => array(
        ),
        'post' => array(
            'lunchId' => SLValidators::ID,
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
            'lunchId' => SLValidators::ID,
        ),
    );

    private function _get($lunch)
    {
        $lunchId = $lunch['lunchId'];
        $user = $this->table('user')->get($lunch['userId']);

        $userId = $lunch['userId'];
        $result = array(
            'lunchId' => $lunchId,
            'theme' => $lunch['theme'],
            'location' => $lunch['location'],
            'description' => $lunch['description'],
            'beginTime' => $lunch['beginTime'],
            'endTime' => $lunch['endTime'],
            'minPeople' => $lunch['minPeople'],
            'maxPeople' => $lunch['maxPeople'],
            'createdTime' => strtotime($lunch['createdTime']),
            'userId' => $user['userId'],
            'displayName' => $user['displayName'],
            'data' => $user['data'],
        );

        $memberResource = new SLMember($lunchId);
        $result['members'] = $memberResource->run(
            'getAll',
            array()
        );

        $commentResource = new SLComment($lunchId);
        $result['comments'] = $commentResource->run(
            'getAll',
            array()
        );

        return $result;
    }

    /**
     * @SWG\Api(
     *   path="/lunch",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="gets a lunch by lunch id",
     *     notes="returns a lunch",
     *     type="Lunch",
     *     @SWG\Parameter(
     *       name="lunchId",
     *       description="lunch id",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=404, message="invalid lunch id"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function get($parameters)
    {
        $lunchId = $parameters['lunchId'];
        $lunch = $this->table('lunch')->get($lunchId);

        if ($lunch === false) {
            throw new SLException(SLHTTPResponseCodes::NOT_FOUND, SLErrorMessages::INVALID_RESOURCE_ID . " ($lunchId)");
        }

        return $this->_get($lunch);
    }

    /**
     * @SWG\Api(
     *   path="/lunch/byCreatorId",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="gets lunches by creator's user id",
     *     notes="returns list of lunches",
     *     type="array",
     *     items="$ref:Lunch",
     *     @SWG\Parameter(
     *       name="userId",
     *       description="creator's user id",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=404, message="invalid user id"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function getByCreatorId($parameters)
    {
        $userId = $parameters['userId'];

        if (!$this->table('user')->has($userId)) {
            throw new SLException(SLHTTPResponseCodes::NOT_FOUND, SLErrorMessages::INVALID_RESOURCE_ID . " ($userId)");
        }

        $lunches = $this->table('lunch')->getByUserId($userId);

        $result = array();
        foreach ($lunches as $lunch) {
            $result[] = $this->_get($lunch);
        }

        return $result;
    }

    /**
     * @SWG\Api(
     *   path="/lunch/joined",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="gets joined lunches",
     *     notes="returns list of lunches",
     *     type="array",
     *     items="$ref:Lunch",
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function getJoined($parameters)
    {
        $me = SLAuthentication::getMe();
        $lunches = $this->table('member')->getByUserId($me['userId']);

        $lunchIds = array();
        foreach ($lunches as $lunch) {
            $lunchIds[] = $lunch['lunchId'];
        }

        $lunches = $this->table('lunch')->getByLunchIds($lunchIds);

        $result = array();
        foreach ($lunches as $lunch) {
            $result[] = $this->_get($lunch);
        }

        return $result;
    }

    /**
     * @SWG\Api(
     *   path="/lunch/byDate",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="gets lunches by date",
     *     notes="returns list of lunches",
     *     type="array",
     *     items="$ref:Lunch",
     *     @SWG\Parameter(
     *       name="date",
     *       description="a date formatted YYYYMMDD",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function getByDate($parameters)
    {
        $date = $parameters['date'];
        $lowerBound = strtotime($date);
        $upperBound = $lowerBound + 86400;

        $lunches = $this->table('lunch')->getByEndTimeRange($lowerBound, $upperBound);

        $result = array();
        foreach ($lunches as $lunch) {
            $result[] = $this->_get($lunch);
        }

        return $result;
    }

    /**
     * @SWG\Api(
     *   path="/lunch/available",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="gets available lunches",
     *     notes="returns list of lunches",
     *     type="array",
     *     items="$ref:Lunch",
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function getAvailable()
    {
        $lunches = $this->table('lunch')->getAvailable();

        $result = array();
        foreach ($lunches as $lunch) {
            $result[] = $this->_get($lunch);
        }

        return $result;
    }

    /**
     * @SWG\Api(
     *   path="/lunch",
     *   @SWG\Operation(
     *     method="POST",
     *     summary="updates a lunch",
     *     notes="returns always true",
     *     type="boolean",
     *     @SWG\Parameter(
     *       name="lunchId",
     *       description="lunch id",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="theme",
     *       description="new theme",
     *       required=true,
     *       type="string",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="location",
     *       description="new location",
     *       required=true,
     *       type="string",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="description",
     *       description="new description",
     *       required=true,
     *       type="string",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="beginTime",
     *       description="new begin time",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="endTime",
     *       description="new end time",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="minPeople",
     *       description="new minimum number of people",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="maxPeople",
     *       description="new maximum number of people",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=403, message="has no permission"),
     *     @SWG\ResponseMessage(code=404, message="invalid lunch id"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function post($parameters)
    {
        $me = SLAuthentication::getMe();
        $lunchId = $parameters['lunchId'];
        $lunch = $this->table('lunch')->get($lunchId);
        $theme = $parameters['theme'];
        $location = $parameters['location'];
        $description = $parameters['description'];
        $beginTime = $parameters['beginTime'];
        $endTime = $parameters['endTime'];
        $minPeople = $parameters['minPeople'];
        $maxPeople = $parameters['maxPeople'];

        if ($minPeople > $maxPeople) {
            throw new SLException(SLHTTPResponseCodes::BAD_REQUEST, SLErrorMessages::INVALID_PARAMETER . " ($minPeople > $maxPeople)");
        }

        if ($beginTime > $endTime) {
            throw new SLException(SLHTTPResponseCodes::BAD_REQUEST, SLErrorMessages::INVALID_PARAMETER . " ($beginTime > $endTime)");
        }

        if (time() > $endTime) {
            throw new SLException(SLHTTPResponseCodes::BAD_REQUEST, SLErrorMessages::INVALID_PARAMETER . " (now > $endTime)");
        }

        if ($lunch === false) {
            throw new SLException(SLHTTPResponseCodes::NOT_FOUND, SLErrorMessages::INVALID_RESOURCE_ID . " ($lunchId)");
        }

        if ($lunch['userId'] !== $me['userId']) {
            throw new SLException(SLHTTPResponseCodes::FORBIDDEN, SLErrorMessages::PERMISSION_ERROR);
        }

        $this->table('lunch')->update($lunchId, $me['userId'], $theme, $location, $description, $beginTime, $endTime, $minPeople, $maxPeople);

        return true;
    }

    /**
     * @SWG\Api(
     *   path="/lunch",
     *   @SWG\Operation(
     *     method="PUT",
     *     summary="updates a lunch",
     *     notes="returns a lunch id",
     *     type="integer",
     *     @SWG\Parameter(
     *       name="theme",
     *       description="new theme",
     *       required=true,
     *       type="string",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="location",
     *       description="new location",
     *       required=true,
     *       type="string",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="description",
     *       description="new description",
     *       required=true,
     *       type="string",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="beginTime",
     *       description="new begin time",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="endTime",
     *       description="new end time",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="minPeople",
     *       description="new minimum number of people",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="maxPeople",
     *       description="new maximum number of people",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function put($parameters)
    {
        $me = SLAuthentication::getMe();
        $theme = $parameters['theme'];
        $location = $parameters['location'];
        $description = $parameters['description'];
        $beginTime = $parameters['beginTime'];
        $endTime = $parameters['endTime'];
        $minPeople = $parameters['minPeople'];
        $maxPeople = $parameters['maxPeople'];

        if ($minPeople > $maxPeople) {
            throw new SLException(SLHTTPResponseCodes::BAD_REQUEST, SLErrorMessages::INVALID_PARAMETER . " ($minPeople > $maxPeople)");
        }

        if ($beginTime > $endTime) {
            throw new SLException(SLHTTPResponseCodes::BAD_REQUEST, SLErrorMessages::INVALID_PARAMETER . " ($beginTime > $endTime)");
        }

        if (time() > $endTime) {
            throw new SLException(SLHTTPResponseCodes::BAD_REQUEST, SLErrorMessages::INVALID_PARAMETER . " (now > $endTime)");
        }

        return $this->table('lunch')->add($me['userId'], $theme, $location, $description, $beginTime, $endTime, $minPeople, $maxPeople);
    }

    /**
     * @SWG\Api(
     *   path="/lunch",
     *   @SWG\Operation(
     *     method="DELETE",
     *     summary="deletes a lunch",
     *     notes="returns always true",
     *     type="boolean",
     *     @SWG\Parameter(
     *       name="lunchId",
     *       description="lunch id",
     *       required=true,
     *       type="integer",
     *       paramType="query"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="not logged in"),
     *     @SWG\ResponseMessage(code=403, message="has no permission"),
     *     @SWG\ResponseMessage(code=404, message="invalid lunch id"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function delete($parameters)
    {
        $me = SLAuthentication::getMe();
        $lunchId = $parameters['lunchId'];
        $lunch = $this->table('lunch')->get($lunchId);

        if ($lunch === false) {
            throw new SLException(SLHTTPResponseCodes::NOT_FOUND, SLErrorMessages::INVALID_RESOURCE_ID . " ($lunchId)");
        }

        if ($lunch['userId'] !== $me['userId']) {
            throw new SLException(SLHTTPResponseCodes::FORBIDDEN, SLErrorMessages::PERMISSION_ERROR);
        }

        $this->table('comment')->removeByLunchId($lunchId);
        $this->table('member')->removeByLunchId($lunchId);
        $this->table('lunch')->remove($lunchId);

        return true;
    }
}
