<?php

/** @var \Codeception\Scenario $scenario */
$I = new AcceptanceTester($scenario);
$I->populateDBData1();

$I->wantTo('test the excel-export of motions from the admin interface');
$I->loginAndGotoStdAdminPage();
$file = $I->downloadLink('.motionExcel1');
if (strlen($file) == 0) {
    $I->fail('File has no content');
}
