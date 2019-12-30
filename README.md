#AngelCake CMS
The first CakePHP Based Hybrid CMS

## Why different?
AngelCake CMS is meant for two kind of users:
* **the basic user**, for him/her it should be easy just to insert contents, images, attachment. The user interface should be as easy as possible.
* **the programmer** should have some competencies in PHP programming. 

**What is missing? The site administrator and the graphic designer**
Our experience says that a lot of small customers do not have a **site administrator** (ie: somebody who takes care of all the configuration of the site, but not necessarily the contents).

When they need a structural modification to the web site (I don't refer here to adding a content, but changing a menu, inserting a new block, etc...), they just **call us**. 

And what do we do? We fight against the "almost-easy to use" web tools in order to make them do what we need.

In particular we come from Drupal. 

Content Types and Views are great, but when you need something a little more complex (like grouping the results and presenting the data in a specific format), you should be a wizard of Drupal hacks, remember which module is needed for grouping, which will conflict with the JSON export and do not cope well with the XLS export facility. 

It all seems easy, but it takes a lot of time. Actually **more time** than we would spend simply writing a couple of lines of code for the feature we need.

So AngelCake CMS is not intended to be used by graphic designer or site administrators with little programming skills. Wordpress is there for you. We sacrifice some user-friendlines for the administrator in order to keep thing easy, light and fast.

## So, you reinvent the wheel? No!
- AppleCake CMS is Based on CakePHP 3.x, it can be easily managed via composer, in order to keep the libraries up to date.
- AppleCake CMS does **not** have a rich configuration interface. All the configuration is done by nerds (programmers), which work on the template/views files, using simple, plaing HTML + CSS + PHP. This seems to be a little more complex than the point-and-click interface of Drupal or Wordpress, but at the end of the day is much faster (if you are a programmer ;-).
- As many extra features as possible are implemented via external, well established libraries, in order not to re-invent the wheel

## Hybrid Static CMS
Another special characteristic of AppleCake CMS is the possibility to behave as a static CMS.
Same philosophy as  [Jekill](https://jekyllrb.com/) or [PicoCMS](http://picocms.org/): simply put some MarkDown files (.md) in a conventional folder structure, and they will be rendered as a proper site.

Variables defined in the FrontMatter section of the will be rendered in the CakePHP template. So:
	variable: value 
is equivalent to:
	$this->set('variable', value);

This way the users can choose if they prefer a graphical user interface (like any CMS), or simply work with files and folder offline.

### Coming Soon
- The users can choose to render static pages as static HTML files (this is not suitable for every sites, for example not for a e-commerce where every page should change depending on the product, the user, etc), but is great for some static sites, where users simply want to publish contents.
- The users can store their files in a external storage (like owncloud, google drive or dropbox ), and this will be synced automatically with the CMS and generate the pages


## File and image management
Files and images can be attached to any page in a very simple way.
AppleCake CMS simply creatse a folder associated with the id of the content and they will appear.
So the attachments of the node (page/article) 33 will be in folder
/webroot/sitename/nodes/33/images/
/webroot/sitename/nodes/33/attachments/

Thumbnails are created on the fly by CakePHP and stored locally in the same folder (super easy to manage, backup, recreate cache, etc)

### Coming Soon
- Possibility to reorder the attachments (today you have to rename them in order to have them in the requested order)
- Possibility to save meta informations (like title, alt, display name, etc) for the attachments.

## Drupal import
Since we have migrated several drupal sites, we have prepared a CakePHP console script which converts Drupal to MD Files, with attachments.

## MultiSite
AppleCake can handle multiple site configurations.
Simply create a 
/config/sites/mydomain.com/settings.php

and the configurations contained in this file will override the default ones

## Themes as Plugins
Themes are standard CakePHP 3 plugins

## Event Management
AppleCake CMS comes with a simple subscription form, which allows the users to subscribe to events, export the results in form of table or XLS

### Coming Soon
- Event Management should become a plugin

