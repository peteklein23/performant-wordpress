<?php

use Carbon_Fields\Field\Field;
use PeteKlein\Performant\Posts\Meta\PostMetaCollection;
use PeteKlein\Performant\Fields\FieldBase;

class PostMetaCollectionTest extends \Codeception\Test\Unit
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
    public function testListInitializesEmpty()
    {
        $collection = new PostMetaCollection();
        $list = $collection->list();

        $this->assertCount(0, $list);
    }

    public function testFieldGetsAddedAndReturned()
    {
        $collection = new PostMetaCollection();
        $field = $this->getMockForAbstractClass(FieldBase::class, ['key', 'Label']);
        $collection->addField($field);
        $returnedField = $collection->getField('key');

        $this->assertNotNull($returnedField);
    }

    public function testFieldReturnsNullWhenNotPresent()
    {
        $collection = new PostMetaCollection();
        $returnedField = $collection->getField('key');

        $this->assertNull($returnedField);
    }
}