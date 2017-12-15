<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 12/1/2017
 * Time: 12:17 PM
 */

namespace AppBundle\Helper;


use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;

class FormErrorMessageHelper
{

    const FORM_ERROR_KEY = 'form';

    /** @var array Array of errors.
     * Each element is an array of errors for a field, with the field title as the key.
     * For form-level errors, the key is FORM_ERROR_KEY.
     */
    protected $errors;

    public function __construct()
    {
        $this->errors = [];
    }

    public function recordFieldErrorMessage(string $fieldName, string $errorMessage) {
        if ( ! isset($this->errors[$fieldName]) ) {
            $this->errors[$fieldName] = [];
        }
        $this->errors[$fieldName][] = $errorMessage;
    }

    public function anyErrors() {
        return count($this->errors) > 0;
    }

    public function addErrorsToForm(Form $form) {
        foreach ($this->errors as $whatToAttachErrorsTo => $errorList) {
            foreach($errorList as $errorMessage) {
                if ( $whatToAttachErrorsTo === self::FORM_ERROR_KEY ) {
                    //Attach error to form.
                    $form->addError(new FormError($errorMessage));
                }
                else {
                    //Attach error to field.
                    $form->get($whatToAttachErrorsTo)->addError(new FormError($errorMessage));
                }
            }
        }
    }
}