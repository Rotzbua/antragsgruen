<?php

use app\components\DateTools;
use app\components\Tools;
use app\components\HTMLTools;
use app\models\db\ConsultationMotionType;
use app\models\forms\DeadlineForm;
use yii\helpers\Html;

/**
 * @var ConsultationMotionType $motionType
 * @var string $locale
 */

$deadlineForm = DeadlineForm::createFromMotionType($motionType);

$simpleDeadlineMotions    = Tools::dateSql2bootstraptime($deadlineForm->getSimpleMotionsDeadline());
$simpleDeadlineAmendments = Tools::dateSql2bootstraptime($deadlineForm->getSimpleAmendmentsDeadline());

?>
<h3><?= \Yii::t('admin', 'motion_deadlines_head') ?></h3>

<div class="checkbox">
    <?php
    echo HTMLTools::fueluxCheckbox(
        'deadlines[formtypeComplex]',
        \Yii::t('admin', 'motion_deadline_complex'),
        !$deadlineForm->isSimpleConfiguration(),
        ['id' => 'deadlineFormTypeComplex']
    );
    ?>
</div>

<div class="form-group deadlineTypeSimple">
    <label class="col-md-4 control-label" for="typeSimpleDeadlineMotions">
        <?= \Yii::t('admin', 'motion_sdeadline_mot') ?>
    </label>
    <div class="col-md-8">
        <div class="input-group date datetimepicker" id="typeDeadlineMotionsHolder">
            <input id="typeSimpleDeadlineMotions" type="text" class="form-control"
                   name="deadlines[motionsSimple]"
                   value="<?= Html::encode($simpleDeadlineMotions) ?>" data-locale="<?= Html::encode($locale) ?>">
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
        </div>
    </div>
</div>

<div class="form-group deadlineTypeSimple">
    <label class="col-md-4 control-label" for="typeSimpleDeadlineAmendments">
        <?= \Yii::t('admin', 'motion_sdeadline_amend'); ?>
    </label>
    <div class="col-md-8">
        <div class="input-group date datetimepicker" id="typeDeadlineAmendmentsHolder">
            <input id="typeSimpleDeadlineAmendments" type="text" class="form-control"
                   name="deadlines[amendmentsSimple]"
                   value="<?= Html::encode($simpleDeadlineAmendments) ?>" data-locale="<?= Html::encode($locale) ?>">
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
        </div>
    </div>
</div>

<div class="hidden deadlineRowTemplate">
    <?php
    $data = ['start' => null, 'end' => null, 'title' => null];
    echo $this->render('_type_deadline_row', ['locale' => $locale, 'type' => 'TEMPLATE', 'data' => $data]);
    ?>
</div>

<?php
$type = ConsultationMotionType::DEADLINE_MOTIONS;
?>
<section class="deadlineTypeComplex deadlineHolder motionDeadlines" data-type="<?= $type ?>">
    <h4><?= \Yii::t('admin', 'motion_cdeadline_mot') ?>:</h4>
    <div class="deadlineList">
        <?php
        foreach ($motionType->getDeadlinesByType($type) as $deadline) {
            echo $this->render('_type_deadline_row', ['locale' => $locale, 'type' => $type, 'data' => $deadline]);
        }
        ?>
    </div>
    <button type="button" class="btn btn-link btn-xs deadlineAdder">
        <span class="glyphicon glyphicon-plus-sign"></span>
        <?= \Yii::t('admin', 'motion_cdeadline_add') ?>
    </button>
</section>

<?php
$type = ConsultationMotionType::DEADLINE_AMENDMENTS;
?>
<div class="deadlineTypeComplex deadlineHolder amendmentDeadlines" data-type="<?= $type ?>">
    <h4><?= \Yii::t('admin', 'motion_cdeadline_amend') ?>:</h4>
    <div class="deadlineList">
        <?php
        foreach ($motionType->getDeadlinesByType($type) as $deadline) {
            echo $this->render('_type_deadline_row', ['locale' => $locale, 'type' => $type, 'data' => $deadline]);
        }
        ?>
    </div>
    <button type="button" class="btn btn-link btn-xs deadlineAdder">
        <span class="glyphicon glyphicon-plus-sign"></span>
        <?= \Yii::t('admin', 'motion_cdeadline_add') ?>
    </button>
</div>

<?php
$type = ConsultationMotionType::DEADLINE_MERGING;
?>
<div class="deadlineTypeComplex deadlineHolder mergingDeadlines" data-type="<?= $type ?>">
    <h4><?= \Yii::t('admin', 'motion_cdeadline_merge') ?>:</h4>
    <div class="deadlineList">
        <?php
        foreach ($motionType->getDeadlinesByType($type) as $deadline) {
            echo $this->render('_type_deadline_row', ['locale' => $locale, 'type' => $type, 'data' => $deadline]);
        }
        ?>
    </div>
    <button type="button" class="btn btn-link btn-xs deadlineAdder">
        <span class="glyphicon glyphicon-plus-sign"></span>
        <?= \Yii::t('admin', 'motion_cdeadline_add') ?>
    </button>
</div>

<?php
$type = ConsultationMotionType::DEADLINE_COMMENTS;
?>
<div class="deadlineTypeComplex deadlineHolder commentDeadlines" data-type="<?= $type ?>">
    <h4><?= \Yii::t('admin', 'motion_cdeadline_com') ?>:</h4>
    <div class="deadlineList">
        <?php
        foreach ($motionType->getDeadlinesByType($type) as $deadline) {
            echo $this->render('_type_deadline_row', ['locale' => $locale, 'type' => $type, 'data' => $deadline]);
        }
        ?>
    </div>
    <button type="button" class="btn btn-link btn-xs deadlineAdder">
        <span class="glyphicon glyphicon-plus-sign"></span>
        <?= \Yii::t('admin', 'motion_cdeadline_add') ?>
    </button>
</div>

<div class="deadlineTypeComplex checkbox">
    <?php
    echo HTMLTools::fueluxCheckbox(
        'activateDeadlineDebugMode',
        \Yii::t('admin', 'motion_deadline_debug'),
        DateTools::isDeadlineDebugModeActive($motionType->getMyConsultation()),
        ['id' => 'deadlineDebugMode']
    );
    ?>
</div>
