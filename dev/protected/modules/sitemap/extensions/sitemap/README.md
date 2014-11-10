Sitemap solution for Yii framework
==================================

Description
-----------
Provides ability to build hierarchy of application modules, their controllers and actions belonging to controllers.
Each hierarchy's node: whether it's a module, controller or action - is represented as abstraction - instance or subclass of AppStructureComponent.

Installation
------------
1.  Fetch files to directory named "sitemap" which is at your extensions dir. At this example I'll use following paths:
    * Application extensions path: ```application.protected.extensions```
    * Files of this extension are at: ```application.protected.extensions.sitemap```
2.  Add this alias to your application configuration:  

    ```php
    'aliases' => array(
      'yiiExtensions' => 'application.protected.extensions',
    ),
    ```
3.  Register application component:

    ```php
    'components' => array(
      'sitemap'            => array(
        'class' => 'yiiExtensions\sitemap\SitemapComponent',
      ),
    ),
    ```

Usage
-----
Just copy&paste this code to one of your views to see what this component can do:
```php
// It is a list of AppStructureComponent
$applicationStructure = Yii::app()->sitemap->getApplicationStructure();

// Now lets watch what we got - it is always better to see it once:
function displayStructure($structure)
{
   foreach ($structure as $structureNode)
   {
      echo '<ul>';
         echo "<li>Name: {$structureNode->getName()}</li>";
         echo "<li>Description: {$structureNode->getDescription()}</li>";
         echo "<li>Route: {$structureNode->getRoute()}</li>";
         echo '<li>Class: '.get_class($structureNode->getInstance()).'</li>';
         echo '<li>Children: ';
         displayStructure($structureNode->getChildren());
         echo '</li>';
      echo '</ul>';
   }
}
displayStructure($applicationStructure);
```

You may want to write a widget for displaying that data in a pretty way. 

It is not cool. What about use-cases?
-------------------------------------
*  Suppose that you have ```PagesController```, that displays static pages stored at DB. You may want to display the list of those pages at your sitemap section, so you can write something like this:
   
   At your controller:
   ```php
   class PagesController extends CController
   {
      public function getPagesForSitemap()
      {
         return new CActiveDataProvider(Page::model());
      }
   }
   ```
   
   Now, let's modify sitemap rendering:
   ```php
   
   foreach ($structure as $structureNode)
   {
      echo '<ul>';
         echo "<li>Name: {$structureNode->getName()}</li>";
         echo "<li>Description: {$structureNode->getDescription()}</li>";
         echo "<li>Route: {$structureNode->getRoute()}</li>";
         echo '<li>Class: '.get_class($structureNode->getInstance()).'</li>';
         if ($structureNode->getInstance() instanceof PagesController)
         {
            echo '<li>Pages: ';
            $this->widget('zii.widgets.grid.CGridView', array('dataProvider'=>$structureNode->getInstance()->getPagesForSitemap()));
            echo '</li>';
         } else {
            echo '<li>Children: ';
            displayStructure($structureNode->getChildren());
            echo '</li>';
         }
      echo '</ul>';
   }
   ```
*  Also, you can create an interface for your modules or controllers and change behavior when current instance 
   of application component implements it at rendering or at initialization of AppStructureComponent instance.
   
   If the former, you already know how to do it. If the latter, you need to extend AppStructureComponent class.
   A tiny example of this:
   
   ```php
   interface IHaveInfoAboutActions
   {
      /**
       * @return array 'actionID'=>['name'=>..., 'description'=>...] 
       */
      public function getActionsInfo();
   }
   
   class MyController extends CController implements IHaveInfoAboutActions
   {
      public function actionIndex()
      {
         // some stuff here
      }
      
      public function getActionsInfo()
      {
         return array('index'=>array('name'=>'My cool action', 'description'=>'I can do some stuff'));
      }
   }
   
   class MyAppStructureComponent extends \yiiExtensions\sitemap\components\AppStructureComponent
   {
   
       /**
        * @param CAction $instance
        */
       public function initFromAction($instance)
       {
         parent::initFromAction($instance);
         if($instance->getController() instanceof IHaveInfoAboutActions)
         {
            $info = $instance->getController()->getActionsInfo();
            if (isset($info[$instance->getId()]))
            {
               $this->name = $info[$instance->getId()]['name'];
               $this->description = $info[$instance->getId()]['description'];
            }
         }
       }
   }
   
   // when fetching structure
   Yii::app()->sitemap->structureComponentClass = 'MyAppStructureComponent';
   $applicationStructure = Yii::app()->sitemap->getApplicationStructure();
   ```

*  If you need to check whether component is suitable for addition to output structure,
   you should override AppStructureComponent::validate() method. 
   
   This example disables all actions, which are under the influence of the "ajaxOnly" filter:
   
   ```php
   class MyAppStructureComponent extends \yiiExtensions\sitemap\components\AppStructureComponent
   {
       public function validate()
       {
           if ($this->getInstance() instanceof CAction) {
               $controller  = $this->getInstance()->getController();
               $filters     = $controller->filters();
               $filterChain = CFilterChain::create($controller, $this->getInstance(), $filters);
               foreach ($filterChain as $filter) {
                   if ($filter instanceof CInlineFilter && $filter->name == 'ajaxOnly') {
                       return false;
                   }
               }
           }
   
           return true;
       }
   }
   ```
