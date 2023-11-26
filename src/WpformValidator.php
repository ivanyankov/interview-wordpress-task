<?php
namespace Wpform;

class WpformValidator {

    /**
     * Validates if the field is not empty.
     *
     * @param mixed $value The value to check.
     * @return bool True if not empty, false otherwise.
     */
    public static function required($value) {
        return !empty(trim($value));
    }

    /**
     * Validates an email address.
     *
     * @param string $email The email to validate.
     * @return bool True if valid, false otherwise.
     */
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validates a phone number (basic validation).
     *
     * @param string $phone The phone number to validate.
     * @return bool True if valid, false otherwise.
     */
    public static function phone($phone) {
        // Basic validation for phone number
        return preg_match('/^(\+?[\d\s]{6,}|00[\d\s]{6,})$/', $phone);
    }

    /**
     * Validates if the string is a valid date.
     *
     * @param string $dateStr The date string to validate.
     * @param string $format The expected date format. Default is 'Y-m-d'.
     * @return bool True if valid date, false otherwise.
     */
    public static function date($dateStr, $format = 'Y-m-d') {
        $dateTime = \DateTime::createFromFormat($format, $dateStr);
        return $dateTime && $dateTime->format($format) === $dateStr;
    }

    /**
     * Validates an array of fields with specified rules.
     *
     * @param array $fieldsRules Associative array of fields and their validation rules.
     * @param array $request The data to validate.
     * @param array $fieldsNameMapping The fields names to be mapped.
     * @return array Array of error messages for fields that failed validation.
     */
    public static function validate_fields(array $fieldsRules, array $request, array $fieldsNameMapping) : array {
        $errors = [];

        foreach ($fieldsRules as $field => $rules) {
            $value = isset($request[$field]) ? $request[$field] : '';

            foreach ($rules as $rule) {
                $isValid = true;

                switch ($rule) {
                    case 'required':
                        $isValid = self::required($value);
                        
                        if (!$isValid) {
                            $errors[$field][] = sprintf(__("The %s field is mandatory."), $fieldsNameMapping[$field], 'wpform-textdomain');
                        }

                        break;
                    case 'email':
                        $isValid = self::email($value);

                        if (!$isValid) {
                            $errors[$field][] = sprintf(__("The %s field must be valid email."), $fieldsNameMapping[$field], 'wpform-textdomain');
                        }

                        break;
                    case 'phone':
                        $isValid = self::phone($value);

                        if (!$isValid) {
                            $errors[$field][] = sprintf(__("The %s field must be valid phone."), $fieldsNameMapping[$field], 'wpform-textdomain');
                        }

                        break;
                    case 'date':
                        $isValid = self::date($value);

                        if (!$isValid) {
                            $errors[$field][] = sprintf(__("The %s field must be valid date."), $fieldsNameMapping[$field], 'wpform-textdomain');
                        }

                        break;
                }
            }
        }

        return $errors;
    }
}
