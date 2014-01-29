<style>
<!--
img {
    border: 0 none;
    display: block;
    margin: 0 auto;
    text-align: center;
	-moz-box-shadow: 0 0 5px 5px #888;
	-webkit-box-shadow: 0 0 5px 5px#888;
	box-shadow: 0 0 5px 5px #888;
}
ul li span.entry{
	font-weight: bold;
}
-->
</style>
<h1>Advanced configuration</h1>
<p><i>In this section, we will see:</i></p> 
<ul>
<li><i>how to fully adapt the field descriptors to their field's types</i></li>
<li><i>then add and configure the user - hobby (Many2Many) Field descriptor</i></li>
<li><i>finally you will get a simple example about coding a custom Field Descriptor</i></li>
</ul>
<h2>Finalize configuration of the user Form</h2>
<p><a href="quickstart.html">In the previous chapter</a>, we left the user form in a simple state:</p>
<ul>
<li>almost all fields are rendered as text fields,</li>
<li>numeric columns are validated as numeric fields,</li>
<li>role_id column is a dropdown list of roles</li>
</ul>
<p>This has been made by simply selecting a main DAO, and saving the default suggested configuration. Here is a list of configuration settings to give you an idea of what you can achieve...</p>
<h3>Add email validation to the "email" field</h3>
<img src="images/email_validation.jpg" alt="add_email"/>
<h3>Add URL validation to the "WebSite" field</h3>
<img src="images/url_validation.jpg" alt="add_url"/>
<h3>Make the "Newsletter" field a checkbox</h3>
<img src="images/chkb_renderer.jpg" alt="add_checkbox"/>
<h3>Advanced : create and use a new length validator</h3>
<p>Let's say we want the name field to be at least 5 chatacters and max 10 characters long. We are going to create a new instance of the MinMaxRangeLengthValidatorn and apply it.</p>
<img src="images/custom_range_length.jpg" alt="add_checkbox"/>
<h3>Result</h3>
<p>Now, refresh your "editUser" URL, and see you have a brand new Form!</p>
<img src="images/new_form.jpg" alt='final_form'/>
<h2>Set the user - hobby field descriptor</h2>
<p>As user - hobby is a Many 2 Many relationship, there is no field involved in the user table. Therefore, when you first created the form, no field descriptor was detected and therefore suggested to handle user's hobbies. You can add a Many2ManyField Descriptor by hiting the "+ m2m" button under the "Configuration tab".</p>
<p>Once you do this, a new field descriptor appears at the end of the list. For a better understanding of the Field Descriptors attributes, you should refer to the <a href="advanced.html">detailed description</a></p>
<p>After having filled out the descriptor (as you see, the configuration interface assists you in finding DAO's and Bean's methods), you may want to have your "Hobbies" field displayed before ImagePath and Newsletter fields. To do so, just drag the field up.</p>
<img src="images/m2m_desc.jpg" alt='m2m_desc'/>
<p>Now, look at your Form... nice, no?</p>
<img src="images/new_form2.jpg" alt='final_form2'/>
<h2>Create and use your own Field Decsriptor</h2>
<p>There is always a tiny specific functionnality you have to integrate. When using automated tools as form builders, CMS, ... these tiny things that count for 5% of the application features end up taking 20% of your time which is (I think) very frustrating. In the BCE System, we tried to make it as easy as possible to implement your own field descriptors.<br/>
<i><b>Note: </b>Before coding your custom descriptor, remember that as for every plugin of the Mouf framework, contribution is more than welcomed. If you are interested in  (and you have the time for) coding some Field Descriptors, Validators, Renderers, etc... please go ahead and contribute back!</i></p>
<p>The first thing to do is creating a class that implements the BCEFieldDescriptorInterace:</p>
<pre class="brush:php">
/**
 * Returns the name of the field as a unique identifier of that field
 */
public function getFieldName();

/**
 * Returns the name of the field as a unique identifier of that field
 */
public function getFieldLabel();

/**
 * Called when initializing the form (loading bean value into decsriptors, getting the validation rules, etc...)
 * @param mixed $bean : the main bean of the form
 * @param mixed $id : the idenfier of the form
 * @param BCEForm $form : the form itself
 */
public function load($bean, $id = null, &$form = null);

/**
 * Function called when rendering the whole Form
 */
public function toHtml();

/**
 * returns the specific JS for the field. 
 * 	- This JS may come from the renderer if any (eg datepicker or slider, multiselect, etc..)
 *  - Or also from the descriptor itself : eg a file upload callback function
 *  - In case of a custom field, this may also be some validation script
 */
