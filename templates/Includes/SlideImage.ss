<% if $Width && $Height %>
    {$Image.FillMax($ThumbWidth,$ThumbWidth)}
<% else %>
    {$Image.FillMax(128,96)}
<% end_if %>
