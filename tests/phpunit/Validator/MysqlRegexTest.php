<?php

namespace MediaWiki\Extension\DynamicPageList3\Validator;

use PHPUnit\Framework\TestCase;

class MysqlRegexTest extends TestCase {

	public function testIsValid(): void {
		$validator = new MysqlRegex();

		// Test valid cases
		$this->assertTrue( $validator->isValid( '{1,2}' ) );
		$this->assertTrue( $validator->isValid( '{10,20}' ) );
		$this->assertTrue( $validator->isValid( 'text{1,2}text' ) );
		$this->assertTrue( $validator->isValid( '^P{1,2}' ) );
		$this->assertTrue( $validator->isValid( '^P{1,2}P' ) );

		// Test invalid cases
		$this->assertFalse( $validator->isValid( '{1,2' ) );
		$this->assertFalse( $validator->isValid( '1,2}' ) );
		$this->assertFalse( $validator->isValid( '{1,2}{3,4' ) );
		$this->assertFalse( $validator->isValid( 'text{1,2text' ) );
		$this->assertFalse( $validator->isValid( 'text1,2}text' ) );
	}
}
