<% if $Width && $Height %>
    {$Image.FillMax($Width,$Height)}
<% else %>
    {$Image.FillMax(128,96)}
<% end_if %>
