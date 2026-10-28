<% if $ThumbWidth && $ThumbWidth %>
    {$Image.FillMax($ThumbWidth.Value,$ThumbWidth.Value)}
<% else %>
    {$Image.FillMax(128,96)}
<% end_if %>
