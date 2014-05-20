<?php
namespace Mouf\MVC\BCE;

use Mouf\Database\DAOInterface;

use Mouf\Html\Widgets\FileUploaderWidget\SimpleFileUploaderWidget;

use Mouf\MVC\BCE\BCEForm;
use Mouf\Database\TDBM\TDBMObject;

/**
 * DAO representing a table containing a list of files must implement this interface
 * if they are to be used by the `JqueryUploadMultiFileFieldDescriptor` or any compatible file uploader.
 * 
 */
interface FileDaoInterface extends DAOInterface {
	
	/**
	 * Returns a list of beans implementing the FileBeanInterface associated with the main bean containing the files.
	 * 
	 * @param TDBMObject $mainBean
	 */
	function findFiles($mainBean);
}