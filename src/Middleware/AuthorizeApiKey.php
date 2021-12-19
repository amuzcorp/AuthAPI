<?php
namespace Amuz\XePlugin\AuthAPI\Middleware;

use Closure;
use Amuz\XePlugin\AuthAPI\Models\ApiKey;
use Amuz\XePlugin\AuthAPI\Models\ApiKeyAccessEvent;
use Illuminate\Http\Request;

class AuthorizeApiKey
{
    const AUTH_HEADER = 'X-AMUZ-RESTAPI-AUTH';
    const AUTH_SECRET_HEADER = 'X-AMUZ-RESTAPI-AUTH';

    /**
     * Handle the incoming request
     *
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Contracts\Routing\ResponseFactory|mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header(self::AUTH_HEADER);
        $secret = $request->header(self::AUTH_SECRET_HEADER);
        if(!$secret){
            return response([
                'errors' => [[
                    'message' => 'need secret'
                ]]
            ], 401);
        }

        $siteKey = \XeSite::getCurrentSiteKey();
        $apiKey = ApiKey::getByKey($header,$siteKey);

        if ($apiKey instanceof ApiKey) {
            //check secret
            if($apiKey->secret != $secret) return response([
                    'errors' => [[
                        'message' => 'Invalid Secret Key'
                    ]]
                ], 401);

            //check ipaddress
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) $ipaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
            else $ipaddress = $_SERVER['REMOTE_ADDR'];
            $hostname = gethostbyaddr($ipaddress);

            if(!in_array($ipaddress,$apiKey->allowIps()) && in_array($hostname,$apiKey->allowIps())) return response([
                'errors' => [[
                    'message' => 'Disallow Access from ' . $hostname . '(' . $ipaddress . ')'
                ]]
            ], 401);

            $this->logAccessEvent($request, $apiKey);
            return $next($request);
        }

        return response([
            'errors' => [[
                'message' => 'Unauthorized'
            ]]
        ], 401);
    }

    /**
     * Log an API key access event
     *
     * @param Request $request
     * @param ApiKey  $apiKey
     */
    protected function logAccessEvent(Request $request, ApiKey $apiKey)
    {
        $event = new ApiKeyAccessEvent;
        $event->api_key_id = $apiKey->id;
        $event->ip_address = $request->ip();
        $event->url        = $request->fullUrl();
        $event->save();
    }
}
