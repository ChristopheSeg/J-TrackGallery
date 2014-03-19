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

// toolbar
JToolBarHelper::title(JText::_('COM_JTG_TERRAIN'), 'categories.png');
JToolBarHelper::back();
JToolBarHelper::spacer();
JToolBarHelper::addNew('newterrain', 'COM_JTG_NEW_TERRAIN');
JToolBarHelper::editList('editterrain');
JToolBarHelper::publish();
JToolBarHelper::unpublish();
JToolBarHelper::deleteList();
JToolBarHelper::help( 'terrain',true );
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <table class="adminlist" cellpadding="1">
        <thead>
            <tr>
                <th width="5%" class="title"><?php echo JText::_( 'COM_JTG_NUM' ); ?></th>
                <th width="5%" class="title">
                    <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(<?php echo count($this->rows); ?>);" />
                </th>
                <th width="60%" class="title"><?php echo JText::_( 'COM_JTG_TITLE'); ?></th>
                <th width="21%" class="title"><?php echo JText::_( 'COM_JTG_PUBLISHED'); ?></th>
                <th width="1%" class="title" nowrap="nowrap"><?php echo JText::_( 'COM_JTG_ID'); ?></th>
	    </tr>
         </thead>
        <tfoot>
            <tr>
                <td colspan="5">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
         <tbody>
         <?php
	$k = 0;
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
	{
		$row = &$this->rows[$i];

		$link 	= JRoute::_( 'index.php?option=com_jtg&task=editterrain&controller=terrain&id='. $row->id );

		$checked 	= JHtml::_('grid.checkedout',   $row, $i );
		$published 	= JHtml::_('grid.published', $row, $i );

             ?>
             <tr class="<?php echo "row" . $k; ?>">
                <td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
                <td align="center"><?php echo $checked; ?></td>
                <td align="center"><a href="javascript:void(0);" onclick="javascript:return listItemTask('cb<?php echo $i; ?>','editterrain')"><?php echo JText::_($row->title); ?></a></td>
                <td align="center"><?php echo $published;?></td>
                <td><?php echo $row->id; ?></td>
             </tr>
             <?php
             $k = 1 - $k;
         }
         ?>
         </tbody>
    </table>
    <input type="hidden" name="option" value="com_jtg" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="terrain" />
    <?php echo JHtml::_( 'form.token' ); ?>
</form>
