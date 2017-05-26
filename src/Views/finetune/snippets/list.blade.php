@extends('finetune::layouts.admin')

@section('manage')
    <i v-if="loading" class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>
    <div class="content" v-cloak v-else>

        <div class="table-responsive" v-if="groups.length != 0">

            <table class="table table-striped">
                <thead>
                <tr class="active">
                    <th class="th-select">{{ trans('finetune::snippets.list.select') }}</th>
                    <th class="th-title">{{ trans('finetune::snippets.list.title') }}</th>
                    <th class="th-actions">{{ trans('finetune::snippets.list.actions') }}</th>
                </tr>
                </thead>
                <tbody id="sortable">
                <tr v-for="item in groups" class="tr-@{{ item.tag }}" :class="renderSelectedRow(item)">
                @include('finetune::partials.select')
                    <td class="title"><a href="#edit" v-on:click="edit(item)">@{{ item['title'] }}</a></td>
                    <td class="actions"><a href="/admin/snippets/@{{ item['id'] }}" class="btn btn-sm btn-plain">{{ trans('finetune::snippets.manage') }}</a></td>
                </tr>
                </tbody>
            </table>
            @include('finetune::partials.destroyModal')
        </div>

        <div v-else >
            <div class="no-content">
                <p class="text-lg-center">{{ trans('finetune::snippets.group.empty') }}</p>
                <a href="#" class="btn btn-success btn-block" @click='createGroup'>{{ trans('finetune::snippets.group.create') }}</a>
            </div>
        </div>

        @include('finetune::snippets.groupmodal')
    </div>
@stop

@section('controls')
    <div class="actions">
        <div class="btn-group">
            <a href="#" class="btn" @click='destroy'>{{ trans('finetune::snippets.group.delete') }}</a>
            <a href="#" class="pull-right btn btn-success" @click='createGroup'>{{ trans('finetune::snippets.group.create') }}</a>
        </div>
    </div>
@stop


@section('breadcrumb')
    @include('finetune::partials.breadcrumbs', ['array' => ['admin'=>'admin', 'content'=>'snippet groups']])
@stop