<?php

namespace app\async\models;

use app\models\db\ConsultationSettingsTag;

class MotionTag extends TransferrableObject
{
    public $id;
    public $title;
    public $position;

    /**
     * @param ConsultationSettingsTag $tag
     * @return MotionTag
     * @throws \Exception
     */
    public static function createFromDbMotionObject(ConsultationSettingsTag $tag)
    {
        $obj           = new static('');
        $obj->id       = $tag->id;
        $obj->title    = $tag->title;
        $obj->position = $tag->position;
        
        return $obj;
    }
}
