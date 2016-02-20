<?php

namespace unit;

use app\components\diff\AffectedLinesFilter;
use app\components\diff\Diff2;
use app\components\diff\DiffRenderer;
use app\components\HTMLTools;
use app\components\LineSplitter;
use app\models\sectionTypes\TextSimple;
use Codeception\Specify;

class AffectedLinesFilterTest extends TestBase
{


    /**
     */
    public function testUlLiInserted()
    {

        // 'Inserted LIs should be shown'
        $in     = '<ul class="inserted"><li>Oamoi a Maß und no a Maß</li></ul>';
        $expect = [[
            'text'     => '<ul class="inserted"><li>Oamoi a Maß und no a Maß</li></ul>',
            'lineFrom' => 0,
            'lineTo'   => 0,
        ]];
        $out    = AffectedLinesFilter::splitToAffectedLines($in, 1);
        $this->assertEquals($expect, $out);
    }

    /**
     */
    public function testUlLiWithLineBreaks()
    {
        // 'Line breaks within lists'

        $in = '<p>###LINENUMBER###Do nackata Wurscht i hob di ' .
            '###LINENUMBER###narrisch gean, Diandldrahn Deandlgwand vui ' .
            '###LINENUMBER###Do nackata</p>' . "\n" .
            '<ul><li>###LINENUMBER###Do nackata Wurscht i hob di narrisch gean, Diandldrahn Deandlgwand ###LINENUMBER###huift vui woaß?</li></ul>' . "\n" .
            '<ul class="inserted"><li>Oamoi a Maß und no a Maß des basd scho wann griagd ma nacha wos z’dringa do Meidromml, oba a fescha Bua!</li></ul>';

        $expect = [[
            'text'     => '<ul class="inserted"><li>Oamoi a Maß und no a Maß des basd scho wann griagd ma nacha wos z’dringa do Meidromml, oba a fescha Bua!</li></ul>',
            'lineFrom' => 5,
            'lineTo'   => 5
        ]];

        $out = AffectedLinesFilter::splitToAffectedLines($in, 1);

        $this->assertEquals($expect, $out);
    }

    /**
     */
    public function testUlLiInlineFormatted()
    {
        $in     = '<div style="color:#FF0000; margin: 0; padding: 0;"><ul class="deleted"><li>###LINENUMBER###Woibbadinga noch da Giasinga Heiwog Biazelt mechad mim Spuiratz, soi zwoa.</li></ul></div>';
        $expect = [[
            'text'     => '<div style="color:#FF0000;margin:0;padding:0;"><ul class="deleted"><li>###LINENUMBER###Woibbadinga noch da Giasinga Heiwog Biazelt mechad mim Spuiratz, soi zwoa.</li></ul></div>',
            'lineFrom' => 1,
            'lineTo'   => 1,
        ]];

        $out = AffectedLinesFilter::splitToAffectedLines($in, 1);

        $this->assertEquals($expect, $out);
    }


    /**
     */
    public function testMultilineParagraph()
    {
        $diff           = '<p class="deleted">###LINENUMBER###Leonhardifahrt ma da middn. Greichats an naa do. Af Schuabladdla Leonhardifahrt ###LINENUMBER###Marei, des um Godds wujn Biakriagal! Hallelujah sog i, luja schüds nei koa des ###LINENUMBER###is schee jedza hogg di hera dringma aweng Spezi nia Musi. Wurschtsolod jo mei is ###LINENUMBER###des schee gor Ramasuri ozapfa no gfreit mi i hob di liab auffi, Schbozal. Hogg ###LINENUMBER###di hera nia need Biakriagal so schee, Schdarmbeaga See.</p>';
        $affectedBlocks = AffectedLinesFilter::splitToAffectedLines($diff, 1);
        $expected       = [[
            'text'     => $diff,
            'lineFrom' => 1,
            'lineTo'   => 5,
        ]];
        $this->assertEquals($expected, $affectedBlocks);

    }


    /**
     */
    public function testInsertedLi()
    {
        $htmlDiff       = '<ul><li>###LINENUMBER###Woibbadinga noch da Giasinga Heiwog Biazelt mechad mim Spuiratz, soi zwoa.</li></ul><ul class="inserted"><li>Oamoi a Maß und no a Maß des basd scho wann griagd ma nacha wos z’dringa do Meidromml, oba a fescha Bua!</li></ul>';
        $affectedBlocks = AffectedLinesFilter::splitToAffectedLines($htmlDiff, 1);
        $expected       = [[
            'text'     => '<ul class="inserted"><li>Oamoi a Maß und no a Maß des basd scho wann griagd ma nacha wos z’dringa do Meidromml, oba a fescha Bua!</li></ul>',
            'lineFrom' => 1,
            'lineTo'   => 1,
        ]];
        $this->assertEquals($expected, $affectedBlocks);
    }

