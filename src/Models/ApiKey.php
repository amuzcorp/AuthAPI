<?php

namespace Amuz\XePlugin\AuthAPI\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use SoftDeletes;

    const EVENT_NAME_CREATED     = 'created';
    const EVENT_NAME_ACTIVATED   = 'activated';
    const EVENT_NAME_DEACTIVATED = 'deactivated';
    const EVENT_NAME_DELETED     = 'deleted';

    protected static $nameRegex = '/^[a-z0-9-]{1,255}$/';

    protected $table = 'api_keys';

    /**
     * Get the related ApiKeyAccessEvents records
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accessEvents()
    {
        return $this->hasMany(ApiKeyAccessEvent::class, 'api_key_id');
    }

    /**
     * Get the related ApiKeyAdminEvents records
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adminEvents()
    {
        return $this->hasMany(ApiKeyAdminEvent::class, 'api_key_id');
    }

    public function getAllowIps(){
        return json_dec($this->allow_ip ?: '[""]');
    }

    public function getAllowServices(){
        return json_dec($this->allow_services ?: "[]");
    }

    public function hasGroup($group_id){
        return in_array($group_id,$this->getAllowServices());
    }

    public function hasService($group_id, $service_id){
        return (in_array($group_id,$this->getAllowServices()) || in_array($service_id,$this->getAllowServices()));
    }

    /**
     * Bootstrapping event handlers
     */
    public static function boot()
    {
        parent::boot();

        static::created(function(ApiKey $apiKey) {
            self::logApiKeyAdminEvent($apiKey, self::EVENT_NAME_CREATED);
        });

        static::updated(function($apiKey) {

            $changed = $apiKey->getDirty();

            if (isset($changed) && array_get('active',$changed,0) === 1) {
                self::logApiKeyAdminEvent($apiKey, self::EVENT_NAME_ACTIVATED);
            }

            if (isset($changed) && array_get('active',$changed,1) === 0) {
                self::logApiKeyAdminEvent($apiKey, self::EVENT_NAME_DEACTIVATED);
            }

        });

        static::deleted(function($apiKey) {
            self::logApiKeyAdminEvent($apiKey, self::EVENT_NAME_DELETED);
        });
    }

    /**
     * Generate a secure unique API key
     *
     * @return string
     */
    public static function generate()
    {
        do {
            $key = Str::random(32);
        } while (self::keyExists($key));

        return $key;
    }
    /**
     * Generate a secure unique API key
     *
     * @return string
     */
    public static function genSecret()
    {
        return Str::random(200);
    }

    /**
     * Get ApiKey record by key value
     *
     * @param string $key
     * @return bool
     */
    public static function getByKey($key,$siteKey = null)
    {
        if($siteKey == null) $siteKey = \XeSite::getCurrentSiteKey();
        return self::where([
            'key'    => $key,
            'site_key' => $siteKey,
            'active' => 1
        ])->first();
    }

    /**
     * Check if key is valid
     *
     * @param string $key
     * @return bool
     */
    public static function isValidKey($key)
    {
        return self::getByKey($key) instanceof self;
    }

    /**
     * Check if name is valid format
     *
     * @param string $name
     * @return bool
     */
    public static function isValidName($name)
    {
        return (bool) preg_match(self::$nameRegex, $name);
    }

    /**
     * Check if a key already exists
     *
     * Includes soft deleted records
     *
     * @param string $key
     * @return bool
     */
    public static function keyExists($key)
    {
        return self::where('key', $key)->withTrashed()->first() instanceof self;
    }

    /**
     * Check if a name already exists
     *
     * Does not include soft deleted records
     *
     * @param string $name
     * @return bool
     */
    public static function nameExists($name,$site_key = null)
    {
        $site_key = $site_key ?: \XeSite::getCurrentSiteKey();
        return self::where('name', $name)->where('site_key',$site_key)->first() instanceof self;
    }

    /**
     * Log an API key admin event
     *
     * @param ApiKey $apiKey
     * @param string $eventName
     */
    protected static function logApiKeyAdminEvent(ApiKey $apiKey, $eventName)
    {
        $event             = new ApiKeyAdminEvent;
        $event->api_key_id = $apiKey->id;
        $event->ip_address = request()->ip();
        $event->event      = $eventName;
        $event->save();
    }
}
