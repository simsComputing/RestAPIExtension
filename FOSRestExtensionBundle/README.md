# RestAPIExtension

This bundle is made as an extension for the FOSRestBundle on Symfony 3.
It provides easy interfaces & configuration for simple resources researches and modifications.
The bundle is still in an early development phase and any help proposal shall be welcomed.

Please contact me at : simon.jm.humeau@gmail.com


The main functionnalities I am trying to develop are : 

1 - A Query Filter
This consists of a little processor that will automatically transform query parameters into repository parameters. We also provide a search function on repositories that will allow the user to match an exact pattern or an approximative pattern (we make use of the MATCH_AGAINST SQL function).

Example : You want to look for an article containing the key words "Symfony bundle rocks" in its "text" column. Then, with some configuration and a very thin controller the URL might look like this :

```
http://myapi.org?match_text=Symfony+bundle+rocks
```

then the controller : 

```php
class MyController extends APIController
{
  public function searchAction() 
  {
      $query_filter = $this->get("scfos_rest_extension.query_filters");
      $query_filter->setFilters(Articles::class);
      
      $match_filters = $query_filter->getMatchFilters();
      
      return $this->getDoctrine()
        ->getRepository("MyBundle:Articles")
        ->search(array(), $match_filters);
  }
}
```


2 - Patch Processor
When it comes to modifying resources on a RESTful API, it is not always easy to know the best way to process.
Based on the article "[Please don't Patch like an idiot](http://williamdurand.fr/2014/02/14/please-do-not-patch-like-an-idiot/)" by William Durand, we provide a PATCH processor that will parse PATCH requests and apply changes to the resource automatically. 

We will provide a full example of this feature in the full documentation.


3 - Authorization provider
Simple service that will handle authorization for each resource. (Based on the CRUD model, can this resource be Created, Read, Updated, Deleted ?


4 - Image creator
Alternative way to create images through RESTful API. This service is useful if the client is using a http module that does not allow him to send the file as would a browser form. 

+ Any other features I have not thought of ! 
