{@begin=main@}
<table>
	<thead>
		<tr>
			<td><?php echo $this->sortLink('id') ?></td>
			{*header*}
			<td>actions</td>
		</tr>
	</thead>
	<tbody>

<?php foreach($this->oPaginator as $oItem): ?>
		<tr>
			<td><?php echo $oItem->getId() ?></td>
			{*content*}
			<td>
				<a href="<?php echo $this->getUrl(['id' => $oItem->getId()], 'edit') ?>">edit</a> |
				<a href="<?php echo $this->getUrl(['id' => $oItem->getId()], 'delete') ?>">del</a>
			</td>
		</tr>
<?php endforeach; ?>

	</tbody>
	<tfoot>
		<tr>
			<td>
				<a href="<?php echo $this->getUrl([], 'add') ?>">+ add</a>
			</td>
		</tr>
	</tfoot>
</table>

<?php echo $this->paginator($this->oPaginator);?>
{@end=main@}
{@begin=header-simple@}
			<td>{*name*}</td>
{@end=header-simple@}
{@begin=header-sort@}
			<td><?php echo $this->sortLink('{*name*}') ?></td>
{@end=header-sort@}
{@begin=content-simple@}
			<td><?php echo $oItem->{*method*}() ?></td>
{@end=content-simple@}
{@begin=content-trim@}
			<td><?php echo $this->trimText($oItem->{*method*}(), 15) ?></td>
{@end=content-trim@}