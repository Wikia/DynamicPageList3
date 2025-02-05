<?php

namespace MediaWiki\Extension\DynamicPageList3\Validator;

class MysqlRegex {
	public function isValid( string $option ): bool {
		$pattern = '/\{\d{1,2},\d{1,2}}/';
		$regexIsCorrect = preg_match_all( $pattern, $option );
		// Check if every opening { has two digits and a comma between and a closing }
		if ( $regexIsCorrect !== substr_count( $option, '{' ) ) {
			return false;
		}

		// Check if every closing } has an opening { with two digits and a comma between them
		if ( $regexIsCorrect !== substr_count( $option, '}' ) ) {
			return false;
		}

		// Check if it has both { and } with two numbers separated by a comma between them
		if ( preg_match( $pattern, $option ) === 0 ) {
			return false;
		}

		return true;
	}
}
