{@begin=main@}
<form method="post" action="">
<?php if(!$this->bEdit): ?>
	<fieldset>
		{*fields-add*}
	</fieldset>
<?php endif; ?>
	<fieldset>
		{*fields-edit*}
	</fieldset>
	<fieldset>
		<button type="submit">save</button>
		<a href="<?php echo $this->getUrl([], 'list') ?>">cancel</a>
	</fieldset>
</form>
{@end=main@}
{@begin=field-text@}
	<?php $sField = '{*name*}'; echo $this->formField(
		'{*uc-name*}',
		$sField,
		$this->formText($sField, $this->formValue($sField))
	)?>
{@end=field-text@}
{@begin=field-textarea@}
	<?php $sField = '{*name*}'; echo $this->formField(
		'{*uc-name*}',
		$sField,
		$this->formTextarea($sField, $this->formValue($sField), array('rows' => 5))
	)?>
{@end=field-textarea@}
{@begin=field-select@}
	<?php $sField = '{*name*}'; echo $this->formField(
		'{*uc-name*}',
		$sField,
		$this->formSelect($sField, $this->formValue($sField), null, array(
			{*select-list*}
		))
	)?>
{@end=field-select@}
{@begin=select-list@}
			'{*name*}'	=> '{*name*}'
{@end=select-list@}