    /**
     */
    public function testBasic()
    {
        /*
        $orig = '<p>Test Test</p><ul><li>Point 1</li><li>Point 2</li></ul><p>Test</p>';
        $new  = '<p>Test Test2</p><ul><li>Point 1</li><li>Point 2</li><li>Test 3</li></ul>';

        $newParagraphs  = HTMLTools::sectionSimpleHTML($new);
        $origParas = HTMLTools::sectionSimpleHTML($orig);
        $origParas = LineSplitter::addLineNumbersToParagraphs($origParagraphs, 80);
        $diff           = new Diff2();
        $diffParas = $diff->compareSectionedHtml($origParas, $newParas, DiffRenderer::FORMATTING_CLASSES);
        */
        $diffParas = [
            '<p>###LINENUMBER###Test Test<ins>2</ins></p>',
            '<ul><li>###LINENUMBER###Point 1</li></ul>',
            '<ul><li>###LINENUMBER###Point 2</li></ul>',
            '<p class="deleted">###LINENUMBER###Test</p><ul class="inserted"><li>Test 3</li></ul>',
        ];
        $expected  = [
            ['text' => '<p>###LINENUMBER###Test Test<ins>2</ins></p>', 'lineFrom' => 1, 'lineTo' => 1],
            ['text' => '<p class="deleted">###LINENUMBER###Test</p><ul class="inserted"><li>Test 3</li></ul>', 'lineFrom' => 4, 'lineTo' => 4],
        ];
        $diff      = implode('', $diffParas);
        $lines     = AffectedLinesFilter::splitToAffectedLines($diff, 1);

        $this->assertEquals($expected, $lines);
    }


    /**
     */
    public function testFilterAffected()
    {
        $lines    = [
            ['text' => '###LINENUMBER###Test Test<ins>2</ins>', 'lineFrom' => 1, 'lineTo' => 1],
        ];
        $expected = [
            ['text' => '###LINENUMBER###Test Test<ins>2</ins>', 'lineFrom' => 1, 'lineTo' => 1],
        ];
        $filtered = AffectedLinesFilter::filterAffectedBlocks($lines);
        $this->assertEquals($expected, $filtered);


        $lines    = [
            ['text' => '###LINENUMBER###Test Test<ins>2', 'lineFrom' => 1, 'lineTo' => 1],
            ['text' => '###LINENUMBER###Test Test2', 'lineFrom' => 2, 'lineTo' => 2],
            ['text' => '###LINENUMBER###Test Test2</ins>', 'lineFrom' => 3, 'lineTo' => 3],
            ['text' => '###LINENUMBER###Test Test2', 'lineFrom' => 4, 'lineTo' => 4],
        ];
        $expected = [
            ['text' => '###LINENUMBER###Test Test<ins>2', 'lineFrom' => 1, 'lineTo' => 1],
            ['text' => '###LINENUMBER###Test Test2', 'lineFrom' => 2, 'lineTo' => 2],
            ['text' => '###LINENUMBER###Test Test2</ins>', 'lineFrom' => 3, 'lineTo' => 3],
        ];
        $filtered = AffectedLinesFilter::filterAffectedBlocks($lines);
        $this->assertEquals($expected, $filtered);
    }


    /**
     */
    public function testGroupAffectedLines()
    {
        $orig   = [
            [
                'text'     => '<del>###LINENUMBER###Leonhardifahrt ma da middn. Greichats an naa do. </del>',
                'lineFrom' => 16,
                'lineTo'   => 16,
            ], [
                'text'     => '<del>###LINENUMBER###Marei, des um Godds wujn Biakriagal! </del>',
                'lineFrom' => 17,
                'lineTo'   => 17,
            ], [
                'text'     => '<del>###LINENUMBER###is schee jedza hogg di hera dringma aweng Spezi nia Musi. </del>',
                'lineFrom' => 18,
                'lineTo'   => 18,
            ],
        ];
        $expect = [
            [
                'text'     => '<del>###LINENUMBER###Leonhardifahrt ma da middn. Greichats an naa do. </del>' .
                    '<del>###LINENUMBER###Marei, des um Godds wujn Biakriagal! </del>' .
                    '<del>###LINENUMBER###is schee jedza hogg di hera dringma aweng Spezi nia Musi. </del>',
                'lineFrom' => 16,
                'lineTo'   => 18,
            ]
        ];

        $filtered = AffectedLinesFilter::groupAffectedDiffBlocks($orig);
        $this->assertEquals($expect, $filtered);
    }

