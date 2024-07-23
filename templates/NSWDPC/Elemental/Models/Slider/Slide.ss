<div class="slide">
    <% if $Link %><a<% if $Title %> title="{$Title.XML}"<% end_if %> href="{$Link.LinkURL}"><% end_if %>
    <% include SlideImage Width=$ThumbWidth, Height=$ThumbHeight %>
    <% if $Link %></a><% end_if %>
    <% if $Content %>
    <p class="caption">{$Content}</p>
    <% end_if %>
</div>
