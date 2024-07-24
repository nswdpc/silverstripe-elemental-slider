<figure>
    <% if $Link %><a<% if $Title %> title="{$Title.XML}"<% end_if %> href="{$Link.LinkURL}"><% end_if %>
    <% include SlideImage Width=$ThumbWidth, Height=$ThumbHeight %>
    <% if $Link %></a><% end_if %>
    <% if $Content %>
    <figcaption>{$Content}</figcaption>
    <% end_if %>
</figure>