    /**
     */
    public function testGroupedWordings()
    {
        $orig     = [
            [
                'text'     => '<del>###LINENUMBER###Leonhardifahrt ma da middn. Greichats an naa do.</del>',
                'lineFrom' => 16,
                'lineTo'   => 16,
            ], [
                'text'     => '<del>###LINENUMBER###Marei, des um Godds wujn Biakriagal!</del>',
                'lineFrom' => 17,
                'lineTo'   => 17,
            ], [
                'text'     => '<del>###LINENUMBER###is schee jedza hogg di hera dringma aweng Spezi nia Musi.</del>',
                'lineFrom' => 18,
                'lineTo'   => 18,
            ], [
                'text'     => '<del>###LINENUMBER###is schee jedza hogg di hera dringma aweng Spezi nia Musi.</del>',
                'lineFrom' => 20,
                'lineTo'   => 20,
            ],
        ];
        $filtered = AffectedLinesFilter::groupAffectedDiffBlocks($orig);

        $this->assertContains('Von Zeile 16 bis 18 löschen', TextSimple::formatDiffGroup($filtered));
        $this->assertContains('In Zeile 20 löschen', TextSimple::formatDiffGroup($filtered));
    }

    /**
     */
    public function testNestedLists()
    {
        $diffParas = [
            '<p>###LINENUMBER###Test Test<ins>2</ins></p>',
            '<ul><li><p>###LINENUMBER###Point <del>1</del><ins>2</ins></p><ul><li>###LINENUMBER###Nested 1</li><li><ins>Nested 2</ins></li></ul></li></ul>',
            '<ul><li>###LINENUMBER###Point 2</li></ul>',
            '<ul><li>###LINENUMBER###Test 3</li></ul>',
            '<p class="deleted">###LINENUMBER###Test</p>'
        ];
        $expected  = [
            ['text' => '<p>###LINENUMBER###Test Test<ins>2</ins></p><ul><li><p>###LINENUMBER###Point <del>1</del><ins>2</ins></p></li></ul>', 'lineFrom' => 1, 'lineTo' => 2],
            ['text' => '<ul><li><ul><li><ins>Nested 2</ins></li></ul></li></ul>', 'lineFrom' => 3, 'lineTo' => 3],
            ['text' => '<p class="deleted">###LINENUMBER###Test</p>', 'lineFrom' => 6, 'lineTo' => 6]
        ];
        $diff      = implode('', $diffParas);
        $lines     = AffectedLinesFilter::splitToAffectedLines($diff, 1);
        $this->assertEquals($expected, $lines);
    }


    /**
     * @throws \app\models\exceptions\Internal
     */
    public function testFilterAffectedBlocks1()
    {
        $in       = [
            [
                'text'     => 'Gipfe Servas des wiad a Mordsgaudi',
                'lineFrom' => 14,
                'lineTo'   => 14,
                'newLine'  => false,
            ], [
                'text'     => 'Gipfe Servas des wiad a Mordsgaudi',
                'lineFrom' => 15,
                'lineTo'   => 15,
                'newLine'  => false,
            ], [
                'text'     => '<del>Leonhardifahrt ma da middn. Greichats an naa do.',
                'lineFrom' => 16,
                'lineTo'   => 16,
                'newLine'  => false,
            ], [
                'text'     => 'Marei, des um Godds wujn Biakriagal!',
                'lineFrom' => 17,
                'lineTo'   => 17,
                'newLine'  => false,
            ], [
                'text'     => 'is schee jedza hogg di hera dringma aweng Spezi nia Musi.</del>',
                'lineFrom' => 18,
                'lineTo'   => 1,
                'newLine'  => false,
            ],
        ];
        $expect   = [
            [
                'text'     => '<del>Leonhardifahrt ma da middn. Greichats an naa do.',
                'lineFrom' => 16,
                'lineTo'   => 16,
                'newLine'  => false,
            ], [
                'text'     => 'Marei, des um Godds wujn Biakriagal!',
                'lineFrom' => 17,
                'lineTo'   => 17,
                'newLine'  => false,
            ], [
                'text'     => 'is schee jedza hogg di hera dringma aweng Spezi nia Musi.</del>',
                'lineFrom' => 18,
                'lineTo'   => 1,
                'newLine'  => false,
            ],
        ];
        $filtered = AffectedLinesFilter::filterAffectedBlocks($in);
        $this->assertEquals($expect, $filtered);
    }

