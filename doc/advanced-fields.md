Advances Fields
==

In the previous chapter (configuring bce forms) we have set up our form to a more convienient display. BCE basically auomatically parses the data model in order to generate base fields (user name, email, etc...) and foreign key fields (user's role) which are, in fact the columns of the user `table`.

But as defined in the quickstart, the user &lt;-&gt; skills & user &lt;- projects relationships are not implemented. In this sections, we will explain how to use the configuration interface in ordre to get a form that handles our data model.

Many to many relationships (M2M)
--

This would apply to the user &lt;-&gt; skills relationship, stored in the user_skill table. Here is how you can quickly configure this type of information using the configuration interface :

![Configure many to many](images/configure_many_to_many.png)

First click on the "add m2m" button, the select the new added field, and set the properties...

The base settings of the Field Descriptor are the same - you can refer to the "Field Descriptor Overview" section of the [configure bce form chapter](configure-bce-forms.md). Specific paramaters involve the dao that stores the M2M relation (mapping dao - ie userSkillDao), and the dao that handles the relatied data (linked dao - ie skillDao). For a better understanding, below is a schema that indicates you how to define the specific properties of a M2M field descriptor (just replace the roleDao by the skillDao):

![Many2Many how to](images/m2m.png)


> **Note :** BCE actually does not allow, handling additionnal data of a many-to-many table. For example, if you want to add a `skill_aquisition_date` column in the `user_skill` table, BCE will not be able to fill it using it's built in field descriptors (anyway, you still can implement your own field descriptor (see [custom field chapter](custom-field.md).

Handeling many-to-one relationships
--

This part will explain how to handle the user-projects relationship, but it's quite different than the previous one. As you saw, a M2M fiedDescriptor will produce a checkbox system (other renderings are available, as multi-select, or a set of single selectlists), which means the user forms does not handle skills (nore adding nore creating a skill), which is quite normal because skills are not related to a particular user.

In the user &lt;- project relationship, this is different : given a project is owned by one user only, that we may want to add or remove a project for a particular user, we will handle projects as an item list in the user form (so called "subforms").

