<?php 

use PeteKlein\Performant\Posts\Meta\PostMetaCollection;

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
    public function testInitializeEmpty()
    {
        $collection = new PostMetaCollection();
        $list = $collection->list();

        $this->assertCount(0, $list);
    }
}