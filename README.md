# wordpress-breadcrumbs

**How to use?**
Include breadcrumbs.php in your project:

In this example I added Breadcrumb.php inside inc/utilities/ and required it in functions.php
```
require get_parent_theme_file_path( '/inc/utilities/Breadcrumb.php' );
```

**Using Class Breadcrumbs**
In header.php
```
$breadcrumbs = new Breadcrumb;
$breadcrumbs->init(array(
  'home_title' 	=> 'Homepage',
  'separator'	 	=> '>',
  'search_prefix' => 'Results'
))->use();
```
