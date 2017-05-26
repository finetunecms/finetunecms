@if(isset($node))
    @if(Entrust::ability('Superadmin,admin', 'can_administer_website,can_manage_'.$node->site->tag))
    <div class="admin admininterface">
        <p>Admin Interface</p>
    </div>
    @endif
@endif