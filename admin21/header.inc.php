<?php

/**
  * Admin panel header, header.inc.php
  * @category admin
  *
  * @author PrestaShop <support@prestashop.com>
  * @copyright PrestaShop
  * @license http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
  * @version 1.3
  *
  */

// P3P Policies (http://www.w3.org/TR/2002/REC-P3P-20020416/#compact_policies)
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0'); // HTTP/1.1
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

require_once(dirname(__FILE__).'/init.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $iso; ?>" lang="<?php echo $iso; ?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link type="text/css" rel="stylesheet" href="../js/jquery/datepicker/datepicker.css" />
		<link type="text/css" rel="stylesheet" href="../modules/gridextjs/extjs/resources/css/ext-all.css" />
		<link type="text/css" rel="stylesheet" href="../css/admin.css" />
		<title>PrestaShop&trade; - <?php echo translate('Administration panel') ?></title>
		<script type="text/javascript">
			var search_texts = Array('<?php echo translate('product, customer, id...'); ?>','<?php echo translate('product, category...'); ?>', '<?php echo translate('customer id, name, e-mail...'); ?>', '<?php echo translate('order id'); ?>', '<?php echo translate('invoice id'); ?>', '<?php echo translate('cart id'); ?>');
			var helpboxes = <?php echo Configuration::get('PS_HELPBOX'); ?>;
		</script>
        
		<script type="text/javascript" src="<?php echo _PS_JS_DIR_ ?>jquery/jquery-1.2.6.pack.js"></script>
		<script type="text/javascript" src="<?php echo _PS_JS_DIR_ ?>jquery/jquery.aautoscroll.2.41.js"></script>
		<script type="text/javascript" src="../js/admin.js"></script>
		<script type="text/javascript" src="../js/toggle.js"></script>
		<script type="text/javascript" src="../js/tools.js"></script>
		<script type="text/javascript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/AjaxUpload.2.0.min.js"></script>
        <script type="text/javascript" src="<?php echo _PS_JS_DIR_ ?>jquery/jquery-1.7.1.js"></script>
        <script type="text/javascript" src="<?php echo _PS_JS_DIR_ ?>jquery.blockUI.js"></script>
        <script type="text/javascript">
        
            var msgAccessory = "<?php echo translate('Delete Accessory #'); ?>";
            var msgAttachment = "<?php echo translate('Delete Attachment #'); ?>";
            var lang_text = "<?php echo translate('Ivoices and Delivery splis') ?>";
            
        </script>
		<link rel="shortcut icon" href="../img/favicon.ico" />
        <?php 
            $auxTab = explode("=",$currentIndex);
            $auxTab = $auxTab[1];
            $lang_text = translate('Orders');
            //$aux_lang_link = '<li><a href="index.php?tab=AdminLanguages&token=1b628b51f2c9103a6fcab4ea1f4c43ee"><img style="margin-right:5px" src="../img/t/would.gig"></a><a href="index.php?tab=AdminLanguages&token=1b628b51f2c9103a6fcab4ea1f4c43ee">'.translate('Orders').'</a></li>'
        ?>
        <script type="text/javascript">
            jQuery(function(){
                setTimeout(function(){
                    var id_lang = "<?php echo $cookie->id_lang ?>";
                    $.post("ajax.php", {id_lang: id_lang, form_login_lang: "form_login_lang"}, function(){});
                }, 2000);
                
                var adminTab = "<?php echo $auxTab ?>";
                var link = "<?php echo $aux_lang_link ?>";
                
                switch(adminTab)
                {
                    case "AdminContacts":
                        jQuery("#content").find("#AdminContacts").remove();
                    break;
                    case "AdminManufacturers":
                        jQuery(".link_add_address, .title_key_access, .address_form").css("display", "none");
                    break;
                }
                
                $("a.action_edit, a.action_view, a.action_add_new, a.action_back_list, .action_duplicate").click(function(){
                    $.blockUI(
                    { 
                        message: '<div style="margin:0 auto;overflow: hidden;padding: 10px 0;width: 250px;"><div style="float:left;margin:0 10px 0 0;"><img src="../img/admin/loading2.gif" /></div><h1 style="float:left;margin:5px 0 0 0;">Espere por favor...</h1></div>',
                        css: {width: '300px'} 
                    });
                });
                
                $(".links_main li a.options_shop, .action_btn_reset, .action_btn_filter").click(function(){
                    $.blockUI(
                    { 
                        message: '<div style="margin:0 auto;overflow: hidden;padding: 10px 0;width: 250px;"><div style="float:left;margin:0 10px 0 0;"><img src="../img/admin/loading2.gif" /></div><h1 style="float:left;margin:5px 0 0 0;">Espere por favor...</h1></div>',
                        css: {width: '300px'} 
                    });
                });
                
                $(".action_delete, .action_btn_del").click(function(){
                    
                    var ele = jQuery(this);
                    var message_del = ele.attr("message");
                    var request_del = confirm(message_del);
                    if(request_del){
                        $.blockUI(
                        { 
                            message: '<div style="margin:0 auto;overflow: hidden;padding: 10px 0;width: 250px;"><div style="float:left;margin:0 10px 0 0;"><img src="../img/admin/loading2.gif" /></div><h1 style="float:left;margin:5px 0 0 0;">Espere por favor...</h1></div>',
                            css: {width: '300px'} 
                        });
                        return true;
                    }else{
                        return false;    
                    }
                });
                
                $(".action_submit_form").click(function(){
                    var ele = jQuery(this);
                    
                    if(ele.attr("id") == "send_restore"){
                        var fileSelect = ele.parent().find("#file").val();
                        if(fileSelect !="")
                            loadPage();
                    }else{
                        loadPage();    
                    }
                });
                
                
            });
            
            function loadPage()
            {
                $.blockUI(
                { 
                    message: '<div style="margin:0 auto;overflow: hidden;padding: 10px 0;width: 250px;"><div style="float:left;margin:0 10px 0 0;"><img src="../img/admin/loading2.gif" /></div><h1 style="float:left;margin:5px 0 0 0;">Espere por favor...</h1></div>',
                    css: {width: '300px'} 
                });
            }
            
        </script>
		<?php echo Module::hookExec('backOfficeHeader'); ?>
		<!--[if IE]>
		<style type="text/css">
		fieldset {
			position: relative;
			padding-top: 25px;
		}
		legend {
			position: absolute;
			top: -0.5em;
			left: 1.1em;
		}
		</style>
		<![endif]--> 
		
		
		
	</head>
	<body>
        <!--<div id="div_hide_dir"></div>-->
        <div>
        
        </div>
        <div style="display: none;">
            <div id="messageGlobal" style="margin:0 auto;overflow: hidden;padding: 10px 0;width: 250px;">
                <div style="float:left;margin:0 10px 0 0;">
                    <img src="../img/admin/loading2.gif" />
                </div>
                <h1 id="textMessageGlobal" style="float:left;margin:5px 0 0 0;"><?php echo translate("Espere por favor...") ?></h1>
            </div>
        </div>
		<div id="container">
			<div style="float: left; margin-top: 11px">
				<form action="index.php?tab=AdminSearch&token=<?php echo Tools::getAdminToken('AdminSearch'.intval(Tab::getIdFromClassName('AdminSearch')).intval($cookie->id_employee)) ?>" method="post">
					<input type="text" name="bo_query" id="bo_query" style="width: 140px;" value="<?php echo (isset($_POST['bo_query']) ? Tools::safeOutput(Tools::stripslashes($_POST['bo_query'])) : ''); ?>" /> <?php translate('in') ?>
					<select name="bo_search_type" id="bo_search_type" onchange="queryType();" style="font-size: 1em;">
						<option value="0"><?php echo translate('everywhere') ?></option>
						<option value="1"<?php echo (isset($_POST['bo_search_type']) AND
						($_POST['bo_search_type'] == 1)) ? ' selected="selected"' : '' ?>><?php echo translate('catalog') ?></option>
						<option value="2"<?php echo (isset($_POST['bo_search_type']) AND
						($_POST['bo_search_type'] == 2)) ? ' selected="selected"' : '' ?>><?php echo translate('customers') ?></option>
						<option value="3"<?php echo (isset($_POST['bo_search_type']) AND (
						$_POST['bo_search_type'] == 3)) ? ' selected="selected"' : '' ?>><?php echo translate('orders') ?></option>
						<option value="4"<?php echo (isset($_POST['bo_search_type']) AND (
						$_POST['bo_search_type'] == 4)) ? ' selected="selected"' : '' ?>><?php echo translate('invoices') ?></option>
						<option value="5"<?php echo (isset($_POST['bo_search_type']) AND (
						$_POST['bo_search_type'] == 5)) ? ' selected="selected"' : '' ?>><?php echo translate('carts') ?></option>
					</select>&nbsp;
					<input type="submit" name="bo_search" value="<?php echo translate('Search') ?>" class="button" />
					<script type="text/javascript">queryType();</script>
				</form>
			</div>
			<div style="float: left;margin: 13px 0 0 10px;" id="flagsLanguage">
				<div class="pos-tag-left">
				<?php
                
				$link = new Link();
				$languages = Language::getLanguages();
				$i = 0;
				if (sizeof($languages) != 1)
					foreach ($languages AS $language)
					{
                        $idLang = Language::getIdByIso("en");
                        $name_lang = Language::getLanguage($idLang);
                        if($language["iso_code"] == "es"){
                            echo '<a href="'.$link->getLanguageLinkAdmin($language['id_lang'], $language['name']).'&adminlang=1"><img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="'.strtoupper($language['iso_code']).'" title="'.$language['name'].'" '.($language['id_lang'] == $cookie->id_lang ? 'class="selected_language"' : '').' "/></a> ';
                        }elseif($language["iso_code"] == "en"){
                            echo '<a href="'.$link->getLanguageLinkAdmin($language['id_lang'], $language['name']).'&adminlang=1"><img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="'.strtoupper($language['iso_code']).'" title="'.$language['name'].'" '.($language['id_lang'] == $cookie->id_lang ? 'class="selected_language"' : '').' "/></a> ';
                        }elseif($language["iso_code"] == "fr"){
                            echo '<a href="'.$link->getLanguageLinkAdmin($idLang, $name_lang['name']).'&adminlang=1"><img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="'.strtoupper($language['iso_code']).'" title="'.$language['name'].'" '.($language['id_lang'] == $cookie->id_lang ? 'class="selected_language"' : '').' "/></a> ';
                        }elseif($language["iso_code"] == "de"){
                            echo '<a href="'.$link->getLanguageLinkAdmin($idLang, $name_lang['name']).'&adminlang=1"><img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="'.strtoupper($language['iso_code']).'" title="'.$language['name'].'" '.($language['id_lang'] == $cookie->id_lang ? 'class="selected_language"' : '').' "/></a> ';
                        }elseif($language["iso_code"] == "pt"){
                            echo '<a href="'.$link->getLanguageLinkAdmin($idLang, $name_lang['name']).'&adminlang=1"><img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="'.strtoupper($language['iso_code']).'" title="'.$language['name'].'" '.($language['id_lang'] == $cookie->id_lang ? 'class="selected_language"' : '').' "/></a> ';
                        }
                        /*if($language["iso_code"] == "es"){
                            echo '<a style="cursor: pointer;" iso_url="'.$link->getLanguageLinkAdmin($language['id_lang'], $language['name']).'&adminlang=1" id_lang="'.$language['id_lang'].'"><img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="'.strtoupper($language['iso_code']).'" title="'.$language['name'].'" '.($language['id_lang'] == $cookie->id_lang_2 ? 'class="selected_language"' : '').' /></a> ';
                        }elseif($language["iso_code"] == "en"){
                            echo '<a style="cursor: pointer;" iso_url="'.$link->getLanguageLinkAdmin($language['id_lang'], $language['name']).'&adminlang=1" id_lang="'.$language['id_lang'].'"><img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="'.strtoupper($language['iso_code']).'" title="'.$language['name'].'" '.($language['id_lang'] == $cookie->id_lang_2 ? 'class="selected_language"' : '').'/></a> ';
                        }elseif($language["iso_code"] == "fr"){
                            echo '<a style="cursor: pointer;" iso_url="'.$link->getLanguageLinkAdmin($idLang, $name_lang['name']).'&adminlang=1" id_lang="'.$idLang.'"><img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="'.strtoupper($language['iso_code']).'" title="'.$language['name'].'" '.($name_lang == $cookie->id_lang_2 ? 'class="selected_language"' : '').' /></a> ';
                        }elseif($language["iso_code"] == "de"){
                            echo '<a style="cursor: pointer;" iso_url="'.$link->getLanguageLinkAdmin($idLang, $name_lang['name']).'&adminlang=1" id_lang="'.$idLang.'"><img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="'.strtoupper($language['iso_code']).'" title="'.$language['name'].'" '.($name_lang == $cookie->id_lang_2 ? 'class="selected_language"' : '').' /></a> ';
                        }elseif($language["iso_code"] == "pt"){
                            echo '<a style="cursor: pointer;" iso_url="'.$link->getLanguageLinkAdmin($idLang, $name_lang['name']).'&adminlang=1" id_lang="'.$idLang.'"><img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="'.strtoupper($language['iso_code']).'" title="'.$language['name'].'" '.($name_lang == $cookie->id_lang_2 ? 'class="selected_language"' : '').' /></a> ';
                        }*/
						if ($i == 5)
							echo '</div><div class="pos-tag-left">';
						$i++;
					}
				?>&nbsp;<input type="button" onclick="javascript:window.open('https://www.team-web.es/_0_CONTENIDO/_Formulario/Formu-Soporte-Tienda.php','Soporte','width=800,height=600,left=100,top=100,menubar=no,scrollbars=no,location=no,resizable=no,directories=no')" value="<?php echo translate('Support') ?>"/>
				<input type="button" onclick="javascript:window.open('https://www.team-web.es/manual/manual.php','Manual','')" value="<?php echo translate('Manual') ?>"/>&nbsp;<input type="button" onclick="javascript:window.open('https://www.team-web.es/1/index.html','Manual','')" value="<?php echo translate('Guide') ?>"/>
				</div>
			</div>
			<script type="text/javascript">$('#flagsLanguage img[class!=selected_language]').css('opacity', '0.3')</script>
            <script type="text/javascript">
                /*jQuery(function(){
                    jQuery('#flagsLanguage a').click(function(){
                        var elem = jQuery(this);
                        //var url_iso = elem.attr("iso_url");
                        $.post("ajax.php", {id_lang: elem.attr("id_lang"), change_lang_back_office: "change_lang_back_office"}, function(){
                                setTimeout(function(){
                                    window.location = elem.attr("iso_url");
                                }, 500);
                            });
                    });
                });*/
                
                jQuery(function()
                {
                    
                    var urlActual = location.href;
                    var array_url = urlActual.split("=");
                    var array_url_2 = array_url[1].split("&");
                    var tabActual = array_url_2[0];
                    
                    jQuery(".sub_menu_shop li#"+tabActual).addClass("active_link");
                    //jQuery(".sub_menu_shop").css("width", jQuery(".sub_menu_shop").width());     
                    jQuery(".sub_title_tab").css("width", jQuery(".form table tr td table.table").width());                         
                    
                });
                
            </script>
			<div style="float: right; margin: 11px 0px 0px 10px; text-align:right;">
				<img src="../img/admin/quick.gif" style="margin-top:5px;" />&nbsp;
				<script type="text/javascript">
				function quickSelect(elt)
				{
					var eltVal = $(elt).val();
					if (eltVal == '0') return false;
					else if (eltVal.substr(eltVal.length - 6) == '_blank')
						window.open(eltVal.substr(0, eltVal.length - 6), '_blank');
					else location.href = eltVal;
				}
				</script>
				<select onchange="quickSelect(this);" style="font-size: 1em; margin:5px 5px 0px 0px;">
					<?php
						$quicks = QuickAccess::getQuickAccesses(intval($cookie->id_lang)); 
						echo '<option value="0">'.translate('Quick access').'</option>';
						foreach ($quicks AS &$quick)
						{
							preg_match('/tab=(.+)(&.+)?$/', $quick['link'], $adminTab);
							if (isset($adminTab[1]))
							{
								if (strpos($adminTab[1], '&'))
									$adminTab[1] = substr($adminTab[1], 0, strpos($adminTab[1], '&'));
								$quick['link'] .= '&token='.Tools::getAdminToken($adminTab[1].intval(Tab::getIdFromClassName($adminTab[1])).intval($cookie->id_employee));
							}
							echo '<option value="'.$quick['link'].($quick['new_window'] ? '_blank' : '').'">&gt; '.Category::hideCategoryPosition($quick['name']).'</option>';
						}
					?>
				</select>
				<img src="../img/admin/nav-user.gif" alt="<?php echo translate('user') ?>" />&nbsp;
				<a href="index.php?logout" title="<?php echo translate('logout') ?>">
					<b><?php echo Tools::substr($cookie->firstname, 0, 1).'.&nbsp;'.htmlentities($cookie->lastname, ENT_COMPAT, 'UTF-8'); ?></b>
					<img src="../img/admin/nav-logout.gif" alt="<?php echo translate('logout') ?>" />
				</a>
			</div>
			<div style="clear:both;"></div>
			<div style="margin: 5px 0 5px 0px;" id="laguageViewsForm">
				<div>
				<?php
				$link = new Link();
				$languages = Language::getLanguages();
                
                //$lang_form = intval(Configuration::get("PS_VIEW_FORMULARIO_LANG"));
                //$lang_form = intval(Configuration::get("PS_LANG_DEFAULT"));
                $lang_form = intval($cookie->id_lang_2);
				$i = 0;
				if (sizeof($languages) != 1)
					foreach ($languages AS $language)
					{
						//echo '<a href="'.$link->getLanguageLinkAdmin($language['id_lang'], $language['name']).'&adminlang=1"><img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="'.strtoupper($language['iso_code']).'" title="'.$language['name'].'" '.($language['id_lang'] == $cookie->id_lang ? 'class="selected_language"' : '').' /></a> ';
                        echo '<a id="lang_'.$language['id_lang'].'"  class="lang_translatable" style="cursor: pointer;" onclick="changeLanguajeFormulario('.$language['id_lang'].')"><img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="'.strtoupper($language['iso_code']).'" title="'.$language['name'].'" '.($language['id_lang'] == $lang_form ? 'class="selected_language"' : '').' /> </a>';
						if ($i == 4)
							echo '</div><div style="margin-top:5px;">';
						$i++;
					}
				?>
				</div>
				<script type="text/javascript">$('#laguageViewsForm img[class!=selected_language]').css('opacity', '0.3')</script>
			</div>
			
			<?php echo Module::hookExec('backOfficeTop'); ?>
			<!-- <ul id="menu" style="margin-top:20px">
				<?php
					global $cookie;

					/* Get current tab informations */
					/*
					$id_parent_tab_current = intval(Tab::getCurrentParentId());
					$tabs = Tab::getTabs(intval($cookie->id_lang), 0);
					foreach ($tabs AS $t)
					{
						if ($t['class_name'] == $tab)
							$id_parent = $t['id_tab'];
						if (checkTabRights($t['id_tab']) === true)
						{
							$img = '../img/t/'.$t['class_name'].'.gif';
							if (trim($t['module']) != '')
								$img = _MODULE_DIR_.$t['module'].'/'.$t['class_name'].'.gif';
							echo '
							<li'.((($t['class_name'] == $tab) OR ($id_parent_tab_current == $t['id_tab'])) ? ' class="active"' : '').'>
								<a href="index.php?tab='.$t['class_name'].'&token='.Tools::getAdminToken($t['class_name'].intval($t['id_tab']).intval($cookie->id_employee)).'"><img src="'.$img.'" alt="" style="width:16px;height:16px" /> '.$t['name'].'</a>
							</li>';
						}
					}
					*/
				?>
			</ul>
			-->
			<div id="main">
				<ul id="submenu" style="display: none">
				<?php
					global $cookie;
					$id_parent_tab_current = intval(Tab::getCurrentParentId());
					/* Display tabs belonging to opened tab */
					$id_parent = isset($id_parent) ? $id_parent : $id_parent_tab_current;
					
					if(isset($_GET["tab"]) && $_GET["tab"]=="AdminShipping"){
						$id_parent = 5;
                        echo '<li>
                            <a href="index.php?tab=AdminShipping&token='.Tools::getAdminToken("AdminShipping".intval(Tab::getIdFromClassName('AdminShipping')).intval($cookie->id_employee)).'"><img style="margin-right:5px" src="../img/t/AdminShipping.gif"></a>
                            <a href="index.php?tab=AdminShipping&token='.Tools::getAdminToken("AdminShipping".intval(Tab::getIdFromClassName('AdminShipping')).intval($cookie->id_employee)).'">'.translate('Shipping').'</a>
                        </li>';
					}elseif((isset($_GET["tab"]) && $_GET["tab"]=="AdminRangePrice") || (isset($_GET["tab"]) && $_GET["tab"]=="AdminRangeWeight") || (isset($_GET["tab"]) && $_GET["tab"]=="AdminCarriers")){
					   echo '<li>
                            <a href="index.php?tab=AdminShipping&token='.Tools::getAdminToken("AdminShipping".intval(Tab::getIdFromClassName('AdminShipping')).intval($cookie->id_employee)).'"><img style="margin-right:5px" src="../img/t/AdminShipping.gif"></a>
                            <a href="index.php?tab=AdminShipping&token='.Tools::getAdminToken("AdminShipping".intval(Tab::getIdFromClassName('AdminShipping')).intval($cookie->id_employee)).'">'.translate('Shipping').'</a>
                        </li>';
					}
					
					if(isset($_GET["tab"]) && $_GET["tab"]=="AdminImportv2"){
						$id_parent = -1;
					}
					
					if(isset($_GET["tab"]) && $_GET["tab"]=="AdminManufacturers"){
						$id_parent = -1;
					}
					if(isset($_GET["tab"]) && $_GET["tab"]=="AdminSuppliers"){
						$id_parent = -1;
					}
					if(isset($_GET["tab"]) && $_GET["tab"]=="AdminImport"){
						$id_parent = -1;
					}
					if(isset($_GET["tab"]) && $_GET["tab"]=="AdminImagenesAdjuntos"){
						$id_parent = -1;
					}
					/*if(isset($_GET["tab"]) && $_GET["tab"]=="AdminMessages"){
						$id_parent = -1;
					}*/
					if(isset($_GET["tab"]) && $_GET["tab"]=="AdminBackup"){
						$id_parent = -1;
					}
                    if(isset($_GET["tab"]) && $_GET["tab"]=="AdminRestore"){
						$id_parent = -1;
					}
					if(isset($_GET["tab"]) && $_GET["tab"]=="AdminContact"){
						$id_parent = -1;
					}
					if(isset($_GET["tab"]) && $_GET["tab"]=="AdminContacts"){
						$id_parent = -1;
					}
					if(isset($_GET["tab"]) && $_GET["tab"]=="AdminAppearance"){
						$id_parent = -1;
					}
					
					if(isset($_GET["tab"]) && $_GET["tab"]=="AdminCMS"){
						$id_parent = -1;
					}
                    if(isset($_GET["tab"]) && $_GET["tab"]=="adminImageAttachments"){
						$id_parent = -1;
					}
					
					/*if($_GET["tab"]=="AdminCarriers"){
						$id_parent = -1;
					}
					*/
					if(isset($_GET["tab"]) && $_GET["tab"]=="AdminCustomers"){
						$id_parent = 2;
                        echo '<li>
                            <a href="index.php?tab=AdminCustomers&token='.Tools::getAdminToken("AdminCustomers".intval(Tab::getIdFromClassName('AdminCustomers')).intval($cookie->id_employee)).'"><img style="margin-right:5px" src="../img/t/AdminCustomers.gif"></a>
                            <a href="index.php?tab=AdminCustomers&token='.Tools::getAdminToken("AdminCustomers".intval(Tab::getIdFromClassName('AdminCustomers')).intval($cookie->id_employee)).'">'.translate('Customers').'</a>
                        </li>';
					}elseif((isset($_GET["tab"]) && $_GET["tab"]=="AdminAddresses") || (isset($_GET["tab"]) && $_GET["tab"]=="AdminGroups")){
					   echo '<li>
                            <a href="index.php?tab=AdminCustomers&token='.Tools::getAdminToken("AdminCustomers".intval(Tab::getIdFromClassName('AdminCustomers')).intval($cookie->id_employee)).'"><img style="margin-right:5px" src="../img/t/AdminCustomers.gif"></a>
                            <a href="index.php?tab=AdminCustomers&token='.Tools::getAdminToken("AdminCustomers".intval(Tab::getIdFromClassName('AdminCustomers')).intval($cookie->id_employee)).'">'.translate('Customers').'</a>
                        </li>';
					}
                    if(isset($_GET["tab"]) && $_GET["tab"]=="adminExportDatas"){
						$id_parent = -1;
					}
                    if(isset($_GET["tab"]) && $_GET["tab"] == "AdminOrders"){
						$id_parent = 3;
                        echo '<li><a href="index.php?tab=AdminOrders&token='.Tools::getAdminToken("AdminOrders".intval(Tab::getIdFromClassName('AdminOrders')).intval($cookie->id_employee)).'"><img style="margin-right:5px" src="../img/t/AdminOrders.gif"></a>
                            <a href="index.php?tab=AdminOrders&token='.Tools::getAdminToken("AdminOrders".intval(Tab::getIdFromClassName('AdminOrders')).intval($cookie->id_employee)).'">'.translate('Orders').'</a></li>'; 
					}elseif((isset($_GET["tab"]) && $_GET["tab"]=="AdminInvoices") || (isset($_GET["tab"]) && $_GET["tab"]=="AdminDeliverySlip") || (isset($_GET["tab"]) && $_GET["tab"]=="AdminOrderMessage") || (isset($_GET["tab"]) && $_GET["tab"]=="AdminMessages")){
					   echo '<li><a href="index.php?tab=AdminOrders&token='.Tools::getAdminToken("AdminOrders".intval(Tab::getIdFromClassName('AdminOrders')).intval($cookie->id_employee)).'"><img style="margin-right:5px" src="../img/t/AdminOrders.gif"></a>
                            <a href="index.php?tab=AdminOrders&token='.Tools::getAdminToken("AdminOrders".intval(Tab::getIdFromClassName('AdminOrders')).intval($cookie->id_employee)).'">'.translate('Orders').'</a></li>';
					}
                    if(isset($_GET["tab"]) && $_GET["tab"]=="AdminPayment"){
						$id_parent = 4;
                         echo '<li><a href="index.php?tab=AdminPayment&token='.Tools::getAdminToken("AdminPayment".intval(Tab::getIdFromClassName('AdminPayment')).intval($cookie->id_employee)).'"><img style="margin-right:5px" src="../img/t/AdminPayment.gif"></a>
                            <a href="index.php?tab=AdminPayment&token='.Tools::getAdminToken("AdminPayment".intval(Tab::getIdFromClassName('AdminPayment')).intval($cookie->id_employee)).'">'.translate('Payment').'</a>
                        </li>'; 
					}elseif((isset($_GET["tab"]) && $_GET["tab"]=="AdminCurrencies") || (isset($_GET["tab"]) && $_GET["tab"]=="AdminTaxes")){
                         echo '<li><a href="index.php?tab=AdminPayment&token='.Tools::getAdminToken("AdminPayment".intval(Tab::getIdFromClassName('AdminPayment')).intval($cookie->id_employee)).'"><img style="margin-right:5px" src="../img/t/AdminPayment.gif"></a>
                            <a href="index.php?tab=AdminPayment&token='.Tools::getAdminToken("AdminPayment".intval(Tab::getIdFromClassName('AdminPayment')).intval($cookie->id_employee)).'">'.translate('Payment').'</a></li>'; 
					}
                    if(isset($_GET["tab"]) && $_GET["tab"]=="AdminMeta"){
						$id_parent = -1;
					}
										
					if (isset($id_parent) AND $id_parent != -1 AND $id_parent!= 0)
					{
					 	$subTabs = Tab::getTabs(intval($cookie->id_lang), intval($id_parent));
						foreach ($subTabs AS $t)
							if (checkTabRights($t['id_tab']) === true){
								
                                if($t['class_name'] == "AdminCarts"){
                                }elseif($t['class_name'] == "AdminReturn" || $t['class_name'] == "AdminSlip" || $t['class_name'] == "AdminStatuses"){
                                }elseif($t['class_name'] == "AdminStates"  || $t['class_name'] == "AdminCountries" || $t['class_name'] == "AdminZones"){
                                }elseif($t['class_name'] == "AdminDiscounts" ){
                                }elseif($t['class_name'] == "AdminOrders" ){
                                    echo '
    								<li>
    								<a href="index.php?tab='.$t['class_name'].'&token='.Tools::getAdminToken($t['class_name'].intval($t['id_tab']).intval($cookie->id_employee)).'"><img src="../img/t/'.$t['class_name'].'.gif" alt="" style="width:16px;height:16px" /></a> <a href="index.php?tab='.$t['class_name'].'&token='.Tools::getAdminToken($t['class_name'].intval($t['id_tab']).intval($cookie->id_employee)).'">'.$t['name'].'</a>
    								</li>';
                                }else{
                                    echo '
    								<li>
    								<a href="index.php?tab='.$t['class_name'].'&token='.Tools::getAdminToken($t['class_name'].intval($t['id_tab']).intval($cookie->id_employee)).'"><img src="../img/t/'.$t['class_name'].'.gif" alt="" style="width:16px;height:16px" /></a> <a href="index.php?tab='.$t['class_name'].'&token='.Tools::getAdminToken($t['class_name'].intval($t['id_tab']).intval($cookie->id_employee)).'">'.$t['name'].'</a>
    								</li>';
                                }	
								
							}
                            
					}
				?>
				</ul>
                <?php $comes = $_GET["comes"];?>
				<div id="content" class="<?php echo (isset($comes) && $comes != "") ? 'bg-remote' : '' ?>">
