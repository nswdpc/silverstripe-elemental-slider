<div class="{$ElementStyles}">
    <div class="slider-element__content">
        <% if $ShowTitle && $Title %>
            <h2 class="content-element__title">{$Title.XML}</h2>
        <% end_if %>
        <% if $HTML %>
        {$HTML}
        <% end_if %>
        <% if $HeroLink %>
            <% with $HeroLink %>
                <p><a href="{$LinkURL}">{$Title}</a></p>
            <% end_with %>
        <% end_if %>
    </div>
    <% if $SortedSlides %>
        <div class="slider-element__slides">
            <% loop $SortedSlides %>
                {$Me}
            <% end_loop %>
        </div>
    <% end_if %>
</div>
