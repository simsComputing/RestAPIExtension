<?php
namespace SC\FOSRestExtensionBundle\Tests\Parser;

use PHPUnit\Framework\TestCase;
use SC\FOSRestExtensionBundle\Parser\ResourceParser;

class ResourceParserTest extends TestCase
{
    public function testCamelize() 
    {
        $parser = new ResourceParser();
        $test_str = "underscored_string";
        
        $output = $parser->camelize($test_str);
        $this->assertEquals("underscoredString", $output);
    }
    
    public function testUnderscore()
    {
        $parser = new ResourceParser();
        $test_str = "camelizedString";
        
        $output = $parser->underscore($test_str);
        $this->assertEquals("camelized_string", $output);
    }
    
    public function testCheckIsDate()
    {
        $parser = new ResourceParser();
        $date = "2018-12-12T14:29:33+00:01";
        $date2 = "2018-22-12T14:29:33+00:01";
        $date3 = "29-12-12T14:29:33T+00:01";
        $date4 = "2018-12-33T14:29:33+00:01";
        $date5 = "2018-12-1214:29:33+00:01";
        $date6 = "2018-12-12T24:29:33+00:01";
        $date7 = "2018-12-12T14:79:33+00:01";
        $date8 = "2018-12-12T14:29:73+00:01";
        $date9 = "2018-12-12T14:29:3300:01";
        $date10 = "2018-12-12T14:29:33+00:01";
        
        $this->assertEquals(1, $parser->checkIsDate($date));
        $this->assertEquals(0, $parser->checkIsDate($date2));
        $this->assertEquals(0, $parser->checkIsDate($date3));
        $this->assertEquals(0, $parser->checkIsDate($date4));
        $this->assertEquals(0, $parser->checkIsDate($date5));
        $this->assertEquals(0, $parser->checkIsDate($date6));
        $this->assertEquals(0, $parser->checkIsDate($date7));
        $this->assertEquals(0, $parser->checkIsDate($date8));
        $this->assertEquals(0, $parser->checkIsDate($date9));
        $this->assertEquals(1, $parser->checkIsDate($date10));
    }
}