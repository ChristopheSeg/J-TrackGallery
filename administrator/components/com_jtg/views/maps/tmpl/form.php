<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 * 
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

defined('_JEXEC') or die('Restricted access');
$id = $this->_models['maps']->_id;
// toolbar
if($id < 1) 
	$title = JText::_('COM_JTG_ADD_MAP');
else
	$title = JText::_('COM_JTG_EDIT_MAP');

JToolBarHelper::title($title, 'categories.png');
JToolBarHelper::back();
JToolBarHelper::spacer();
if($id < 1):
	JToolBarHelper::save('savemap', $alt='COM_JTG_SAVE', 'save.png' );
else:
	JToolBarHelper::save('updatemap', $alt='COM_JTG_SAVE', 'save.png' );
	// find correct map
	$maps = $this->maps;
	foreach($maps AS $map)
		if ($map->id == $id)
			break;
endif;
JToolBarHelper::help("maps/newmap", true);
if($id) {
	$cache =& JFactory::getCache('com_jtg');
	$cfg = JtgHelper::getConfig();
	$model = $this->getModel();
	$document =& JFactory::getDocument();
}
?>
<form action="" method="post" name="adminForm" id="adminForm" class="adminForm" enctype="multipart/form-data">
    <table class="adminlist" cellpadding="1">
        <thead>
            <tr>
                <th colspan="3" align="center"><?php echo $title; ?></th>
            </tr>
        </thead>
        <tbody>
<?php if($id) { ?>
            <tr>
                <td>Id:</td>
                <td><?php echo $id; ?></td>
            </tr><?php } ?>
            <tr>
                <td><?php echo JText::_('COM_JTG_NAME'); ?>:*</td>
                <td><input id="name" type="text" name="name" value="<?php if (($id) AND (isset($map->name))) echo $map->name; ?>" size="30" maxsize="30" /> (<?php  if (($id) AND (isset($map->name))) echo JText::_($map->name); ?>)</td>
            </tr>
            <tr>
                <td><?php echo JText::_('COM_JTG_PUBLISHED'); ?>:*</td>
                <td><?php echo $this->list['published']; ?></td>
            </tr>
            <tr>
                <td><?php echo JText::_('COM_JTG_PARAMETERS'); ?>:*</td>
                <td><textarea id="param" type="text" name="param" value="" cols="100" maxsize="300" rows="8"><?php
	$replace = array("'",'"');
	$with = array("'","&quot;");
if (($id) AND (isset($map->param)))
{
	$param = str_replace($replace,$with,$map->param);
	echo $param;
}
?></textarea>
		
		    
</td>
            </tr>
            <tr>
                <td><?php echo JText::_('COM_JTG_NEEDSCRIPT'); ?>:</td>
                <td><input id="script" type="text" name="script" value="<?php
if (($id) AND (isset($map->script)))
{
	$script = str_replace($replace,$with,$map->script);
	echo $script;
}
?>" size="100" maxsize="300" /></td>
            </tr>
            <tr>
                <td><?php echo JText::_('COM_JTG_CODE'); ?>:</td>
                <td><input id="code" type="text" name="code" value="<?php
if (($id) AND (isset($map->code)))
{
	$code = str_replace($replace,$with,$map->code);
	echo $code;
}
?>" size="100" maxsize="300" /></td>
            </tr>
            <tr>
                <td><?php echo JText::_('COM_JTG_ORDER'); ?>:*</td>
                <td><input id="order" type="text" name="order" value="<?php if (($id) AND (isset($map->ordering))) echo $map->ordering; else echo 99; ?>" size="2" maxsize="2" </td>
            </tr>
        </tbody>
    </table>
    <?php
    echo JHtml::_( 'form.token' ); ?>
    <input type="hidden" name="option" value="com_jtg" />
    <input type="hidden" name="controller" value="maps" />
    <input type="hidden" name="checked_out" value="0" />
    <input type="hidden" name="task" value="maps" />
<?php
if ($id)
{ ?>
	<input type="hidden" name="id" value="<?php echo $id; ?>" />
	
<?php }
?>
</form>
