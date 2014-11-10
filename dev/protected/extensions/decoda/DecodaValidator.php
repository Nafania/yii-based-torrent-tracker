<?php

/**
 * Decoda Validator Class
 *
 * Validate string for parsing errors
 *
 * @author Vadim Vorotilov <fant.geass@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php Licensed under The MIT License
 * @link    http://milesj.me/code/php/decoda
 * @version 1.0
 * @uses YiiDecoda
 */
class DecodaValidator extends CValidator
{

    /**
     * @var boolean whether the attribute value can be null or empty. Defaults to true,
     * meaning that if the attribute is empty, it is considered valid.
     */
    public $allowEmpty = true;

    /**
     * @var Decoda
     * YiiDecoda component
     */
    public $decoda;

    /**
     * @var array
     * Errors types that need to be validated
     * example: array(Decoda::ERROR_NESTING, Decoda::ERROR_SCOPE)
     */
    public $errorTypes;

    /**
     * @var bool
     * if true and no errors then attribute will contain parsed string
     */
    public $useParsed = true;

    /**
     * Validates the attribute of the object.
     * If there is any error, the error message is added to the object.
     * @param CModel $object the object being validated
     * @param string $attribute the attribute being validated
     */
    protected function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;

        if($this->allowEmpty && $this->isEmpty($value)) {
            return;
        }

        if ($this->decoda == null) {
            $this->decoda =& Yii::app()->decoda;
        }

        if ($this->errorTypes === null || in_array(Decoda::ERROR_ALL, $this->errorTypes)) {
            $this->errorTypes = array(Decoda::ERROR_CLOSING, Decoda::ERROR_NESTING, Decoda::ERROR_SCOPE);
        }

        $parsedValue  = $this->decoda->parse($value);
        $decodaErrors = $this->decoda->getErrors();
        foreach ($decodaErrors as $error) {
            switch ($error['type']) {
                case Decoda::ERROR_NESTING:
                    $nesting[] = $error['tag'];
                    break;
                case Decoda::ERROR_CLOSING:
                    $closing[] = $error['tag'];
                    break;
                case Decoda::ERROR_SCOPE:
                    $scope[] = $error['child'] . ' in ' . $error['parent'];
                    break;
            }
        }

        $errors = array();
        if (in_array(Decoda::ERROR_NESTING, $this->errorTypes) && !empty($nesting)) {
            $errors[] = Yii::t('decoda', 'The following tags have been nested in the wrong order: {tags}',
                array('{tags}' => implode(', ', $nesting)));
        }

        if (in_array(Decoda::ERROR_CLOSING, $this->errorTypes) && !empty($closing)) {
            $errors[] = Yii::t('decoda', 'The following tags have no closing tag: {tags}',
                array('{tags}' => implode(', ', $closing)));
        }

        if (in_array(Decoda::ERROR_SCOPE, $this->errorTypes) && !empty($scope)) {
            $errors[] =  Yii::t('decoda', 'The following tags can not be placed within a specific tag: {tags}',
                array('{tags}' => implode(', ', $scope)));
        }

        if (!empty($errors)) {
            foreach ($errors as $message) {
                $this->addError($object, $attribute, $message);
            }
        } elseif ($this->useParsed) {
            $object->$attribute = $parsedValue;
        }
    }
}
