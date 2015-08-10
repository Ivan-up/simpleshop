{load_presentation_object filename="product" assign="obj"}
<h1 class="title">{$obj->mProduct.name}</h1>
{if $obj->mProduct.image}
<img src="{$obj->mProduct.image}" alt="{$obj->mProduct.name} image" class="product-image"/>
{/if}
{if $obj->mProduct.image_2}
<img src="{$obj->mProduct.image_2}" alt="{$obj->mProduct.name} image 2" class="product-image"/>
{/if}
<p class="description">{$obj->mProduct.description}</p>
<p class="section">
	Price: 
	{if $obj->mProduct.discounted_price != 0}
		<span class="old-price">{$obj->mProduct.price}</span>
		<span class="price">{$obj->mProduct.discounted_price}</span>
	{else}
		<span class="price">{$obj->mProduct.discounted_price}</span>
	{/if}
</p>

{* Форма добавления в корзину *}
<form class="add-product-form" target="_self" method="post"
 action="{$obj->mProduct.link_to_add_product}">

{* Генерируем списки значение атрибутов *}
<p class="attributes">

{* Просматриваем список атрибутов и их значений*}
{section name=k loop=$obj->mProduct.attributes}
	
	{* Генерируем новый тег select? *}
	{if $smarty.section.k.first ||
			$obj->mProduct.attributes[k].attribute_name !== 
			$obj->mProduct.attributes[k.index_prev].attribute_name}
		{$obj->mProduct.attributes[k].attribute_name}:
	<select name="attr_{$obj->mProduct.attributes[k].attribute_name}">
	{/if}
	
		{* Генерируем новый тег option *}
		<option value="{$obj->mProduct.attributes[k].attribute_value}">
			{$obj->mProduct.attributes[k].attribute_value}
		</option>
	{* Закрываем тег select? *}
	{if $smarty.section.k.last ||
			$obj->mProduct.attributes[k].attribute_name !== 
			$obj->mProduct.attributes[k.index_next].attribute_name}
	</select>
	{/if}
	
{/section}
</p>

{* Кнопка добавления к корзину *}
<p>
  <input type="submit" name="submit" value="Add to Cart" />
</p>
</form>

{* Отображаем кнопку редактирования для администратора *}
{if $obj->mShowEditButton}
<form action="{$obj->mEditActionTarget}" target="_self" 
	method="post" class="edit-form">
	<p>
		<input type="submit" name="submit_edit" value="Edit Product Details" />
	</p>
</form>
{/if}

{if $obj->mLinkToContinueShopping}
<a href="{$obj->mLinkToContinueShopping}">Continue Shopping</a>
{/if}
<h2>Find similar product in our catalog:</h2>
<ol>
{section name=i loop=$obj->mLocations}
	<li class="navigation">
		{strip}
		<a href="{$obj->mLocations[i].link_to_department}">
			{$obj->mLocations[i].department_name}
		</a>
		{/strip}
		&raquo;
		{strip}
		<a href="{$obj->mLocations[i].link_to_category}">
			{$obj->mLocations[i].category_name}
		</a>
		{/strip}
	</li>
{/section}
</ol>