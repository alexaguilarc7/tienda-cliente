<?php

/**
  * Catalog tab for admin panel, AdminCatalog.php
  * Tab has been separated in 3 files : this one, AdminCategories.php and AdminProducts.php
  * @category admin
  *
  * @author PrestaShop <support@prestashop.com>
  * @copyright PrestaShop
  * @license http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
  * @version 1.3
  *
  */

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');
include(PS_ADMIN_DIR.'/tabs/AdminCategories.php');
include(PS_ADMIN_DIR.'/tabs/AdminProducts.php');

class AdminCatalog extends AdminTab
{
	/** @var object AdminCategories() instance */
	private $adminCategories;

	/** @var object AdminProducts() instance */
	private $adminProducts;

	/** @var object AttributeGenerator() instance */
	private $attributeGenerator;

	/** @var object AttributeGenerator() instance */
	private $imageResize;

	/** @var object Category() instance for navigation*/
	private static $_category = NULL;

	public function __construct()
	{
		/* Get current category */
		$id_category = abs(intval(Tools::getValue('id_category')));
		if (!$id_category) $id_category = 1;
		self::$_category = new Category($id_category);
		if (!Validate::isLoadedObject(self::$_category))
			die('Category cannot be loaded');

		$this->table = array('category', 'product');
		$this->adminCategories = new AdminCategories();
		$this->adminProducts = new AdminProducts();
		//var_dump("");
		parent::__construct();
	}

	/**
	 * Return current category
	 *
	 * @return object
	 */
	public static function getCurrentCategory()
	{
		return self::$_category;
	}

	public function viewAccess($disable = false)
	{
		$result = parent::viewAccess($disable);
		$this->adminCategories->tabAccess = $this->tabAccess;
		$this->adminProducts->tabAccess = $this->tabAccess;
		return $result;
	}

	public function postProcess()
	{
		if (!Tools::getValue('id_product'))
			$this->adminCategories->postProcess();
		elseif (isset($_GET['attributegenerator']))
		{
			if (!isset($this->attributeGenerator))
			{
				include_once(PS_ADMIN_DIR.'/tabs/AdminAttributeGenerator.php');
				$this->attributeGenerator = new AdminAttributeGenerator();
			}
			$this->attributeGenerator->postProcess();
		}
		elseif (isset($_GET['imageresize']))
		{
			if (!isset($this->imageResize))
			{
				include_once(PS_ADMIN_DIR.'/tabs/AdminImageResize.php');
				$this->imageResize = new AdminImageResize();
			}
			$this->imageResize->postProcess();
		}
		$this->adminProducts->postProcess($this->token);
	}

	public function displayErrors()
	{
		$this->adminProducts->displayErrors();
		$this->adminCategories->displayErrors();
	}

