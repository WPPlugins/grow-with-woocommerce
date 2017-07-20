<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoloader
 *
 * @since  1.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WooCommerce_Grow_Autoloader {
	/**
	 * The extension class prefix
	 */
	const PREFIX = 'WooCommerce_Grow';

	/**
	 * @var string The classes path
	 */
	private $path;

	/**
	 * @var string Folder path to the file
	 */
	private $folder_path;

	/**
	 * Constructor
	 *
	 * @param string $path
	 * @param string $folder
	 */
	public function __construct( $path, $folder = 'includes' ) {
		$this->path        = $path;
		$this->folder_path = $folder;
	}

	/**
	 * Include the plugin classes
	 *
	 * @param $class_name
	 */
	public function load_classes( $class_name ) {
		// We will include only our classes
		if ( 0 !== strpos( $class_name, self::PREFIX ) ) {
			return;
		}

		// Path to the classes folder
		$includes_path = $this->path . '/' . $this->folder_path;

		// The file name we want to load
		$include_file_name = 'class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';

		// Search the classes folder and return all files and directories
		$directory_iterator = new RecursiveDirectoryIterator( $includes_path, RecursiveDirectoryIterator::SKIP_DOTS );
		$files_and_folders  = new RecursiveIteratorIterator( $directory_iterator, RecursiveIteratorIterator::SELF_FIRST );

		// Look through all files and folders
		foreach ( $files_and_folders as $file ) {
			$file_name = $file->getFilename();
			// Match the file by name
			if ( $include_file_name === $file_name ) {
				// If we have a match inlclude the file and bail
				$path = $file->getRealPath();
				require( $path );

				return;
			}
		}
	}
}