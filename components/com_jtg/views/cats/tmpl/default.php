<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: default.php,v 1.2 2011/04/08 15:48:22 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage frontend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

echo $this->lh;
?>
<table width="100%" class="tracktable" >
    <thead>
        <tr class="sectiontableheader">
            <th colspan="2" width="100px" align="center"><?php echo JText::_('COM_JTG_CAT'); ?></th>
            <th align="center"><?php echo JText::_('COM_JTG_DESCRIPTION'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
        $k = 0;
		$imgdir = JURI :: base() . "images/jtg/cats/";
        for($i=0, $n=count($this->cats); $i<$n; $i++) {
            $cat = $this->cats[$i];
            $cat->img = null;
        	if ($cat->image)
        		$cat->img = "&nbsp;<img title=\"".JText::_($cat->name)."\" alt=\"".JText::_($cat->name)."\" src=\"".$imgdir.$cat->image."\" />";
            $link = JRoute::_('index.php?option=com_jtg&view=files&layout=list&cat='.$cat->id);
            ?>
    <tr class="sectiontableentry<?php echo $k; ?>">
        <td width="10%" align="center"><a href="<?php echo $link; ?>" ><?php echo $cat->img; ?></a></td>
        <td><b><a href="<?php echo $link; ?>" ><?php echo JText::_($cat->name); ?></a></b></td>
        <td><?php echo $cat->description; ?></td>
    </tr>
            <?php
        $k = 1 -$k;
        }
    ?>
    </tbody>
</table>

<?php
echo $this->footer;
