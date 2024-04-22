<?php

namespace MediaWiki\Extension\DynamicPageList3;

class DplDebug {

	public static function getConfig(): array {
		static $config;
		if ( $config === null ) {
			$config = [
				'file-debug' => false,
				'recursive-preprocess' => true,
			];
			if ( \RequestContext::getMain()->getRequest()->getBool( 'dplfiledebug' ) ) {
				$config['file-debug'] = true;
			}
			$dplTest = \RequestContext::getMain()->getRequest()->getText( 'dpltest' );
			if ( $dplTest === 'standard' ) {
				$config['file-debug'] = true;
				$config['recursive-preprocess'] = false;
			} elseif ( $dplTest === 'speedup' ) {
				$config['file-debug'] = true;
				$config['recursive-preprocess'] = true;
			}
		}

		return $config;
	}

	public static function useRecursivePreprocess(): bool {
		return self::getConfig()['recursive-preprocess'];
	}

	public static function isFileDebugEnabled(): bool {
		return self::getConfig()['recursive-preprocess'];
	}

	public static function getRequestName(): string {
		static $requestName;
		if ( !$requestName ) {
			$safeTitle = self::sanitize( \RequestContext::getMain()->getTitle()->getPrefixedText() );
			$requestName = sprintf( "%s-%s",
				\wfTimestamp( TS_DB, $_SERVER['REQUEST_TIME_FLOAT'] ),
				$safeTitle );
		}

		return $requestName;
	}

	public static function getOutputDir(): string {
		static $dir;
		if ( !$dir ) {
			global $IP;

			$dir = sprintf( "%s/cache/dpldebug/%s", $IP, self::getRequestName() );
			if ( !is_dir( $dir ) ) {
				mkdir( $dir, recursive: true );
			}
		}

		return $dir;
	}

	public static function sanitize( string $s ): string {
		$s = preg_replace( '/' . preg_quote( '/\\|', '/' ) . '/', '_', $s );
		$s = preg_replace( '/[{}]/', '', $s );

		return $s;
	}

	public static function save( string $name, mixed $data ): void {
		if ( !self::isFileDebugEnabled() ) {
			return;
		}
		$name = self::sanitize( $name );
		if ( !is_string( $data ) ) {
			$data = json_encode( $data, JSON_PRETTY_PRINT );
		}

		$outname = self::getOutputDir() . '/' . $name;
		file_put_contents( $outname, $data );
	}
}
