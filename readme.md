About BCE
==
BCE is a form builder for the Mouf framework. Its main goal is making your life easier. It will handle the main aspects of an HTML form:

* Build the form
* Perform client **and** server side validation
* Persist data into the database

When to use BCE ?
--
BCE should be used in order to create forms that reflect your business objects, ie BCE is very powerfull for generating the forms for your CRUDs.

Design choices
--
BCE has been design in order to be quick and easy to use, and also easely customaizable :

* an advanced form configuration interface that will automatically suggest form fields, and their attributes. In a few words, creating a basic form (including validation and persistance) may take you less than 5 minute!
* Because specific needs will always come up, you may code your custom field and use it in your form quite simply.

Most simple implemetation
--
Once you have configured your form, you have about 3 lines to code.
### Controller
```php
/**
 * Edit a user
 * @URL user/edit
 * @param int $id : the id of the user to edit (null for adding a new user)
 */
public function addUser($id = null) {
    $this->userFormInstance->load($id);//load the user into the form
    ...
}

/**
 * Save a user
 * @URL user/save
 */
public function saveUser() {
    $this->userFormInstance->save();//save the user
    ...
}
```

### View (display the form)
```php
$this->userFormInstance->toHtml();
```

### Result
![Renderer edit form](doc/images/edit-form.png)

> Ready to dive in? [Let's get started!](doc/quickstart.md)
