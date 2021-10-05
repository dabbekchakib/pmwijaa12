<?php

namespace Kanboard\Plugin\CompletedComplexity\Validator;

use Kanboard\Validator\BaseValidator;
use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * SubTask Time Tracking Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class SubTaskTimeTrackingValidator extends BaseValidator
{

    /**
     * Validate SubTackTimeTracking from form
     *
     * @param  array   $values           Form values
     *
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validates(array $values)
    {
        $valids = [];
        $errors = [];

        foreach ($values as $key => $value) {
            $rules = array(
                new Validators\Required('id', t('The task time tracking id is required')),
                new Validators\Date('start', t('Invalid date'), $this->dateParser->getParserFormats()),
                new Validators\Date('end', t('Invalid date'), $this->dateParser->getParserFormats()),
            );

            $v = new Validator($value, $rules);

            $valids[$key] = $v->execute();
            $errors[$key] = $v->getErrors();
        }

        return array(
            $valids,
            $errors
        );
    }
}
