<?php

use app\components\UrlHelper;
use app\models\db\Amendment;
use app\models\db\Motion;
use app\models\db\VotingBlock;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var VotingBlock[] $votingBlocks
 * @var array $data
 */

/** @var \app\controllers\ConsultationController $controller */
$controller = $this->context;
$layout     = $controller->layoutParams;

$this->title = \Yii::t('con', 'proposal_title');
$layout->addBreadcrumb(\Yii::t('con', 'proposal_bc'));

echo '<h1>' . Html::encode($this->title) . '</h1>';

?>
    <div class="content">

    </div>
<?php

foreach ($data as $dataRow) {
    /** @var Motion $motion */
    $motion = $dataRow['motion'];
    /** @var Amendment[] $amendments */
    $amendments = $dataRow['amendments'];
    /** @var VotingBlock[] $votingBlocks */
    $votingBlocks = $dataRow['votingBlocks'];

    if (count($amendments) == 0) {
        continue;
    }

    $coveredAmendments = [];
    $proposalStati     = Amendment::getProposedStatiNames();

    ?>
    <section class="motionHolder motionHolder<?= $motion->id ?> proposedProcedureOverview">
        <h2 class="green">
            <?php
            echo Html::encode($motion->getTitleWithPrefix());
            if ($motion->status == Motion::STATUS_WITHDRAWN) {
                echo ' <span class="withdrawn">(' . \Yii::t('structure', 'STATUS_WITHDRAWN') . ')</span>';
            }
            ?></h2>
        <div class="content">
            <?php
            foreach ($votingBlocks as $votingBlock) {
                ?>
                <table class="table votingTable votingTable<?= $votingBlock->id ?>">
                    <caption>
                        <?= \Yii::t('con', 'proposal_table_voting') ?>:
                        <?= Html::encode($votingBlock->title) ?>
                    </caption>
                    <thead>
                    <tr>
                        <th class="prefix"><?= \Yii::t('con', 'proposal_table_amend') ?></th>
                        <th class="initiator"><?= \Yii::t('con', 'proposal_table_initiator') ?></th>
                        <th class="procedure"><?= \Yii::t('con', 'proposal_table_proposal') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($votingBlock->amendments as $amendment) {
                        $coveredAmendments[] = $amendment->id;
                        $classes = ['amendment' . $amendment->id];
                        if ($amendment->status == Amendment::STATUS_WITHDRAWN) {
                            $classes[] = 'withdrawn';
                        }
                        ?>
                        <tr class="<?= implode(' ', $classes) ?>">
                            <td>
                                <?= Html::a($amendment->getShortTitle(), UrlHelper::createAmendmentUrl($amendment)) ?>
                            </td>
                            <td><?= $amendment->getInitiatorsStr() ?></td>
                            <td>
                                <?php
                                echo $amendment->getFormattedProposalStatus();
                                if ($amendment->proposalExplanation) {
                                    echo '<div class="explanation">';
                                    echo Html::encode($amendment->proposalExplanation);
                                    echo '</div>';
                                }
                                ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            }

            /** @var Amendment[] $uncoveredAmendments */
            $uncoveredAmendments = array_filter($amendments, function (Amendment $amendment) use ($coveredAmendments) {
                return !in_array($amendment->id, $coveredAmendments);
            });
            if (count($uncoveredAmendments)) {
                ?>
                <table class="table proposalTable">
                    <?php
                    if (count($votingBlocks) > 0) { ?>
                        <caption><?= \Yii::t('con', 'proposal_table_others') ?></caption>
                        <?php
                    } ?>
                    <thead>
                    <tr>
                        <th class="prefix"><?= \Yii::t('con', 'proposal_table_amend') ?></th>
                        <th class="initiator"><?= \Yii::t('con', 'proposal_table_initiator') ?></th>
                        <th class="procedure"><?= \Yii::t('con', 'proposal_table_proposal') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($uncoveredAmendments as $amendment) {
                        $classes = ['amendment' . $amendment->id];
                        if ($amendment->status == Amendment::STATUS_WITHDRAWN) {
                            $classes[] = 'withdrawn';
                        }
                        ?>
                        <tr class="<?= implode(' ', $classes) ?>">
                            <td>
                                <?= Html::a($amendment->getShortTitle(), UrlHelper::createAmendmentUrl($amendment)) ?>
                            </td>
                            <td><?= $amendment->getInitiatorsStr() ?></td>
                            <td>
                                <?php
                                echo $amendment->getFormattedProposalStatus();
                                if ($amendment->proposalExplanation) {
                                    echo '<div class="explanation">';
                                    echo \Yii::t('con', 'proposal_explanation') . ': ';
                                    echo Html::encode($amendment->proposalExplanation);
                                    echo '</div>';
                                }
                                ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </div>
    </section>
    <?php
}