public function getJS();

/**
 * Does all the operations before the main bean is saved. E.G:
 *   - unformat value
 *   - validate value
 *   - set the value on the bean
 *   ...
 *   
 * @param array $post The $_POST
 * @param BCEForm $form the form instance
 */
public function preSave($post, BCEForm &$form);

/**
 * Does some operations after the main bean has been saved.
 * Very important for M2M descriptors in order to perform their own persistance
 * @param mixed $bean the saved bean
 * @param mixed $beanId the id of the saved bean
 */
public function postSave($bean, $beanId);
</pre>
<p>Let's say we want a file upload fiel for our user's image path field.</p>
<pre class="brush:php">
class ImageUploadCustomDescriptor implements BCEFieldDescriptorInterface{
	
	
	private $fieldName = "cust_image";
	
	/**
	 * Returns the name of the field as a unique identifier of that field
	 */
	public function getFieldName(){
		return $this-&gt;fieldName;
	}
	
	/**
	 * Returns the label of the field
	 */
	public function getFieldLabel(){
		return "Image";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see BCEFieldDescriptorInterface::load()
	 */
	public function load($bean, $id = null, &$form = null){
		/* @var $bean UserBean */
		//modify the forms enctype in order to allow file uploads
		$form-&gt;setAttribute('enctype', 'multipart/form-data');
		$this-&gt;imagePath = $bean-&gt;getImagePath();
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see BCEFieldDescriptorInterface::toHtml()
	 */
	public function toHtml(){
		return "
			&lt;input type='file' value='' name='".$this-&gt;fieldName."' id='".$this-&gt;fieldName."'/&gt;
			&lt;label style='font-size: 11px;'&gt;Current File: " . ($this-&gt;imagePath ? "&lt;a target='_blank' href='". ROOT_URL ."$this-&gt;imagePath'&gt;$this-&gt;imagePath&lt;/a&gt;" : "none") . "&lt;/label&gt;
			&lt;label class='checkbox'&gt;
				&lt;input type='checkbox' id='".$this-&gt;fieldName."_remove' name='".$this-&gt;fieldName."_remove' value='1'&gt;
				Unlink Image
			&lt;/label&gt;";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see BCEFieldDescriptorInterface::getJS()
	 */
	public function getJS(){
		//NO JS needed
		return array();
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see BCEFieldDescriptorInterface::preSave()
	 */
	public function preSave($post, BCEForm &$form){
		/* @var $user UserBean */
		$user = $form-&gt;baseBean; 
		$tmpImageData = $_FILES[$this-&gt;fieldName];
		
		//New image has been uploaded
		if (!empty($tmpImageData['name'])){
			$filePath = $this-&gt;getDestinationFilePath($tmpImageData['name']);
			if (move_uploaded_file($tmpImageData['tmp_name'], ROOT_PATH . $filePath)){
				$user-&gt;setImagePath($filePath);
			}else{
				$form-&gt;addError($this-&gt;fieldName, "Could'nt move uploaded file");
			}
		}
		//Image must be cleared
		else{
			if (isset($post[$this-&gt;fieldName."_remove"])){
				unlink(ROOT_PATH . $user-&gt;getImagePath());
				$user-&gt;setImagePath(null);
			}
		}
	}
	
	/**
	 * Helper to get a filename that is unique: if a file with name $fileName alreday exists, then the value returned will be $fileNameX where X &gt;= 2 and $fileNameX does not exists
	 * @param string $fileName the original filename
	 * @return string the final file name
	 */
	public function getDestinationFilePath($fileName){
		$finalPath = "uploadedImages/" . $fileName;
		$i = 2;
		while (file_exists(ROOT_PATH . $finalPath)) {
			$finalPath = "uploadedImages/" . $fileName . $i;
			$i ++;
		}
		return $finalPath;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see BCEFieldDescriptorInterface::postSave()
	 */
	public function postSave($bean, $beanId){
		//No POST SAVE behavior
		return;
	}
	
}
</pre>
<p>Now, the next thing to do is : </p>
<img src="images/add_custom_uploader.jpg" />
<ol>
<li>Include the file in mouf (use include php files),</li>
<li>Create an instance of the ImageUploadCustomDescriptor class,</li>
<li>Add the instance to your form. Unfortunately, this is not possible in the configuration interface. You have to swicth back to the classic instance page of the form, and add the field descriptor there.</li>
<li>Then, you can go back to the configuration interface, and de-activate the default image path descriptor (of course, you could have done this in the default instance view).</li>
</ol>
<h3>Final result !</h3>
<img src="images/final_final_form.jpg" />