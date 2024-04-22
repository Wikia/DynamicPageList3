<?php

namespace MediaWiki\Extension\DynamicPageList3;

class DplDebug {

	public static function getConfig(): array {
		static $config;
		if ( $config === null ) {
			$config = [
				'file-debug' => false,
				'force-query-exec' => false,
				'recursive-preprocess' => true,
			];

			$dplTest = \RequestContext::getMain()->getRequest()->getText( 'dpltest' );
			if ( $dplTest === 'standard' ) {
				$config = array_merge( $config, [
					'file-debug' => true,
					'force-query-exec' => true,
					'recursive-preprocess' => false,
				] );
			} elseif ( $dplTest === 'speedup' ) {
				$config = array_merge( $config, [
					'file-debug' => true,
					'force-query-exec' => true,
					'recursive-preprocess' => true,
				] );
			}

			$dplFileDebug = \RequestContext::getMain()->getRequest()->getBool( 'dplfiledebug' );
			if ( $dplFileDebug !== null ) {
				$config['file-debug'] = $dplFileDebug;
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

	public static function forceQueryExecution(): bool {
		return self::getConfig()['force-query-exec'];
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

	private static int $currentRun = 1;

	public static function nextRun(): void {
		self::$currentRun++;
	}

	public static function save( string $name, mixed $data ): void {
		if ( !self::isFileDebugEnabled() ) {
			return;
		}
		$name = self::sanitize( $name );
		if ( !is_string( $data ) ) {
			$data = json_encode( $data, JSON_PRETTY_PRINT );
		}

		$runStr = 'run-' . str_pad( (string)self::$currentRun, 4, '0', STR_PAD_LEFT );
		$outname = self::getOutputDir() . '/' . $runStr . '-' . $name;
		file_put_contents( $outname, $data );
	}
}
