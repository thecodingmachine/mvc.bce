Uploading multiple files
========================

Sometimes, you want to a form to accept multiple files at one time.
For instance, you want to upload multiple pictures of a product, or multiple documents associated to a particular contract...

BCE has a special *field descriptor* that allows you to do this.

First of all, the basics:

- Your main bean will contain many files
- Therefore, there is a 1..* relationship between your files and your main bean
- In BCE, each file is stored on the disk, and a reference to that file is stored in database
- So you need a table that contains the list of files, with a foreign key pointing to the main bean

Let's assume we are making a database containing products and that each product has several photos. The data model 
would look like this:

![Typical database schema](db_schema.png)

As you can see, the "files" table contains a foreign key pointing to the "products" table, and a column containing the name of the
file to be stored.

Getting started
---------------

Multiple file upload in a bean is done via a special field descriptor in BCE:  `FileMultiUploaderFieldDescriptor`.
Therefore, to add a file upload field to your form, you need to add a `FileMultiUploaderFieldDescriptor` to your
`BCEForm`.

Here are the important fields to fill:

- **label**: This is the label of the field
- **description**: A small description displayed below the field
- **fileDao**: An instance of the DAO pointing to the tables containing the files. Your table does not need to be called "files" by the way.
- **filePathMethod**: The name of the method of the fileDao that returns the list of the file beans.
  This method signature must be:
  ```
  function filePathMethod($beanId)
  ```

  
TODO: continue this