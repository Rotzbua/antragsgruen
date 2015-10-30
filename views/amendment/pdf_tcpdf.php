<?php

use app\models\db\Amendment;

/**
 * @var Amendment $amendment
 */

$pdfLayout = $amendment->motion->motionType->getPDFLayoutClass();
$pdf       = $pdfLayout->createPDFClass();

header('Content-type: application/pdf; charset=UTF-8');


$initiators = [];
foreach ($amendment->getInitiators() as $init) {
    $initiators[] = $init->getNameWithResolutionDate(false);
}
$initiatorsStr = implode(', ', $initiators);

// set document information
$pdf->SetCreator('Antragsgrün');
$pdf->SetAuthor(implode(', ', $initiators));
$pdf->SetTitle(Yii::t('amend', 'amendment') . ' ' . $amendment->getTitle());
$pdf->SetSubject(Yii::t('amend', 'amendment') . ' ' . $amendment->getTitle());

\app\views\amendment\LayoutHelper::printToPDF($pdf, $pdfLayout, $amendment);

$pdf->Output('Amendment_' . str_replace('Ä', 'AE', $amendment->titlePrefix) . '.pdf', 'I');

die();
