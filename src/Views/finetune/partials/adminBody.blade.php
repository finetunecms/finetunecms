<body>
<style>
    .adminbar {
        width: 100%;
        display: block;
        overflow: hidden;
        background-color:#000;
    }

    .adminbar h5{
        float: left;
        color: #FFF;
        padding: 12px;
    }

    .adminbar ul {
        list-style: none;
        padding: 12px;
    }

    .adminbar a{
        color:#FFF;
    }
    .adminbar li {
        display: inline-block;
        padding: 0 12px;
    }
</style>
<div class="adminbar">
    <h5>Admin Bar</h5>
    <ul class="pull-right">
        <li><a href="/admin/content/{{ $node->id }}/edit">Edit This Page</a></li>
        @if($node->area == 1)
            @if($node->type->nesting == 1)
            <li><a href="/admin/content/{{ $node->id }}">Manage Children of this page</a></li>
                @endif
        @endif
        <li><a href="/auth/logout">Logout</a></li>
    </ul>

</div>
@include('finetune::partials.formopen')