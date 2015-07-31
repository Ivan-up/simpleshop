{* search_box.tpl *}
{load_presentation_object filename="search_box" assign="obj"}
{* Начала поля поиска *}
<div class="box">
	<p class="box-title">Search the Catalog</p>
	<form action="{$obj->mLinkToSearch}" method="post" class="search_form">
		<p>
			<input type="text" id="search_string" maxlength="100" name="search_string"
				value="{$obj->mSearchString}" size="19"/>
			<input type="submit" value="Go!" /> <br/>
		</p>
		<p>
			<input type="checkbox" id="all_words" name="all_words"
			 {if $obj->mAllWords == "on"} checked="checked" {/if} />
			 Search for all words
		</p>
	</form>
</div>
{* Конец поля поиска *}