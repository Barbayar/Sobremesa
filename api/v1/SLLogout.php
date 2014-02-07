<?php
/**
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   basePath="/api/v1",
 *   resourcePath="logout"
 * )
 */
class SLLogout extends SLResource
{
    protected $parameters = array(
        'get' => array(
        ),
    );

    /**
     * @SWG\Api(
     *   path="/logout",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="logs out",
     *     notes="returns always true",
     *     type="boolean",
     *     @SWG\ResponseMessage(code=400, message="invalid parameters"),
     *     @SWG\ResponseMessage(code=500, message="an internal error occured")
     *   )
     * )
     */
    protected function get($parameters)
    {
        // copied from http://php.net/manual/en/function.session-destroy.php
        $_SESSION = array();
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();

        return true;
    }
}
