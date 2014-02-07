<?php
/**
 * @SWG\Model(id="Me",required="userId,displayName,data")
 */
class SWGModelMe
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
 *   resourcePath="login"
 * )
 */
class SLLogin extends SLResource
{
    protected $parameters = array(
        'post' => array(
            'username' => SLValidators::SINGLE_LINE,
            'password' => SLValidators::SINGLE_LINE,
        ),
    );

    /**
     * @SWG\Api(
     *   path="/login",
     *   @SWG\Operation(
     *     method="POST",
     *     summary="logs in",
     *     notes="returns user information",
     *     type="Me",
     *     @SWG\Parameter(
     *       name="username",
     *       description="user name",
     *       required=true,
     *       type="string",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="password",
     *       description="password",
     *       required=true,
     *       type="string",
     *       paramType="query"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=401, message="invalid user name or password"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function post($parameters)
    {
        session_regenerate_id(true);
        $username = $parameters['username'];
        $password = $parameters['password'];

        $userInformation = SLAuthentication::authenticate($username, $password);

        if (is_null($userInformation)) {
            throw new SLException(SLHTTPResponseCodes::UNAUTHORIZED, SLErrorMessages::AUTHORIZATION_FAILED);
        }

        $user = $this->table('user')->getByUsername($username);

        if ($user === false) {
            $this->table('user')->add(
                $username, 
                $userInformation['email'], 
                $userInformation['displayName'], 
                $userInformation['data']
            );

            $user = $this->table('user')->getByUsername($username);
        }

        $_SESSION['me'] = $user;

        return array(
            'userId' => $user['userId'],
            'displayName' => $user['displayName'],
            'data' => $user['data'],
        );
    }
}
