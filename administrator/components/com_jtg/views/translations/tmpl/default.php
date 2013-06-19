<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: default.php,v 1.2 2011/04/22 07:36:30 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('COM_JTG_TRANSLATIONS'), 'categories.png');
JToolBarHelper::save('saveLanguages', $alt='COM_JTG_SAVE', 'save.png' );
JToolBarHelper::help( 'translations',true );
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist" cellpadding="1">
		<thead>
			<tr>
<?php
foreach ($this->languages as $lang) {
?>
				<th width="1%" class="title"><?php echo $lang['header']; ?></th>
<?php
}
?>
				<th class="title">
<!--				blank-->
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
<?php
foreach ($this->languages as $lang) {
?>
				<td class="title" valign="top">
					<textarea name="<?php echo $lang['tag']; ?>" cols="<?php echo $lang['cols']; ?>" rows="<?php echo $lang['rows']; ?>"><?php echo $lang['value']; ?></textarea>
				</td>
<?php
}
?>
				<td class="title">
<!--				blank-->
				</td>
			</tr>
		</tbody>
	</table>
    <input type="hidden" name="option" value="com_jtg" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="translations" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
