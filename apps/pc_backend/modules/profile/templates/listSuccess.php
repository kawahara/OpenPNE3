<?php use_helper('Javascript'); ?>
<h2>プロフィール項目設定</h2>

<h3>プロフィール項目一覧</h3>
<p><?php echo link_to('プロフィール項目登録', 'profile/edit') ?></p>
<table>
<thead><tr>
<th colspan="3">操作</th>
<th>ID</th>
<th>項目名</th>
<th>識別名</th>
<th>必須</th>
<th>公開設定変更の可否</th>
<th>公開設定デフォルト値</th>
<th>重複の可否</th>
<th>フォームタイプ</th>
<th>選択肢</th>
<th>登録</th>
<th>変更</th>
<th>検索</th>
</tr></thead>
<tbody id="profiles">
<?php foreach ($profiles as $value): ?>
<tr id="profile_<?php echo $value->getId() ?>">
<td class="sort-handle sortable">||</td>
<td class="sort-handle-next"><?php echo link_to('変更', 'profile/edit?id=' . $value->getId()) ?></td>
<td><?php echo link_to('削除', 'profile/delete?id=' . $value->getId()) ?></td>
<td><?php echo $value->getId() ?></td>
<?php if ($value->isPreset()) : ?>
<?php $presetConfig = $value->getPresetConfig(); ?>
<td><?php echo __($presetConfig['Caption']) ?></td>
<?php else: ?>
<td><?php echo $value->getCaption() ?></td>
<?php endif; ?>
<td><?php echo $value->getName() ?></td>
<td><?php echo ($value->getIsRequired() ? '○' : '×') ?></td>
<td><?php echo ($value->getIsEditPublicFlag() ? '○' :'×') ?></td>
<td><?php echo (Doctrine::getTable('Profile')->getPublicFlag($value->getDefaultPublicFlag())) ?></td>
<td><?php echo ($value->getIsUnique() ? '×' :'○') ?></td>
<td><?php echo $value->getFormType() ?></td>
<td>
<?php if (!$value->isPreset() && ($value->getFormType() == 'radio' || $value->getFormType() == 'checkbox' || $value->getFormType() == 'select')) : ?>
<?php echo link_to('一覧', 'profile/list', array('anchor' => $value->getName())) ?>
<?php else: ?>
-
<?php endif; ?>
</td>
<td><?php echo ($value->getIsDispRegist() ? '○' :'×') ?></td>
<td><?php echo ($value->getIsDispConfig() ? '○' :'×') ?></td>
<td><?php echo ($value->getIsDispSearch() ? '○' : '') ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php echo sortable_element('profiles',array(
  'tag' => 'tr',
  'url' => 'profile/sortProfile',
  'handles' => '$$("#profiles .sort-handle")',
)) ?>

<h3>プロフィール選択肢一覧</h3>
<?php $selectionCount = 0; ?>
<?php foreach ($profiles as $value): ?>
<?php if (!$value->isPreset() && ($value->getFormType() == 'radio' || $value->getFormType() == 'checkbox' || $value->getFormType() == 'select')) : ?>
<?php $selectionCount++; ?>
<h4><a name="<?php echo $value->getName() ?>"><?php echo $value->getCaption() ?></a></h4>
<?php $optionForm = $optionForms[$value->getId()] ?>
<?php echo $optionForm->renderFormTag(url_for('profile/editOption?id='.$value->getId())) ?>
<table>
<thead>
  <th colspan="2">ID</th>
  <th>項目名(<?php echo $sf_user->getCulture() ?>)</th>
  <th>削除</th>
</thead>
<tbody id="profile_options_<?php echo $value->getId() ?>">
<?php foreach (array_keys($optionForm->getProfileOptions()) as $id): ?>
<tr id="profile_option_<?php echo $id ?>" class="sort">
  <td class="sort-handle sortable">||</td>
  <td class="sort-handle-next"><?php echo $id ?></td>
  <td><?php echo $optionForm[$id]->renderError() ?><?php echo $optionForm[$id] ?></td>
  <td><?php echo $optionForm['is_delete_'.$id]->renderError() ?><?php echo $optionForm['is_delete_'.$id] ?></td>
</tr>
<?php endforeach; ?>
<tr>
  <td colspan="2">新規</td>
  <td><?php echo $optionForm['new']->renderError() ?><?php echo $optionForm['new']->render() ?></td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td colspan="4"><input type="submit" value="<?php echo __('Edit') ?>" /></td>
</td>
</tbody>
</table>
<?php echo sortable_element('profile_options_'.$value->getId(),array(
  'tag'  => 'tr',
  'only' => 'sort',
  'handles' => '$$("#profile_options_'.$value->getId().' .sort-handle")',
)) ?>
<?php echo $optionForm->renderHiddenFields() ?>
</form>

<?php endif; ?>
<?php endforeach; ?>
<?php if (!$selectionCount): ?>
<p>選択肢を設定可能なプロフィール項目がありません。</p>
<?php endif; ?>
