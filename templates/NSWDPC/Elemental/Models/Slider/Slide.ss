<figure>
    <% if $DerivedLink %><a<% if $Title %> title="{$Title.XML}"<% end_if %> href="{$DerivedLink.LinkURL}" rel="noopener noreferrer"<% if $DerivedLink.OpenInNewWindow %> target="_blank"<% end_if %>><% end_if %>
    <% include SlideImage Width=$ThumbWidth, Height=$ThumbHeight %>
    <% if $DerivedLink %></a><% end_if %>
    <% if $Content %>
    <figcaption>{$Content}</figcaption>
    <% end_if %>
</figure>
