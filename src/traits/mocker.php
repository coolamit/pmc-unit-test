<?php
/**
 * Mocker for unit test.
 *
 * @package pmc-unit-test
 */

namespace PMC\Unit_Test\Traits;

use Error;
use PMC\Unit_Test\Mocks\Factory;

/**
 * Class Mocker.
 */
trait Mocker {
	protected $mock     = false;
	protected $_counter = 0;

	public function __construct() {
		$this->mock = Factory::get_instance();
		$parents    = class_parents( __CLASS__ );

		if ( ! empty( $parents ) ) {
			// @codeCoverageIgnoreStart DO NOT remove this code: We really don't need to cover this code.
			// Check if parent constructor is accessible before calling it.
			try {
				parent::__construct();
			} catch ( Error $e ) {
				error_log( 'Parent constructor call failed: ' . $e->getMessage() );
			}
			// @codeCoverageIgnoreEnd
		}
	}

	/**
	 * @return $this
	 * @codeCoverageIgnore doesn't require code coverage for this trait function
	 */
	public function mock() {
		return $this;
	}

	public function generate( array $args = [] ) {

		$post = false;
		if ( empty( $args ) ) {
			$args = [];
		}

		$this->_counter++;

		array_walk(
			$args,
			function( &$item ) {
				if ( is_string( $item ) ) {
					$item = sprintf( $item, $this->_counter );
				}
			}
		);

		$test_factory = Factory::get_instance()->test_factory();

		if ( is_object( $test_factory ) ) {
			if ( isset( $args['post_type'] ) && 'attachment' === $args['post_type'] ) {
				$post = $test_factory->attachment->create_and_get( $args );
			} else {
				$post = $test_factory->post->create_and_get( $args );
			}
		} else {
			// @TODO: Add code to generate the post outside of unit test to allow generating sample data for initial dev site setup
			// Probably hook up to a wp plugin that does lipsum generation
			throw new \Error( 'Mocker not proeprly initialize before calling generate function' );
		}

		return $post;

	}

}
