<?php

namespace FpDbTest;

use Exception;
use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    // Check __SKIP__ in multidimensional array
    public function in_array_recursive($needle, $haystack) {
        foreach ($haystack as $element) {
            if ($element === $needle) {
                return true;
            } elseif (is_array($element)) {
                if ($this->in_array_recursive($needle, $element)) {
                    return true;
                }
            }
        }

        return false;
    }

    // Implement the buildQuery method
    public function buildQuery(string $query, array $args = []): string
    {
        $index = 0; // Index to track the arguments array
        $skip_value = $this->skip(); // Get the skip value
        // Check if the block contains the skip value

        $query = preg_replace_callback('/\{.*?\}/s', function($matches) use ($skip_value, $args) {
            if ($this->in_array_recursive($skip_value, $args)) {
                return ''; // Remove the block
            }
            return str_replace(['{', '}'], '', $matches[0]); // Return block as is if no skip value found
        }, $query);

        $query = preg_replace_callback('/\?(d|f|a|#|)/', function($matches) use ($args, &$index, $skip_value) {
            $value = $args[$index++] ?? null; // Get the next argument or null
            switch ($matches[1]) {
                case 'd':
                    return is_null($value) ? 'NULL' : intval($value);
                case 'f':
                    return is_null($value) ? 'NULL' : floatval($value);
                case 'a':
                    if (is_array($value)) {
                        // Check if it's an associative array
                        $assoc = array_keys($value) !== range(0, count($value) - 1);
                        if ($assoc) {
                            return implode(', ', array_map(function($key, $val) {
                                // Format as `key` = 'value'
                                $val = is_null($val) ? 'NULL' : (is_numeric($val) ? $val : "'" . $this->escape($val) . "'");
                                return "`$key` = $val";
                            }, array_keys($value), $value));
                        } else {
                            // If not associative, handle as a list of values
                            return implode(', ', array_map(function($v) {
                                return is_numeric($v) ? $v : "'" . $this->escape($v) . "'";
                            }, $value));
                        }
                    } else {
                        throw new Exception('Expected an array for ?a placeholder');
                    }
                case '#':
                    if (is_array($value)) {
                        return implode(', ', array_map(function($v) { return "`" . $this->escape($v) . "`"; }, $value));
                    } else {
                        return "`" . $this->escape($value) . "`";
                    }
                default:
                    return is_null($value) ? 'NULL' : "'" . $this->escape($value) . "'";
            }
        }, $query);

        return $query;
    }

    public function skip()
    {
        return '__SKIP__'; // Уникальное значение для индикации пропуска блока
    }

    // Helper method to escape strings
    private function escape($value): string
    {
        return $this->mysqli->real_escape_string($value);
    }
}
