<?php

namespace App\Utilities;

trait CommonUtilities
{
    /**
     * Check if a model matches itself
     * @param self $model
     * @return bool
     */
    public function matches(self $model): bool
    {
        return $this->id() === $model->id();
    }

    /**
     * Check if a model matches a given ID
     * @param int $id
     * @return bool
     */
    public function matchesId(int $id): bool
    {
        return $this->id() === $id;
    }

    /**
     * Check if a provided string is an email or not
     *
     * @param string $string
     * @return bool
     */
    public function isEmail(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Escape a string. This is useful for preventing XSS attacks.
     *
     * @param string $string
     * @return string
     * @throws \Exception
     */
    public function escapeString(string $string): string
    {
        try {
            return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while escaping the string.');
        }
    }

    /**
     * Strip tags from a string. This is useful for preventing XSS attacks.
     *
     * @param string $string
     * @return string
     * @throws \Exception
     */
    public function stripTags(string $string): string
    {
        try {
            return strip_tags($string);
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while stripping tags from the string.');
        }
    }

    /**
     * Clean up a string. This is useful for preventing XSS attacks.
     * (This method is a combination of the escapeString and stripTags methods.)
     *
     * @param string $string
     * @return string
     * @throws \Exception
     */
    public function cleanString(string $string): string
    {
        try {
            return $this->escapeString($this->stripTags($string));
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while cleaning the string.');
        }
    }

    /**
     * Check if a string is a valid URL
     *
     * @param string $string
     * @return bool
     */
    public function isValidUrl(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_URL);
    }

    /**
     * Check if a string is a valid IP address
     *
     * @param string $string
     * @return bool
     */
    public function isValidIpAddress(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_IP);
    }

    /**
     * Stringify a variable
     *
     * @param mixed $variable
     * @return string
     */
    public function stringify(mixed $variable): string
    {
        // Determine the type of the variable
        $type = gettype($variable);

        // Stringify the variable based on its type
        switch ($type) {
            case 'boolean':
                return $variable ? 'true' : 'false';
            case 'integer':
            case 'double':
                return (string) $variable;
            case 'string':
                return $variable;
            case 'array':
                return json_encode($variable);
            case 'object':
                return json_encode($variable);
            case 'resource':
                return 'resource';
            case 'NULL':
                return 'null';
            default:
                return 'unknown';
        }
    }

    /**
     * Check if a variable is empty
     *
     * @param mixed $variable
     * @return bool
     */
    public function isEmpty(mixed $variable): bool
    {
        return empty($variable);
    }

    /**
     * Check if variable contains a value
     * Like the contains() method in Laravel collections, but to support arrays and strings as well
     * @param mixed $target The variable to search in
     * @param mixed $variable The key to search for
     * @param mixed $value The value to search for
     * @return bool
     */
    public function contains(mixed $target, mixed $variable, mixed $value): bool
    {
        // Switch based on the type of the target
        switch (gettype($target)) {
            // Check if the target is an array
            case is_array($target):
                // Check if the array contains the key
                if (array_key_exists($variable, $target)) {
                    // Check if the value matches
                    return $target[$variable] === $value;
                }
                return false;
            // Check if the target is a string
            case is_string($target):
                // Check if the string contains the value
                return strpos($target, $value) !== false;
            // Check if the target is an object
            case is_object($target):
                // Check if the object has the key
                if (property_exists($target, $variable)) {
                    // Check if the value matches
                    return $target->$variable === $value;
                }
                return false;
            // Check if the target is a collection
            case $target instanceof \Illuminate\Support\Collection:
                // Check if the collection contains the key
                if ($target->has($variable)) {
                    // Check if the value matches
                    return $target->get($variable) === $value;
                }
                return false;
            // Check if the target is a model
            case $target instanceof \Illuminate\Database\Eloquent\Model:
                // Check if the model has the key
                if ($target->has($variable)) {
                    // Check if the value matches
                    return $target->get($variable) === $value;
                }
                return false;
            // Check if the target is a collection of models
            case $target instanceof \Illuminate\Database\Eloquent\Collection:
                // Check if the collection contains the key
                if ($target->has($variable)) {
                    // Check if the value matches
                    return $target->get($variable) === $value;
                }
                return false;
            // Check if the target is a number
            case is_numeric($target):
                // Check if the number matches the value
                return $target === $value;
            // Check if the target is a boolean
            case is_bool($target):
                // Check if the boolean matches the value
                return $target === $value;
            // Check if the target is null
            case is_null($target):
                // Check if the target is null
                return false;
            // Check if the target is a resource
            case is_resource($target):
                // Check if the resource matches the value
                return $target === $value;
            // Check if the target is a callable
            case is_callable($target):
                // Check if the callable matches the value
                return $target === $value;
            // Check if the target is an unknown type
            default:
                return false;

        }
    }
}