	public function display()
	{
		global $currentIndex;
        
        if(isset($_GET['verifyproducts']) AND $_GET['verifyproducts'] == 1){
            
            echo "<h2>".$this->l("Cantidad Categorias y productos.")."</h2>";
            
            $this->getCategoriesAndProducts();
        }elseif(isset($_GET["createnewslatter"]) && $_GET["createnewslatter"] == 1)
        {
            echo "<h2>".$this->l("Crear newsletter")."</h2>";
            $this->createNewslatter();
        }
		elseif (((Tools::isSubmit('submitAddcategory') OR Tools::isSubmit('submitAddcategoryAndStay')) AND sizeof($this->adminCategories->_errors)) OR isset($_GET['updatecategory']) OR isset($_GET['addcategory']))
		{
			$this->adminCategories->displayForm($this->token);
			//echo '<br /><a href="'.$currentIndex.'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" /> '.$this->l('Back to list').'</a><br />';
            echo '<br /><a class="action_back_list" href="'.$currentIndex.'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" /> '.$this->l('Back to catalog').'</a><br />';
		}
		elseif (((Tools::isSubmit('submitAddproduct') OR Tools::isSubmit('submitAddproductAndStay')) AND sizeof($this->adminProducts->_errors)) OR Tools::isSubmit('updateproduct') OR Tools::isSubmit('addproduct'))
		{
			$this->adminProducts->displayForm($this->token);
			echo '<br /><a class="action_back_list" href="index.php?tab='.Tools::getValue('tab').'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" />'.$this->l('Back to catalog').'</a><br />';
		}
		elseif (isset($_GET['attributegenerator']))
		{
			if (!isset($this->attributeGenerator))
			{
				include_once(PS_ADMIN_DIR.'/tabs/AdminAttributeGenerator.php');
				$this->attributeGenerator = new AdminAttributeGenerator();
			}
			$this->attributeGenerator->displayForm();
		}
		elseif (isset($_GET['imageresize']))
		{
			if (!isset($this->imageResize))
			{
				include_once(PS_ADMIN_DIR.'/tabs/AdminImageResize.php');
				$this->imageResize = new AdminImageResize();
			}
			$this->imageResize->displayForm();
		}
		elseif (!isset($_GET['editImage']))
		{
			$id_category = intval(Tools::getValue('id_category'));
			if (!$id_category)
				$id_category = 1;
			$catalog_tabs = array('category', 'product');
			// Cleaning links
			$catBarIndex = $currentIndex;
			foreach ($catalog_tabs AS $tab)
				if (Tools::getValue($tab.'Orderby') && Tools::getValue($tab.'Orderway')) 
					$catBarIndex = preg_replace('/&'.$tab.'Orderby=([a-z _]*)&'.$tab.'Orderway=([a-z]*)/i', '', $currentIndex);
            
            $url_grafico = $currentIndex."&token=".$this->token."&verifyproducts=1";
            echo '<h2 class="sub_title_tab right-h2">'.$this->l('Categories').'</h2>';  
			echo '
                <div class="cat_bar"><span style="color: #3C8534;">
                    '.$this->l('Current category').' :</span>&nbsp;&nbsp;&nbsp;'.getPath($catBarIndex, $id_category).' 
                    <a href="'.$url_grafico.'" style="float:right;margin: 0 20px 0 0;">'.$this->l("Grafico").'</a>
                </div>';

			
			
			//echo '<h3>'.$this->l('Current category').'&nbsp;&nbsp;'.getPath($catBarIndex, $id_category).'</h3>';
			$this->adminCategories->display($this->token);
			echo '<div style="margin:10px">&nbsp;</div>';			
			echo '<h2>'.$this->l('Products in this category').'</h2>';

			$this->adminProducts->display($this->token);
		}
	}
    
    public function getCategoriesAndProducts()
    {
        global $cookie, $currentIndex;
        
        $categories = Category::getCategories(intval($cookie->id_lang_2), false);
        
        echo '
        <table cellspacing="0" cellpadding="0" class="table_categories">';
		$done = array();
		$index = array();
		$indexedCategories =  array();
		foreach ($indexedCategories AS $k => $row)
			$index[] = $row['id_category'];
		$this->recurseCategoryForInclude($index, $categories, $categories[0][1], 1, 1);
		echo '			</table>
        
        ';
    }
    
    function recurseCategoryForInclude($indexedCategories, $categories, $current, $id_category = 1, $id_category_default = NULL)
	{
		global $done;
		static $irow;
		$id_obj = intval(Tools::getValue($this->identifier));

		if (!isset($done[$current['infos']['id_parent']]))
			$done[$current['infos']['id_parent']] = 0;
		$done[$current['infos']['id_parent']] += 1;

		$todo = sizeof($categories[$current['infos']['id_parent']]);
		$doneC = $done[$current['infos']['id_parent']];

		$level = $current['infos']['level_depth'] + 1;
		$img = $level == 1 ? 'lv1.gif' : 'lv'.$level.'_'.($todo == $doneC ? 'f' : 'b').'.gif';

		echo '
		<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
            ';
        $producs = Product::getCategoryProducts($id_category);
        $cant = count($producs);
        echo '
			<td>
				<img src="../img/admin/'.$img.'" alt="" /> &nbsp;<label for="categoryBox_'.$id_category.'" class="t">'.stripslashes(Category::hideCategoryPosition($current['infos']['name'])).' <span>('.$cant.')</span></label>
			</td>
		</tr>';

		if (isset($categories[$id_category]))
			foreach ($categories[$id_category] AS $key => $row)
				if ($key != 'infos')
					$this->recurseCategoryForInclude($indexedCategories, $categories, $categories[$id_category][$key], $key);
	}
    
