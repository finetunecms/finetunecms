@extends('finetune::layouts.admin')

@section('manage')
    <sync-loader :loading="loading" :color="color" :size="size" v-if="loading"></sync-loader>
    <i v-if="loading" class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>
    <div class="content" v-cloak v-else>

        <div class="table-responsive" v-if="sites.length != 0">
            <table class="table table-striped">
                <thead>
                <tr class="active">
                    <th class="th-select">{{ trans('finetune::sites.list.select') }}</th>
                    <th class="th-title">{{ trans('finetune::sites.list.title') }}</th>
                    <th class="th-actions">{{ trans('finetune::sites.list.actions') }}</th>
                </tr>
                </thead>
                <tbody id="sortable">
                <tr v-for="item in sites" class="tr-@{{ item.tag }}" :class="renderSelectedRow(item)">
                @include('finetune::partials.select')
                    <td class="title"><a href="#edit" v-on:click="edit(item)">@{{ item['title'] }}</a></td>
                    <td class="actions"><a href="/admin/sites/@{{ item['id'] }}" class="btn btn-sm btn-plain">{{ trans('finetune::sites.manage') }}</a></td>
                </tr>
                </tbody>
            </table>
            @include('finetune::partials.destroyModal')
        </div>

        <div v-else >
            <p>{{ trans('finetune::sites.empty') }}</p>
            <a href="#" class="btn btn-success" @click='create'>{{ trans('finetune::sites.create') }}</a>
        </div>

        @include('finetune::sites.updateModal')
    </div>
@stop

@section('controls')
    <div class="actions">
        <div class="btn-group">
            @if(Entrust::hasRole('Superadmin'))
                <a href="#" class="btn" @click='destroy'>{{ trans('finetune::sites.delete') }}</a>
                <a href="#" class="btn btn-success" @click='create'>{{ trans('finetune::sites.create') }}</a>
            @endif
        </div>
    </div>
@stop

@section('breadcrumb')
@include('finetune::partials.breadcrumbs', ['array' => ['admin'=> trans('finetune::main.admin'), 'content'=> trans('finetune::sites.title')]])
@stop