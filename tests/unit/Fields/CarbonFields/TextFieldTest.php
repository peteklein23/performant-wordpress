<?php

use PeteKlein\Performant\Fields\CarbonFields\TextField;

class TextFieldTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testGetSelectionSQL()
    {
        $textField = new TextField('name', 'Name');
        $selectionSql = $textField->getSelectionSQL();

        $this->assertEquals("= '_name'", $selectionSql);
    }

    public function testGetValue()
    {
        $result = new \stdClass();
        $result->meta_key = '_name';
        $result->meta_value = 'Jim';
        $metaResults = [
            $result
        ];
        $textField = new TextField('name', 'Name');
        $value = $textField->getValue($metaResults);

        $this->assertEquals('Jim', $value);
    }
}