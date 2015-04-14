<?php

namespace app\models\sectionTypes;

use app\models\exceptions\FormError;
use yii\helpers\Html;

class Title extends ISectionType
{

    /**
     * @return string
     */
    public function getMotionFormField()
    {
        // @TODO Max Length
        $type = $this->section->consultationSetting;
        return '<fieldset class="form-group">
            <label for="sections_' . $type->id . '">' . Html::encode($type->title) . '</label>
            <input type="text" class="form-control" id="sections_' . $type->id . '"' .
        ' name="sections[' . $type->id . ']" value="' . Html::encode($this->section->data) . '">
        </fieldset>';
    }

    /**
     * @return string
     */
    public function getAmendmentFormField()
    {
        return $this->getMotionFormField();
    }

    /**
     * @param $data
     * @throws FormError
     */
    public function setMotionData($data)
    {
        $this->section->data = $data;
    }

    /**
     * @param string $data
     * @throws FormError
     */
    public function setAmendmentData($data)
    {
        $this->setMotionData($data);
    }

    /**
     * @return string
     */
    public function showSimple()
    {
        return Html::encode($this->section->data);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return ($this->section->data == '');
    }
}