<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: form.php,v 1.1 2011/04/03 08:41:52 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// toolbar
JToolBarHelper::title(JText::_('COM_JTG_EDIT_COMMENT'), 'addedit.png');
JToolBarHelper::back($alt= 'COM_JTG_BACK', $href= 'javascript:history.back();');
JToolBarHelper::spacer();
JToolBarHelper::save('saveComment', $alt='COM_JTG_SAVE', 'save.png' );
JToolBarHelper::help( 'comments/form',true );
?>
<form class="adminForm" name="adminForm" id="adminForm" method="post" action="">
<table class="adminlist">
	<thead>
		<tr>
			<th colspan="2"><?php echo JText::_('COM_JTG_EDIT_COMMENT'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo JText::_('COM_JTG_USER'); ?></td>
			<td>
				<input type="text" name="user" value="<?php echo $this->comment->user; ?>" size="30" readonly="readonly" />
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_EMAIL'); ?></td>
			<td>
				<input type="text" name="email" value="<?php echo $this->comment->email; ?>" size="30" readonly="readonly" />
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_TITLE'); ?></td>
			<td>
				<input type="text" name="title" value="<?php echo $this->comment->title; ?>" size="30" />
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_COMMENT_TEXT'); ?></td>
			<td><?php echo $this->editor->display( 'text', $this->comment->text , '500', '200', '100', '50', true ) ; ?></td>
		</tr>
	</tbody>
</table>
<?php
echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_jtg" />
<input type="hidden" name="controller" value="comments" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->comment->id; ?>" />
</form>
