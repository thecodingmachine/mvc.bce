Renderers and formatters
==

In this page, you will find the list of all renderers / validators / formatters that come by default with Mouf.
Of course, you can also code your own, but before, let's check that what you are looking for does not already exists.

Renderers
---------

TODO: list existing renderers

Formatters
----------

BCE comes with a [list of formatters provided by the *mouf/utils.common.formatters* package](http://mouf-php.com/packages/mouf/utils.common.formatters/).
BCE also adds one useful formatter:

`HtmlPurifierFormatter`: this formatter is in charge of purifying HTML elements to avoid XSS attacks in your code.
You would typically use this formatter along a `RichTextFieldRenderer` to sanitize the HTML before storing HTML in database. 
The `HtmlPurifierFormatter` is based on the [HTML Purifier](http://htmlpurifier.org) library.
