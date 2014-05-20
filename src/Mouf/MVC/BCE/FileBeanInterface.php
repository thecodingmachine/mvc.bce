<?php
namespace Mouf\MVC\BCE;

use Mouf\Database\DAOInterface;

use Mouf\Html\Widgets\FileUploaderWidget\SimpleFileUploaderWidget;

use Mouf\MVC\BCE\BCEForm;
use Mouf\Database\TDBM\TDBMObject;

/**
 * Beans representing a file must implement this interface
 * if they are to be used by the `JqueryUploadMultiFileFieldDescriptor` or any compatible file uploader.
 * 
 */
interface FileBeanInterface {
	
	/**
	 * Returns the full path to the file.
	 */
	function getFullPath();
	
	/**
	 * Sets the name of the file to be stored.
	 * 
	 * @param string $fileName
	 */
	function setFileName($fileName);
	
	/**
	 * Sets the main bean we are pointing to.
	 * 
	 * @param TDBMObject $mainBean
	 */
	function setMainBean($mainBean);
}