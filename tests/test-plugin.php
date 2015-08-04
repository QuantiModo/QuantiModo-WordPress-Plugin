<?php
class PluginTest extends WP_UnitTestCase {
    private $plugin; 
    function setUp() {
         
        parent::setUp();
        $this->plugin = $GLOBALS['QM-OAuth'];
     
    } // end setup   
	
	
    function testPluginInitialization() {
        $this->assertFalse( null == $this->plugin );
    } 
	
	
	public function testMoodShortCodeExists() {
		$this->assertTrue( method_exists($this->plugin, 'qmoa_mood_tracker') );
	}
}