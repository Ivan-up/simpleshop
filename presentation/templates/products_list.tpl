{* products_list *}
{load_presentation_object filename="products_list" assign="obj"}
{if $obj->mSearchDescription != ""}
	<p class="description">{$obj->mSearchDescription}</p>
{/if}
{if count($obj->mProductListPages) > 0}
<p>

	{if $obj->mLinkToPreviousPage}
	<a href="{$obj->mLinkToPreviousPage}">Previous page</a>
	{/if}
	
	{section name=m loop=$obj->mProductListPages}
		{if $obj->mPage eq $smarty.section.m.index_next}
		<strong>{$smarty.section.m.index_next}</strong>
		{else}
		<a href="{$obj->mProductListPages[m]}">
			{$smarty.section.m.index_next}
		</a>
		{/if}
	{/section}
	
	{if $obj->mLinkToNextPage}
	<a href="{$obj->mLinkToNextPage}">Next page</a>
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
					
					 {* Форма добавления в корзину *}
					<form class="add-product-form" target="_self" method="post"
					 action="{$obj->mProducts[k].link_to_add_product}"
					 onsubmit="retutn addProductToCart(this);">
					
					
					{* Генерируем список атрибутов и их значений *}					
					<p class="attributes">
					
					{* Просматриваем список атрибутов и их значений *}
					{section name=l loop=$obj->mProducts[k].attributes}
					
						{* Генерируем новый тег select? *}
						{if $smarty.section.l.first || 
								$obj->mProducts[k].attributes[l].attribute_name !== 
								$obj->mProducts[k].attributes[l.index_prev].attribute_name}
							{$obj->mProducts[k].attributes[l].attribute_name}: 
						<select name="attr_{$obj->mProducts[k].attributes[l].attribute_name}">
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
					 {* Кнопка добавления в корзину *}
					<p>
						<input type="submit" name="add_to_cart" value="Add to Cart" />
					</p>
					</form>
					
					{* Отображаем кнопку редактирования для администраторов *}
					{if $obj->mShowEditButton}
						<form action="{$obj->mEditActionTarget}" target="_self" 
							method="post" class="edit-form">
						<input type="hidden" name="product_id" 
							value="{$obj->mProducts[k].product_id}"/>
						<input type="submit" name="submit" value="Edit Product Details" />
					</form>
					{/if}					
					
				</td>
			{if $smarty.section.k.index % 2 != 0 && !$smarty.section.k.first ||
					$smarty.section.k.last}
			</tr>
			{/if}
		{/section}
	</tbody>
</table>
{/if}