<?php

/**
 * PHSD is a PHP Library for handling data storage with expiration.
 * 
 * @category Library
 * @package  PHSD
 */
class PHSD {
    private static $data = [];
    private static $file = '.env';

    /**
     * Initialize the data storage from the file.
     */
    private static function init() {
        if (file_exists(self::$file)) {
            self::$data = json_decode(file_get_contents(self::$file), true) ?: [];
        } else {
            self::$data = [];
        }
        self::expireAllExpired();
    }

    /**
     * Save the data storage to the file, each data on a new line.
     */
    private static function save() {
        $jsonData = json_encode(self::$data, JSON_PRETTY_PRINT);
        file_put_contents(self::$file, $jsonData . PHP_EOL);
    }

    /**
     * Add a key-value pair to the data storage with optional expiration.
     *
     * @param string $key The key to add.
     * @param mixed $value The value to add.
     * @param int|null $expiration Expiration time in minutes (optional).
     * @return void
     */
    public static function add($key, $value, $expiration = null) {
        self::init();
        self::$data[$key] = [
            'value' => $value,
            'expiration' => self::calculateExpiration($expiration)
        ];
        self::save();
    }

    /**
     * Update the value and expiration of an existing key.
     *
     * @param string $key The key to update.
     * @param mixed $value The new value.
     * @param int|null $expiration New expiration time in minutes (optional).
     * @return void
     */
    public static function update($key, $value, $expiration = null) {
        self::init();
        if (self::exists($key)) {
            self::$data[$key]['value'] = $value;
            self::$data[$key]['expiration'] = self::calculateExpiration($expiration);
            self::save();
        }
    }

    /**
     * Remove a key from the data storage.
     *
     * @param string $key The key to remove.
     * @return void
     */
    public static function remove($key) {
        self::init();
        unset(self::$data[$key]);
        self::save();
    }

    /**
     * Get the value of a key from the data storage.
     *
     * @param string $key The key to retrieve.
     * @return mixed|null The value or null if the key does not exist or has expired.
     */
    public static function get($key) {
        self::init();
        if (self::exists($key)) {
            return self::$data[$key]['value'];
        }
        return null;
    }

    /**
     * Get all key-value pairs from the data storage.
     *
     * @return array The entire data storage.
     */
    public static function getAll() {
        self::init();
        return self::$data;
    }

    /**
     * Expire a specific key immediately.
     *
     * @param string $key The key to expire.
     * @return void
     */
    public static function expire($key) {
        if (self::exists($key)) {
            self::$data[$key]['expiration'] = time();
            self::save();
        }
    }

    /**
     * Expire all keys immediately.
     *
     * @return void
     */
    public static function expireAll() {
        foreach (self::$data as $key => $value) {
            self::$data[$key]['expiration'] = time();
        }
        self::save();
    }

    /**
     * Get details of all expired keys.
     *
     * @return array The expired key-value pairs.
     */
    public static function getExpiredDetails() {
        $expired = [];
        foreach (self::$data as $key => $value) {
            if ($value['expiration'] !== null && $value['expiration'] <= time()) {
                $expired[$key] = $value;
            }
        }
        return $expired;
    }

    /**
     * Get details of all active keys.
     *
     * @return array The active key-value pairs.
     */
    public static function getActiveDetails() {
        $active = [];
        foreach (self::$data as $key => $value) {
            if ($value['expiration'] === null || $value['expiration'] > time()) {
                $active[$key] = $value;
            }
        }
        return $active;
    }

    /**
     * Remove all keys from the data storage.
     *
     * @return void
     */
    public static function removeAll() {
        self::$data = [];
        self::save();
    }

    /**
     * Remove all expired keys from the data storage.
     *
     * @return void
     */
    public static function expireAllExpired() {
        $expiredDetails = self::getExpiredDetails();
        foreach ($expiredDetails as $key => $value) {
            unset(self::$data[$key]);
        }
        self::save();
    }

    /**
     * Check if a key exists in the data storage.
     *
     * @param string $key The key to check.
     * @return bool True if the key exists, false otherwise.
     */
    private static function exists($key) {
        return isset(self::$data[$key]);
    }

    /**
     * Calculate the expiration timestamp based on the given expiration time in minutes.
     *
     * @param int|null $expiration Expiration time in minutes (optional).
     * @return int|null The expiration timestamp or null if no expiration.
     */
    private static function calculateExpiration($expiration) {
        if ($expiration === null || $expiration === 0) {
            return null;
        } else {
            return time() + ($expiration * 60);
        }
    }
}
?>
