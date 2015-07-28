{* products_list *}
{load_presentation_object filename="products_list" assign="obj"}
{if $obj->mrTotalPages > 1}
<p>
	Page {$obj->mPage} of {$obj->mrTotalPages}
	{if $obj->mLinkToPreviousPage}
	<a href="{$obj->mLinkToPreviousPage}">Previous</a>
	{else}
	Previous
	{/if}
	{if $obj->mLinkToNextPage}
	<a href="{$obj->mLinkToNextPage}">Next</a>
	{else}
	Next
	{/if}
</p>
{/if}
{if $obj->mProducts}
<table class="product-list" border="0">
	<tbody>
		{section name=k loop=$obj->mProducts}
			{if $smarty.section.k.index % 2 == 0}
			<tr>
			{/if}
				<td valign="top">
					<h3 class="product-title">
						<a href="{$obj->mProducts[k].link_to_product}">
							{$obj->mProducts[k].name}
						</a>
					</h3>
					<p>
						{if $obj->mProducts[k].thumbnail neq ""}
						<a href="{$obj->mProducts[k].link_to_product}">
							<img src="{$obj->mProducts[k].thumbnail}" 
								alt="{$obj->mProducts[k].name}"/>
						</a>
						{/if}
						{$obj->mProducts[k].description}
					</p>
					<p class="section">
						Price:
						{if $obj->mProducts[k].discounted_price != 0}
							<span class="old-price">{$obj->mProducts[k].price}</span>
							<span class="price">{$obj->mProducts[k].discounted_price}</span>
						{else}
							<span class="price">{$obj->mProducts[k].price}</span>
						{/if}
					</p>
					{* Генерируем список атрибутов и их значений *}					
					<p class="attributes">
					
					{* Просматриваем список атрибутов и их значений *}
					{section name=l loop=$obj->mProducts[k].attributes}
					
						{* Генерируем новый тег select? *}
						{if $smarty.section.l.first || 
								$obj->mProducts[k].attributes[l].attribute_name !== 
								$obj->mProducts[k].attributes[l.index_prev].attribute_name}
							{$obj->mProducts[k].attributes[l].attribute_name}:
						<select name="attr_{$obj->mProducts[k].attributes[l].attributes_name}">
						{/if}
							
							{* Генерируем новый тег option *}
							<option value="{$obj->mProducts[k].attributes[l].attribute_value}">
								{$obj->mProducts[k].attributes[l].attribute_value}
							</option>
							
						{* Закрываем тег select? *}
						{if $smarty.section.l.last ||
								$obj->mProducts[k].attributes[l].attribute_name !==
								$obj->mProducts[k].attributes[l.index_next].attribute_name}
						</select>
						{/if}
						
					{/section}
					</p>
				</td>
			{if $smarty.section.k.index % 2 != 0 && !$smarty.section.k.first ||
					$smarty.section.k.last}
			</tr>
			{/if}
		{/section}
	</tbody>
</table>
{/if}