    public function createnewslatter()
    {
        global $cookie, $currentIndex;
        
        $textmessage = $this->l('Agregando Producto......');
        
        echo '
            <script type="text/javascript">
                jQuery(function(){
                    jQuery("#form_add_prod_newslatter").submit(function(){
                        var ele = jQuery(this);
                        var datos = ele.serialize();
                        var inputText = jQuery("#name_reference").val();
                        if(inputText != "")
                        {
                            loadMessage("350px", \''.$textmessage.'\', "280px");
                            jQuery.ajax({
                                type: "post",
                                url: "newslatterhtml.php",
                                data: datos,
                                success: function(res){
                                    var obj = $.parseJSON(res);
                                    var lihtml = obj.add_li;
                                    $("#name_reference").val("");
                                    setTimeout($.unblockUI, 200);
                                    if(obj.message == "error"){
                                        alert("No Hay producto con esa referencia");    
                                    }else{
                                        jQuery("#html_newslater_generado #product_list_newslatter").prepend(lihtml);
                                        
                                        $("#product_list_newslatter li").hover(
                                            function () {
                                                var delete2 = jQuery(this).find(".delete_product_newslatter");
                                                delete2.css("display","block");
                                                }, 
                                            function () {
                                                var delete2 = jQuery(this).find(".delete_product_newslatter");
                                                delete2.css("display","none");
                                            }
                                        );
                                        
                                        jQuery(".delete_product_newslatter").click(function(){
                                            var elePadre = jQuery(this).parent();
                                            elePadre.remove();
                                            return false;
                                        });    
                                    }
                                }     
                            });    
                        }
                        return false;
                    });
                });        
            </script>
        ';
        
        $id_eso = Language::getIsoById($cookie->id_lang);
        $dir_img_default = _PS_BASE_URL_._PS_IMG_.'p/'.$id_eso.'-default-home.jpg';
        $dir_files = _PS_FILES_NEWS_.'newsletter_c813e.txt';
        
        $style = $this->newsletterStyle();
        echo $style;
        
        echo '
            
            <div id="create_newslatter_html">
                <div id="message_success_store_remote" class="message_result" style="display: none; width: 400px;">
                    <img src="../img/admin/ok.gif" alt="">'.$this->l("La direccion remota se guardo con exito.").'
                </div>
                <form id="form_add_prod_newslatter" class="width2" onsubmit="return false;">
                    <p id="error_address_remote" style="color:red; display:none;">'.$this->l("No es valido la direccion remota.").'</p>
    				<div class="">
                        <label style="text-align: left;">'.$this->l('Add Refereces Products').'</label>
                    <br><br>
    					<input type="text" size="45" name="name_reference" id="name_reference"  value="" />
                        <input type="hidden" name="add_products_references" id="add_products_references" value="1" />
                        <input type="hidden" name="id_lang" id="id_lang" value="'.$cookie->id_lang.'"/>
                        <input type="hidden" name="on_sale_lang" id="on_sale_lang" value="'.$this->l('On sale!').'"/>
                        <input type="hidden" name="price_lowered_lang" id="price_lowered_lang" value="'.$this->l('Price lowered!').'"/>
                        <input type="hidden" name="available_lang" id="available_lang" value="'.$this->l('Available').'"/>
                        <input type="hidden" name="out_of_lang" id="out_of_lang" value="'.$this->l('Out of stock').'"/>
                        <input type="hidden" name="add_to_cart_lang" id="add_to_cart_lang" value="'.$this->l('Add to cart').'"/>
                        <input type="hidden" name="view_lang" id="view_lang" value="'.$this->l('View').'"/>
                        <input type="submit" value="'.$this->l('   add   ').'" name="" class="button" />
    				</div>        
                </form>
                <div style="padding: 15px 0;">
                    <a id="btn_save_newslatter" class="button" href="#">Guardar Boletin</a>
                </div>
                <div class="message_result">
                    <img alt="" src="../img/admin/ok.gif"/>'.$this->l('El archivo se a creado correctamente.').'<br/>
                    <a href="newslatter/export_accessory_05fcd164.xls">'.$this->l('Download file').' newslatter.html</a>
                    <input type="image" src="newslatter/newsletter_b4bfd.html" alt="Submit">
                    <a href="newslatter/newsletter_c813e.txt">'.$this->l('Download file').' newslatter.html</a>
                </div><a href="'.$dir_files.'">'.$this->l('Download file').' newslatter.html</a>
                <div id="ligth-box-newslatter">
                    <div id="html_newslater_generado">
                        <ul id="product_list_newslatter" class="clear">
                            
                        </ul>
                    </div>
                </div>
                <!--<textarea id="text_html_newslatter" cols="90" rows="20"></textarea> -->               
            </div>

            <script type="text/javascript">
                jQuery(function(){
                    jQuery("#btn_save_newslatter").click(function(){
                        var textHTML = jQuery("#ligth-box-newslatter").html();
                        var contenUL = jQuery("#product_list_newslatter");
                        jQuery("#ligth-box-newslatter").val(textHTML);
                        if(contenUL.find("li").length > 0){
                            jQuery.post("newslatterhtml.php", {"newnewslatter": 1, "texthtml": textHTML}, function(){
                                
                            });    
                        }else{
                            alert("Debe agregar producto para guardar.");
                        }
                        return false;  
                    });
                });
            </script>
        ';
    }
    
    public function newsletterStyle()
    {
        global $cookie;

        $dirBtnEx = _PS_BASE_URL_._THEME_IMG_DIR_.'button-medium_exclusive.gif';
        $dirBtnVer = _PS_BASE_URL_._THEME_IMG_DIR_.'button-medium.gif';
        $dirBgLi = _PS_BASE_URL_._THEME_IMG_DIR_.'block_category_item_bg.jpg';
        
        $style = '
            <style type="text/css">
                .clear{
                    clear: both;
                }
                .clearfix:after {
                    clear: both;
                    content: ".";
                    display: block;
                    font-size: 0;
                    height: 0;
                    visibility: hidden;
                }
                #html_newslater_generado{
                    margin: 0 0 30px;
                    width: 556px;
                }
                #product_list_newslatter{
                    list-style-type: none;
                    margin-top: 2em;
                }
                #product_list_newslatter li{
                    background: url("'.$dirBgLi.'") repeat-x scroll 0 0 #E5E7EB;
                    border: 1px solid #D0D3D8;
                    clear: both;
                    height: 130px;
                    margin-bottom: 0.3em;
                    padding: 6px;
                    position: relative;
                }
                #product_list_newslatter li .delete_product_newslatter{
                    position: absolute;
                    top: 5px;
                    right: 15px;
                    display: none;
                }
                #product_list_newslatter li a{
                    text-decoration: none;   
                }
                #product_list_newslatter li .center_block{
                    float: left;
                    width: 74%;
                }
                #product_list_newslatter li .center_block .product_img_link{
                    border: 1px solid #D0D3D8;
                    float: left;
                    margin-right: 0.6em;   
                }
                #product_list_newslatter li .center_block .product_img_link img{
                    vertical-align: bottom;
                    width: 129px;
                }
                #product_list_newslatter li .center_block h3{
                    color: #800010;
                    text-decoration: none;
                    margin: 0.4em 0;
                    
                }
                #product_list_newslatter li .center_block .product_desc{
                    font-size: 0.92em;
                    margin: 0.5em 0;
                    padding-left: 0.7em;
                }
                #product_list_newslatter li div.right_block{
                    float: left;
                    margin-left: 1em;
                    margin-top: 1.5em;
                    text-align: right;
                    width: 10em;                    
                }
                #product_list_newslatter li .right_block .on_sale{
                    color: #DA0F00;
                    display: block;
                    font-weight: bold;
                }
                #product_list_newslatter li .right_block .price{
                    color: #800030;
                    display: block;
                    font-size: 170%;
                    margin-bottom: 0.2em;
                    font-weight: bold;
                    white-space: nowrap;
                }
                #product_list_newslatter li .right_block .availability{
                    color: #800040;
                    font-size: 0.9em;
                }
                #product_list_newslatter li .right_block a{
                    margin-top: 0.5em;
                    width: 120px;
                }
                #product_list_newslatter li .right_block a{
                    background-position: left top;
                    background-repeat: no-repeat;
                    border: medium none;
                    cursor: pointer;
                    display: block;
                    font-size: 10px !important;
                    font-weight: bold;
                    height: 18px;
                    line-height: 18px;
                    text-align: center;
                    text-decoration: none !important;
                }
                #product_list_newslatter li .right_block a.exclusive{
                    color: white;
                    background-image: url("'.$dirBtnEx.'");
                }
                #product_list_newslatter li .right_block a.ver{
                    color: black;
                    background-image: url("'.$dirBtnVer.'");
                }
            
            </style>
        ';
        
        return $style;
    }
}

?>