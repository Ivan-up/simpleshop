{* departments_list.tpl *}
{load_presentation_object filename="departments_list" assign="obj"}
{* Начало списка отделов*}
<div class="box">
	<p class="box-title">Choose a Department</p>
	<ul>
	{* Перебираем элементы списка отделов *}
	{section name=i loop=$obj->mDepartments}
		{assign var=selected value=""}
		{* Проверяем, выделен ли отдел, чтобы определить, какой стиль
			CSS использовать*}
		{if ($obj->mSelectedDepartment == 
				$obj->mDepartments[i].department_id)}
			{assign var=selected value="class=\"selected\""}
		{/if}
		<li>
			{* Генерируем ссылку для нового отдела в списке *}
			<a {$selected} href="{$obj->mDepartments[i].link_to_department}">
				{$obj->mDepartments[i].name}
			</a>
		</li>
	{/section}
	</ul>
</div>
{* Конец списка отделов *}