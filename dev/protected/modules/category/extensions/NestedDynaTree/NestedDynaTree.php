<?php

/**
 * Nested DynaTree Widget. A wrapper for dynaTree jQuery plugin
 *
 * inspired  Murat Kutluer <muratkutluer@yahoo.com>
 * @copyright Copyright &copy; dynamicLine gmbH. 2012
 * @link      http://www.yiiframework.com/extension/nestedtree/
 * @author    Szincsák András <andras@szincsak.hu>
 * @access    public
 * @version   0.1
 */
class NestedDynaTree extends CWidget {

	/**
	 * The classname of the model. Can not be null.
	 *
	 * @var string The classname of the model
	 */
	public $modelClass = null;


	/**
	 * Enable manipulation as insert, delete and dnd
	 *
	 * @var boolean wether manipulation is enabled
	 */
	public $manipulationEnabled = true;

	/**
	 * Drag&Drop
	 *
	 * @var boolean wether drag&drop is enabled
	 */
	public $dndEnabled = true;

	/**
	 * Container id. There will be loaded the clickAction
	 *
	 * @var string the id of the container
	 */
	public $clickAjaxLoadContainer = "";

	/**
	 * path to Ajax controller
	 * You could set CortollerMap in config/main.php like this:
	 * 'controllerMap'=>array(
	 *   'AXtree'=>'ext.NestedDynaTree.AXcontroller'
	 * ),
	 * @var string the path. It must be ended slash (/)
	 */
	public $ajaxController = "/AXtree/";

	/**
	 * url to href value ( href value extended by model pk)
	 *
	 * @var string $clickAction must be ended slash (update/) or = (update/?id=)
	 */
	public $clickAction = "";

	/**
	 * url to Ajax Manipulation (AXcontroller) to load data to tree.
	 *
	 * @var string name of load action default "load"
	 */
	public $loadAxAction = "loadTree";

	/**
	 * url to Ajax Manipulation (AXcontroller) to insert events
	 *
	 * @var string name of insert action default "insert"
	 */
	public $insAxAction = "create";

	/**
	 * url to Ajax Manipulation (AXcontroller) to delete item
	 *
	 * @var string name of delete action default "delete"
	 */
	public $delAxAction = "delete";

	/**
	 * url to Ajax Manipulation (AXcontroller) to handle drag and drop events
	 *
	 * @var string name of move action default "move"
	 */
	public $dndAxAction = "move";

	/**
	 * html options for container div tag
	 *
	 * @var mixed
	 */
	public $htmlOptions = array();

	/**
	 * clas of the container of log message
	 *
	 * @var mixed
	 */
	public $logClass = '';

	public $skin = 'default';

	/**
	 * options for dynatree initialization
	 *
	 * @var mixed
	 */
	public $options = array();

	/**
	 * default options
	 *
	 * @var mixed
	 */
	protected $defaultOptions = array(
		'debugLevel'      => 0,
		'checkbox'        => false,
		'selectMode'      => 2,
		'clickFolderMode' => 1,
		//'persist'         => true,
		'activeVisible'   => true,
		'minExpandLevel'  => 2,
	);

