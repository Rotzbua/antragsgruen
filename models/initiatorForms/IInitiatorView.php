<?php

namespace app\models\initiatorForms;

use app\controllers\Base;
use app\models\db\Amendment;
use app\models\db\AmendmentSupporter;
use app\models\db\Consultation;
use app\models\db\Motion;
use app\models\db\MotionSupporter;
use app\models\exceptions\FormError;
use app\models\forms\MotionEditForm;

interface IInitiatorView
{
    /**
     * @param string $name
     * @return bool
     */
    public function isValidName($name);


    /**
     * @param Motion $motion
     * @throws FormError
     */
    public function submitInitiatorViewMotion(Motion $motion);


    /**
     * @param Amendment $amendment
     * @throws FormError
     */
    public function submitInitiatorViewAmendment(Amendment $amendment);


    /**
     * @param Consultation $consultation
     * @param MotionEditForm $editForm
     * @return string
     */
    public function getMotionInitiatorForm(Consultation $consultation, MotionEditForm $editForm, Base $controller);


    /**
     * @param Motion $motion
     * @return MotionSupporter[]
     */
    public function getMotionSupporters(Motion $motion);

    /**
     * @return AmendmentSupporter[]
     */
    public function getAmendmentSupporters();
}