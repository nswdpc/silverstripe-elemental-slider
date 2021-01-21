<div class="{$ElementStyles}">
    <div class="slider-element__content">
        <% if $ShowTitle %>
            <h2 class="content-element__title">{$Title.XML}</h2>
        <% end_if %>
       {$HTML}
    </div>
    <% if $Slides %>
        <div class="slider-element__slides">
            <% loop $SortedSlides %>
                <div>
                    <a<% if $Title %> title="{$Title.XML}"<% end_if %> href="{$Link.LinkURL}">
                        <% include SlideImage Width=$ThumbWidth, Height=$ThumbHeight %>
                    </a>
                </div>
            <% end_loop %>
        </div>
    <% end_if %>
</div>
