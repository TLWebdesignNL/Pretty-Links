<?php
/**
 * @package        Joomla.Site
 * @subpackage     mod_prettylinks
 *
 * @copyright      Copyright (C) 2022 TLWebdesign. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

if (is_object($data)) :
	?>
    <div class="d-flex justify-content-center align-items-center flex-wrap py-2">
		<?php foreach ($data as $row) :
			if ($row->text || $row->iconclass) :
				?>
                <span class="me-3 small">
                    <?php $target = ($row->urltarget) ? $row->urltarget : '_blank'; ?>
					<?php echo ($row->url) ? '<a target="' . $target . '" href="' . $row->url . '">' : ''; ?>
	    				<?php echo ($row->iconclass) ? '<i class="' . $row->iconclass . ' pe-2"></i>' : ''; ?>
                        <span class="<?php echo ($row->hidemobile == "1") ? 'd-none d-md-inline' : ''; ?>"><?php echo ($row->text) ? $row->text : ''; ?></span>
					<?php echo ($row->url) ? '</a>' : ''; ?>
			    </span>
			<?php
			endif; // if $row->text
		endforeach; // foreach $data as $row
		?>
    </div>
<?php
endif // check if object
?>