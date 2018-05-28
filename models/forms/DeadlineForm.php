<?php

namespace app\models\forms;

use app\components\Tools;
use app\models\db\ConsultationMotionType;
use app\models\exceptions\Internal;

class DeadlineForm
{
    public $deadlinesMotions;
    public $deadlinesAmendments;
    public $deadlinesComments;
    public $deadlinesMerging;

    /**
     * @param ConsultationMotionType $motionType
     * @return DeadlineForm
     */
    public static function createFromMotionType(ConsultationMotionType $motionType)
    {
        $form                      = new DeadlineForm();
        $form->deadlinesMotions    = $motionType->getDeadlines(ConsultationMotionType::DEADLINE_MOTIONS);
        $form->deadlinesAmendments = $motionType->getDeadlines(ConsultationMotionType::DEADLINE_AMENDMENTS);
        $form->deadlinesComments   = $motionType->getDeadlines(ConsultationMotionType::DEADLINE_COMMENTS);
        $form->deadlinesMerging    = $motionType->getDeadlines(ConsultationMotionType::DEADLINE_MERGING);
        return $form;
    }

    /**
     * @param array $input
     * @return DeadlineForm
     */
    public static function createFromInput($input)
    {
        $form = new DeadlineForm();

        if (isset($input['formtypeComplex'])) {
            $form->createFromInputComplex($input);
        } else {
            $form->createFromInputSimple($input);
        }
        return $form;
    }

    /**
     * @param array $input
     */
    public function createFromInputSimple($input)
    {
        try {
            $motionsEnd    = Tools::dateBootstraptime2sql($input['motionsSimple']);
            $amendmentsEnd = Tools::dateBootstraptime2sql($input['amendmentsSimple']);
        } catch (Internal $e) {
            $motionsEnd    = null;
            $amendmentsEnd = null;
        }

        $this->deadlinesComments   = [];
        $this->deadlinesMerging    = [];
        $this->deadlinesMotions    = [['start' => null, 'end' => $motionsEnd, 'title' => null]];
        $this->deadlinesAmendments = [['start' => null, 'end' => $amendmentsEnd, 'title' => null]];
    }

    /**
     * @param array $rows
     * @return array
     */
    private function parseComplexRows($rows)
    {
        $deadlines = [];
        for ($i = 0; $i < count($rows['start']); $i++) {
            try {
                $start = Tools::dateBootstraptime2sql($rows['start'][$i]);
                $end   = Tools::dateBootstraptime2sql($rows['end'][$i]);
            } catch (Internal $e) {
                $start = null;
                $end   = null;
            }
            if (!$start && !$end) {
                continue;
            }
            if ($start && $end && Tools::dateSql2timestamp($start) > Tools::dateSql2timestamp($end)) {
                continue;
            }
            $deadlines[] = ['start' => $start, 'end' => $end, 'title' => $rows['title'][$i]];
        }
        return $deadlines;
    }

    /**
     * @param array $input
     */
    public function createFromInputComplex($input)
    {
        $this->deadlinesMotions    = [];
        $this->deadlinesAmendments = [];
        $this->deadlinesMerging    = [];
        $this->deadlinesComments   = [];

        if (isset($input[ConsultationMotionType::DEADLINE_MOTIONS])) {
            $this->deadlinesMotions = $this->parseComplexRows($input[ConsultationMotionType::DEADLINE_MOTIONS]);
        }
        if (isset($input[ConsultationMotionType::DEADLINE_AMENDMENTS])) {
            $this->deadlinesAmendments = $this->parseComplexRows($input[ConsultationMotionType::DEADLINE_AMENDMENTS]);
        }
        if (isset($input[ConsultationMotionType::DEADLINE_MERGING])) {
            $this->deadlinesMerging = $this->parseComplexRows($input[ConsultationMotionType::DEADLINE_MERGING]);
        }
        if (isset($input[ConsultationMotionType::DEADLINE_COMMENTS])) {
            $this->deadlinesComments = $this->parseComplexRows($input[ConsultationMotionType::DEADLINE_COMMENTS]);
        }
    }

    /**
     * @return boolean
     */
    public function isSimpleConfiguration()
    {
        if (count($this->deadlinesComments) > 0 || count($this->deadlinesMerging) > 0) {
            return false;
        }
        $simpleMotion    = (
            count($this->deadlinesMotions) === 0 ||
            (count($this->deadlinesMotions) === 1 && !$this->deadlinesMotions[0]['start'] &&
                !$this->deadlinesMotions[0]['title'])
        );
        $simpleAmendment = (
            count($this->deadlinesAmendments) === 0 ||
            (count($this->deadlinesAmendments) === 1 && !$this->deadlinesAmendments[0]['start'] &&
                !$this->deadlinesAmendments[0]['title'])
        );
        return ($simpleMotion && $simpleAmendment);
    }

    /**
     * @return null|string
     */
    public function getSimpleMotionsDeadline()
    {
        if (count($this->deadlinesMotions) === 0) {
            return null;
        }
        return $this->deadlinesMotions[0]['end'];
    }

    /**
     * @return null|string
     */
    public function getSimpleAmendmentsDeadline()
    {
        if (count($this->deadlinesAmendments) === 0) {
            return null;
        }
        return $this->deadlinesAmendments[0]['end'];
    }

    /**
     * @return array
     */
    public function generateDeadlineArray()
    {
        return [
            ConsultationMotionType::DEADLINE_MOTIONS    => $this->deadlinesMotions,
            ConsultationMotionType::DEADLINE_AMENDMENTS => $this->deadlinesAmendments,
            ConsultationMotionType::DEADLINE_MERGING    => $this->deadlinesMerging,
            ConsultationMotionType::DEADLINE_COMMENTS   => $this->deadlinesComments,
        ];
    }
}
