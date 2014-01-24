Quick Start Guide
==
In this quick start guide, we will see how you can use BCE to build a user form

Our playground data model
--
The first thing you have to know, is that BCE directy relies on your application's ORM system. For now, Mouf's ORM system is TDBM, but when others will be available, we will do our best to provide multi-ORM support. You will find a detailed description of BCE's architecture in the dedicated chapter [Detailed Architecture](detailed-architecture.html)

[Data Model](images/data-model.png)

**Requirement** : BCE needs a specific TDBM setting. You must turn the defaultAutoSaveMode to false in the "tdbmService" instance*

##Create and configure the form instance
First thing you have to do is creating a BCEForm instance. The BCEForm class represents a form. This is done, as usual, by hiting the "create a new instance" item of the ribbon menu. For example, we create a userForm instance :

[Create User Form](images/create-user.png)

Once you've done this, you reach usual instance interface of Mouf. As explained the the readme, a dedicated interface assists you in creating and configuring your form : press the "configure form" button just under the title of the instance page. You will reach the configuration screen.
Now, as explained above, BCE relies on the ORM system. You will first have to choose the DAO that handles the main entity the form should manage, in our case, we will choose the UserDAO :

[Choose Dao](images/choose-dao.png)

Once you choosed your DAO, the default form configuration will load automatically : each column of the ```user``` table will be mapped with a field descriptor (please read the  [Detailed Architecture](detailed-architecture.html) for more information on field descriptors).

In this exemple, we will just activate all suggested fields, and configure the form's action to submit to the ```saveUser``` URL. To do so, click Configuration >> Form Attributes, then modify the "Form Action URL" field

[Configure Form](images/configure-form.png)
[Set save URL](images/set-save-url.png)

_**Note :** If the data model changes, and you add a column, it will be automatically detected and suggested. If you remove a column, you will have to remove the field descriptor manually_

##Write a simple controller & view
We will assume, you already now about splash and its MVC implementation. If not, please refer to [Splash documentation](http://mouf-php.com/packages/mouf/mvc.splash/index.md) to understand what comes next.

Create a controller class (let's say the UserController), that has 2 actions: editUser and saveUser. Add a userFormInstance property of the BCEFormInstance class.

```php
    /**
     * @var BCEFormInstance
	 */
	public $formInstance;
	
	/**
	 * User edition page.
	 * @param string $id the user's id (null for creation)
	 * @URL editUser
	 */
	public function editUser($id = null) {
		$this->formInstance->load($id);
		
		$this->content->addFile(ROOT_PATH."src/views/user/edit-user.php", $this);
		$this->template->toHtml();
	}
	
	/**
	 * Save the user into DB.
	 * @URL saveUser
	 */
	public function saveUser() {
		$id = $this->formInstance->save();

		if ($id){
			set_user_message("User has been saved", UserMessageInterface::SUCCESS);
		}else{
			set_user_message("An error occured while saving the user", UserMessageInterface::ERROR);
		}
        
    	header("location:".ROOT_URL."editUser?id=".$id);
		return;
	}
```

create the view for diaplaying the form :
```php
<?php
use Test\Controllers\UserController;
/* @var $this UserController */
?>
<h1>Edit User</h1>
<?php 
$this->formInstance->toHtml();
```

Then, bind those instances together : the userController with the formInstance, and the userForm to the formInstance

[Bind userForm to form instance](images/bind-form-instance.png)

## ENJOY !!
Refresh the cache for registering the new URLs, and goto the /editUser page (or editUser?id=1 for user edition)

[Renderer edit form](images/edit-form.png)