	/**
	 * initialization
	 *  Check model against NestedTreeActiveRecord
	 *  Load model and Tree data
	 *  Set widget id
	 */
	public function init () {
		/* check model */
		if ( $this->modelClass == null ) {
			throw new CDbException(Yii::t('tree', 'You must have implement model .'));
		}
		$model = new $this->modelClass;
		if ( count($model::model()->findAll()) == 0 )
		{
			if ( !($model->roots()) ) {
				throw new CDbException(Yii::t('tree', 'Model `{model}` have to be an instance of NestedTreeActiveRecord, or have NestedSet behavior.', array('{model}' => $this->modelClass)));
			}
		}


		/* get widget id */
		if ( isset($this->htmlOptions['id']) ) {
			$this->id = $this->htmlOptions['id'];
		}
		else {
			$this->htmlOptions['id'] = $this->getId();
		}

		/* prepare csrf token */
		$csrfToken = Yii::app()->request->getCsrfToken();

		/* set option: initAjax - use AJax Action */
		$this->options["initAjax"] = array(
			'url'  => Yii::app()->createUrl($this->ajaxController . '/' . $this->loadAxAction),
			'type' => 'post',
			'data' => array(
				'model'                            => $this->modelClass,
				'clickAction'                      => $this->clickAction,
				Yii::app()->request->csrfTokenName => $csrfToken
			)
		);

		/* set option: onActivate -  ajax load event tree on activate  */
		if ( $this->clickAjaxLoadContainer ) {
			$this->options["onActivate"] = 'js:function(node) {if( node.data.href&& node.data.href!="#"){ $("#' . $this->clickAjaxLoadContainer . '").load(node.data.href)};return false;}';
		}
		else {
			$this->options["onActivate"] = 'js:function(node) {if( node.data.href&& node.data.href!="#"){window.open(node.data.href, node.data.target);}return false;}';
		}

		/* set option: Drag&Drop */
		if ( $this->dndEnabled && $this->manipulationEnabled ) {
			$this->options["dnd"] = array(
				'preventVoidMoves' => true,
				'onDragStart'      => 'js: function(node) {return true;}',
				'onDragEnter'      => 'js: function(node, sourceNode) {return true;}',
				'onDrop'           => 'js: function(node, sourceNode, hitMode, ui, draggable) {

                $.post("' . Yii::app()->createUrl($this->ajaxController . '/' . $this->dndAxAction) . '",{model:"' . $this->modelClass . '", "source":sourceNode.data.key ,"target":node.data.key, "mode":hitMode,"' . Yii::app()->request->csrfTokenName . '":"' . $csrfToken . '"},
                    function(data){
                    if(data.status==\'' . Ajax::AJAX_SUCCESS . '\'){
                        var tree=$("#' . $this->id . '").dynatree("getTree");
                        var sourceNode=tree.getNodeByKey(data.data.sourceNode);
                        var node=tree.getNodeByKey(data.data.node);
                        var parent=sourceNode.getParent();
                        sourceNode.move(node, data.data.mode);

                        if(parent){
                            parent.data.isFolder=(parent.hasChildren());
                            parent.render(false,false);    
                        }
                        sourceNode.data.isFolder=(sourceNode.hasChildren());
                        node.data.isFolder=(node.hasChildren());
                        node.render();
                        sourceNode.render();
                        sourceNode.activate();
                        sourceNode.focus();
                    }else{
                   alert(data.message);
                   }
                    },"json");
            }',
			);
		}
		;

		/* Draw tree box   */
		echo CHtml::openTag('div', array('class' => 'treeWidget'));
		echo CHtml::tag('div', $this->htmlOptions, true, true);
		echo CHtml::closeTag('div');

		/* encode data and options */
		$options = CJavaScript::encode(array_merge($this->defaultOptions, $this->options));

		/* publish files */
		$path = Yii::app()->assetManager->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets',
			false,
			-1,
			YII_DEBUG);

		/* register script and css files, then initialize with data and options */
		Yii::app()->getClientScript()->registerCoreScript('jquery')->registerCoreScript('jquery.ui')->registerScriptFile($path . '/jquery.dynatree.js')->registerCssFile($path . '/skin' . ( $this->skin == 'vista' ? '-vista' : '') . '/ui.dynatree.css')->registerScript(__CLASS__ . $this->id,
			'$("#' . $this->id . '").dynatree(' . $options . '); ');

		$deleteUrl = Yii::app()->createUrl($this->ajaxController . '/' . $this->delAxAction);
		$succesStatus = Ajax::AJAX_SUCCESS;

		if ( $this->manipulationEnabled ) {

		$editButtonsScript = <<<JS
		jQuery('body').on('click','.treeButtonNew', function(event) {
			event.preventDefault();
			$("#addCategory").dialog("open");
			jQuery.ajax({
				url: $(this).attr('href'),
				dataType:'json',
				type: 'POST',
				success: function(data) {
					$('#addCategory').html(data.data.form);
				}
			})
		});

		jQuery('body').on('click','.treeButtonDelete', function(event) {
			event.preventDefault();
    		var node = $("#{$this->id}").dynatree("getActiveNode");
      		if( node && confirm('Are you sure?') ) {
				jQuery.ajax({
					url: '{$deleteUrl}',
					dataType:'json',
					type: 'POST',
					data: {source: node.data.key},
					success: function(data) {
						if( data.status == '{$succesStatus}' ) {
                 			var tree=$("#{$this->id}").dynatree("getTree");
                    		tree.reload(function(){
                    			tree.activateKey(data.key);
                    		});
                    	}
					},
					error: function(data) {
						//alert(data.message);
					}
				});
        	}
      });
JS;


		Yii::app()->getClientScript()->registerScript('editButtonsScript', $editButtonsScript);

		}

		/*if ( $this->manipulationEnabled ) {
			echo '<br /><ul class="submit-row">';
			echo '<li class="left submit-button-container"><a class="submit-link treeButtonNew" href="' . Yii::app()->createUrl('category/categoryBackend/create') . '">' . Yii::t('tree', 'New') . '</a></li>';
			echo '<li class="left submit-button-container"><a class="delete-link treeButtonDelete" href="' . Yii::app()->createUrl('category/categoryBackend/delete') . '">' . Yii::t('tree', 'Delete') . '</a></li>';
			echo '</ul>';
		}*/
	}

	/**
	 * run widget
	 *
	 */
	public function run () {

	}

}

?>
