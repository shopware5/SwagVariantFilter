{block name="frontend_index_header_css_screen" append}
    <link type="text/css" media="screen, presentation" rel="stylesheet" href="{link file='frontend/plugins/SwagVariantFilter/_resources/styles/variant_filter.css'}" />
{/block}


{block name='frontend_listing_right_filter_properties' append}
<div class="filter_properties">
    <span class="headingbox_nobg filter-heading">{s namespace="frontend/SwagVariantFilter/main" name='frontend/SwagVariantFilter/FilterHeadlineVariants'}{/s}</span>
    <div class="supplier_filter">
    {foreach $GroupArray AS $Group}
        <div>{$Group.GroupName} <span class="expandcollapse">+</span></div>
        <div class="slideContainer">
            <ul>

                {foreach $Group.Options AS $Option}

                    {if $Option.Active}
                        <li class="active">
                            <a class="activeVariant" href="{$BaseURL}?p=1&oid={$Option.IdForRemoveURL}" title="{$Option.Name}">{$Option.Name}</a>
                        </li>
                    {else}
                        <li><a href="{$BaseURL}?p=1&oid={$Option.IdForURL}" title="{$Option.Name}">{$Option.Name}</a></li>
                    {/if}

                {/foreach}

                {if $Group.SubValueIsActive}
                    <li class="close"><a href="{$BaseURL}?p=1" title="{$Option.Name}">{s namespace="frontend/SwagVariantFilter/main" name='frontend/SwagVariantFilter/ListingFilterBoxShowAll'}{/s}</a></li>
                {/if}

            </ul>
        </div>
    {/foreach}
    </div>
</div>
{/block}

