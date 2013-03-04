{include file='head.tpl'}
  {if isset($tplType)}
    {include file="../tplTypes/{$tplType}.tpl"}
  {else}
    {include file="../tplTypes/main.tpl"}
  {/if}
{include file='end.tpl'}