    /**
     * @throws \app\models\exceptions\Internal
     */
    public function testFilterAffectedBlocks2()
    {
        $in       = [
            [
                'text'     => 'Test1 <del>Test2</del> Test3 <ins>Test4</ins> <del>Test2</del> Test3 <ins>Test4</ins>',
                'lineFrom' => 15,
                'lineTo'   => 15,
                'newLine'  => false,
            ], [
                'text'     => 'Bla 1.',
                'lineFrom' => 16,
                'lineTo'   => 16,
                'newLine'  => false,
            ], [
                'text'     => 'Bla 2.',
                'lineFrom' => 17,
                'lineTo'   => 17,
                'newLine'  => false,
            ], [
                'text'     => 'Test1 <del>Test2</del> Test3 <ins>Test4</ins> <del>Test2</del> Test3 <ins>Test4</ins>',
                'lineFrom' => 18,
                'lineTo'   => 18,
                'newLine'  => false,
            ],
        ];
        $expect   = [
            [
                'text'     => 'Test1 <del>Test2</del> Test3 <ins>Test4</ins> <del>Test2</del> Test3 <ins>Test4</ins>',
                'lineFrom' => 15,
                'lineTo'   => 15,
                'newLine'  => false,
            ], [
                'text'     => 'Test1 <del>Test2</del> Test3 <ins>Test4</ins> <del>Test2</del> Test3 <ins>Test4</ins>',
                'lineFrom' => 18,
                'lineTo'   => 18,
                'newLine'  => false,
            ],
        ];
        $filtered = AffectedLinesFilter::filterAffectedBlocks($in);
        $this->assertEquals($expect, $filtered);
    }


    /**
     * @throws \app\models\exceptions\Internal
     */
    public function testFilterAffectedBlocks3()
    {
        $in       = [
            [
                'text'     => 'Test1 <del>Test2</del> Test3 <ins>Test4</ins> <del>Test2</del> Test3 <ins>Test4</ins>',
                'lineFrom' => 15,
                'lineTo'   => 15,
                'newLine'  => false,
            ], [
                'text'     => 'Bla 1.',
                'lineFrom' => 16,
                'lineTo'   => 16,
                'newLine'  => false,
            ], [
                'text'     => 'Bla 2.',
                'lineFrom' => 17,
                'lineTo'   => 17,
                'newLine'  => false,
            ], [
                'text'     => 'Test1 <del>Test2</del> Test3 <ins>Test4</ins> <del>Test2</del> Test3 <ins>Test4</ins>',
                'lineFrom' => 18,
                'lineTo'   => 18,
                'newLine'  => false,
            ], [
                'text'     => 'Bla 2.',
                'lineFrom' => 19,
                'lineTo'   => 19,
                'newLine'  => false,
            ], [
                'text'     => 'Test1 <del>Test2</del> Test3 <ins>Test4</ins> <del>Test2</del> Test3 <ins>Test4</ins>',
                'lineFrom' => 20,
                'lineTo'   => 20,
                'newLine'  => false,
            ],
        ];
        $expect   = [
            [
                'text'     => 'Test1 <del>Test2</del> Test3 <ins>Test4</ins> <del>Test2</del> Test3 <ins>Test4</ins>',
                'lineFrom' => 15,
                'lineTo'   => 15,
                'newLine'  => false,
            ], [
                'text'     => 'Test1 <del>Test2</del> Test3 <ins>Test4</ins> <del>Test2</del> Test3 <ins>Test4</ins>',
                'lineFrom' => 18,
                'lineTo'   => 18,
                'newLine'  => false,
            ], [
                'text'     => 'Bla 2.',
                'lineFrom' => 19,
                'lineTo'   => 19,
                'newLine'  => false,
            ], [
                'text'     => 'Test1 <del>Test2</del> Test3 <ins>Test4</ins> <del>Test2</del> Test3 <ins>Test4</ins>',
                'lineFrom' => 20,
                'lineTo'   => 20,
                'newLine'  => false,
            ],
        ];
        $filtered = AffectedLinesFilter::filterAffectedBlocks($in);
        $this->assertEquals($expect, $filtered);
    }
}