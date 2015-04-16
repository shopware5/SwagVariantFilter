{block name="frontend_index_header_css_screen" append}
    <link type="text/css" media="screen, presentation" rel="stylesheet" href="{link file='frontend/plugins/SwagVariantFilter/_resources/styles/variant_filter.css'}" />
{/block}

{block name="frontend_listing_right_filter_properties" append}
<div class="filter_properties">
    <span class="headingbox_nobg filter-heading">{s namespace="frontend/swag_variant_filter/main" name='FilterHeadlineVariants'}{/s}</span>
    <div class="supplier_filter">
    {foreach $swagVariantFilterConditions AS $filterItem}
        <div>{$filterItem->getLabel()} <span class="expandcollapse">+</span></div>
        <div class="slideContainer">
            <ul>
                {foreach $filterItem->getOptions() as $option}
                    {if $option->isActive()}
                        <li class="active">
                            <a class="activeVariant" href="{$option->getRemoveUrl()}" title="{$option->getLabel()}">{$option->getLabel()}</a>
                        </li>
                    {else}
                        <li>
                            <a href="{$option->getAddUrl()}" title="{$option->getLabel()}">{$option->getLabel()}</a>
                        </li>
                    {/if}
                {/foreach}

                {if $filterItem->hasActiveOptions()}
                    <li class="close">
                        <a href="{$filterItem->getBaseUrl()}?p=1" title="{s namespace="frontend/swag_variant_filter/main" name='ListingFilterBoxShowAll'}{/s}">
                            {s namespace="frontend/swag_variant_filter/main" name='ListingFilterBoxShowAll'}{/s}
                        </a>
                    </li>
                {/if}

            </ul>
        </div>
    {/foreach}
    </div>
</div>
{/block}

