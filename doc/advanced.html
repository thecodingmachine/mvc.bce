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
<h1>Detailed presentation and configuration of BCE</h1>
<p><i>We will start over from the last step of the <a href="quickstart.html">"quickstart" presentation</a>, and see how to get a nice userForm. But before you need to get a quick description of BCEForm's structure, and the main objects that it contains.</i></p>
<h2>Detailed description</h2>
<h3>Structure</h3>
<img src="images/structure.jpg" alt="structure" />
<h3>Main Classes</h3>
<ul>
<li>
<span class="entry">BCE Form</span>
The main object representing a form: it embedds two categories of objects/values: the field descriptors and the rest that is mainly the configuration part of the form. As you will see just after, FieldDescriptor are responsible for handling a field of the edited Bean (a User in this case). The configuration part defines how the form should behave (rendering, attributes as POST/GET method, action, id, etc...), and it also includes one special Field Descriptor: the idFieldDescriptor that handled the "identifier" field that has special rules.
</li>
<li><span class="entry"><u>The FieldDescriptors</u></span>
These instances describe how a field should be loaded, displayed, validated and persisted. Depending on the field's data, and relations, there may be different Types of Field Descriptors:
</li>
<li>
<ul>
<li><span class="entry">Base Field Descriptor</span>
This type is made for handdling simple fields of a table, that are written directly into the table without any relationship. In our example (as defined in the <a href="quickstart.html">quickstart</a>) this corresponds to user's name, email, birtdate, etc...<br/>
<code>Properties</code>
<ul>
<li><span class="entry">Field Name:</span> the name of the field as it will appear in the POST values. Therefore, it is a unique idenfier of the field inside the Form</li>
<li><span class="entry">Label:</span> the label of the field in the form</li>
<li><span class="entry">Renderer:</span> handles the display of the field</li>
<li><span class="entry">Formatter:</span> may be used in order to format the value retrived from DB before displaying it. Also, the formatter may implement an unformat method that will do the opposite (the formatter has to implement the BijectiveFormatterInterface)</li>
<li><span class="entry">Validators:</span> a list of instances that are responsible of validating the values in the Form. They always implement a server side validation method, and often a client side (JS) validation script (the validator has to implement the JSValidatorInterface)</li>
<li><span class="entry">Getter:</span> the function of the bean that will return the value for the field</li>
<li><span class="entry">Setter:</span> the function of the bean that will set the value into the field</li>
</ul>
</li>
<li><span class="entry">ForeignKey Field Descriptor</span>
As the name tells it, this type is dedicated for handling one-to-many relationships, as our user's role_id foreign key.<br/>
<code>Specific Properties (in addition to the Base Field Descriptor)</code>
<ul>
<li><span class="entry">Linked DAO:</span> the DAO that will be used to retrieve the linked beans (in our example, the Role DAO that will get the role beans to feed the Role SelectBox)</li>
<li><span class="entry">Linked Id Getter:</span> the function of the linked bean (ex/ RoleBean) that will return the id of the linked bean, that will be the value of the option</li>
<li><span class="entry">Linked Label Getter:</span> the function of the linked bean (ex/ RoleBean) that will return the label of the linked bean, that will be the text of the option</li>
<li><span class="entry">Data Method:</span> the function of the linked DAO that will return a list of beans</li>
</ul>
</li>
<li><span class="entry">ManyToMany Field Descriptor</span>
This third type of descriptor handles, of course, many-to-many relationships, as our user's hobbies<br/>
<code>Specific Properties (Many To Many descriptor do not have "getter" nor "setter" properties because no field is involved in the main table / bean)</code>
<ul>
<li><span class="entry">Mapping DAO</span> The DAO that handles the mapping table (the relation table, in our example, the UserHobby DAO)</li>
<li><span class="entry">Mapping Id Getter</span> The function that will get the primary key of the mapping table (ex: user_hobby.id)</li>
<li><span class="entry">Mapping Left Key Setter</span> The function that will set the left key (ex: user_hobby.user_id)</li>
<li><span class="entry">Mapping Right Key Setter</span> The function that will set the right key (ex: user_hobby.hobby_id)</li>
<li><span class="entry">Mapping Right Key Getter</span> The function that will get the right key</li>
<li><span class="entry">Bean Values Method</span> The function that will load the beans of the mapping table that correspond to the main bean (ex: all UserHobby beans where userId is the current edited UserBean)</li>
<li><span class="entry">Linked DAO, Linked Id Getter, Linked Label Getter, Data Method</span> see above, ForeingKey Field Descriptor's fields</li>
</ul>
</li>
<li><span class="entry">Custom Field Descriptor</span>
Because there is always some specific needs that need to be implemented, you may code any Field Descriptor you want by implementing the BCEFieldDescriptor Interface.
</li>
</ul>
</li>
<li><span class="entry"><u>The Configuration</u></span>
<ul>
<li><span class="entry">Id Field Decriptor</span>
This is a base descriptor that handles the identifier of the table. It is automatically detected if the PrimaryKey is defined for this table, and it is required for the Form to work properly
</li>
<li><span class="entry">HTML attributes</span>
The list of attributes to be found in the Form Tag (id, name, action, method, enctype, etc...)
</li>
<li><span class="entry">JS Validation Handler</span>
The JS library that will handle the client side validation : the validation functions are embedded in the validators, but the JS validation handler is responsible for aggregating the scripts, and diplaying the messages.
</li>
<li><span class="entry">Renderer</span>
Instance that will import the CSS files, and build the Form's DOM, by taking the Form as input.
</li>
</ul>
</li>
</ul>
<h2>Using the configuration interface</h2>
<h3>Global Overview</h3>
<p>Let's follow our example case: we just saved our form. The configuration interface is composed of 2 main tabs that allow the user to:
<ul>
<li>configure all existing or suggested field descriptors</li>
<li>configure the form itself</li>
</ul>
<img src="images/configuration details.jpg" alt="tab fields" />
<h4>The Field Descriptor widget</h4>
<img src="images/fied_descriptor_widget.jpg" alt="tab fields" />
<p>The widget is composed of a title bar that allows to sort, and expand/collapse the detailed view of the descriptor.</p>
<ol>
  <li>For confort, only one detail panel will be shown at once.</li>
  <li>The "Activate checkbox" will define if the fieldDescriptor should be added to the form. If the field descriptor is a suggested one (in green), then it will be created.</li>
  <li>The Title bar shows the name of the field descriptor instance.</li>
  <li>The "switch to PK" button is shown whenever no id field descriptor is set nor has been suggested (may occur for example if no PK is defined for the table, and if the form is being created). It will switch any base field descriptor into the configuration tab, as the id descriptor for the form. This action can be undone, when the button is clicked, it becomes an unset PK button.</li>
  <li>The switch to FK button will transform a base field descriptor widget into a ForeignKeyFieldDescriptor widget. This action can be undone as the swicth PK one</li>
</ol>
<p>When the widget is expanded, you can see the descriptors' details. The properties you can manipulate obviously depend on the class of the decriptor instance's (see Properties description of Field Descriptors)</p>
<h3>The Configuration Tab</h3>
<p>There is a second tab called "configuration", that allows you to define the specific Id FieldDescriptor, and some other form related attributes (renderer to be used, id, name, action, etc...)</p>
<img src="images/configure_page_2_1.jpg" alt="tab fields" />