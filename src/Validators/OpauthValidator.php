<?php

declare(strict_types=1);

namespace Silverstripe\Opauth\Validators;

use SilverStripe\Forms\Validation\RequiredFieldsValidator;

/**
 * OpauthValidator
 * Merges with a custom validator to bring to the user some super error messages
 * @author Will Morgan <@willmorgan>
 */
class OpauthValidator extends RequiredFieldsValidator
{
    public function php($data): bool
    {
        $customValid = true;
        $requiredValid = null;
        // If there's a custom validator set, validate with that too
        if ($validatorClass = self::config()->custom_validator) {
            $custom = new $validatorClass();
            $custom->setForm($this->form);
            $customValid = $custom->php($data);
            if (!$customValid) {
                if ($requiredValid) {
                    $this->errors = array();
                }

                $this->errors = array_merge($this->errors, $custom->errors);
            }
        }

        return $customValid && $requiredValid;
    }

}
