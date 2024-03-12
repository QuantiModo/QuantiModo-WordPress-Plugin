<?php
// Include the WordPress test suite
require_once '../../../../../tests/phpunit/includes/bootstrap.php';



class Test_Page_Creation extends WP_UnitTestCase {

	function setUp(): void
	{
		parent::setUp();
		// Activate your plugin
		activate_plugin('petition-the-government/petition-the-government.php');
	}

	function tearDown(): void
	{
		// Deactivate your plugin
		deactivate_plugins('your-plugin-directory/your-plugin-file.php');
		parent::tearDown();
	}

	function test_page_created() {
		// The logic here depends on how your plugin creates a page
		// For instance, if your plugin creates a page with a specific title, check for that
		$page_title = 'Thank You for Signing the Petition!';

		$args = array(
			'post_type' => 'page',
			'post_status' => 'publish',
			'title' => $page_title
		);

		$query = new WP_Query($args);

		// Assert that the page exists
		$this->assertNotEmpty($query->posts);

		// Optionally, confirm that the page is published
		$this->assertEquals('publish', get_post_status($query));
	}
}

