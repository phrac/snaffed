document.observe('dom:loaded', function() {
   new Ajax.Autocompleter('search', 'searchres', '/lib/autosearch.php', {
     method: 'get',
     minChars: 2

   });
 });