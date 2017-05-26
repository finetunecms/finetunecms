@extends('finetune::layouts.admin')

@section('manage')
    <i v-if="loading" class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>
    <div class="content" v-cloak v-else>

        <div class="table-responsive" v-if="tags.length != 0">

            <table class="table table-striped">
                <thead>
                <tr class="active">
                    <th class="th-select">{{ trans('finetune::tags.list.select') }}</th>
                    <th class="th-title">{{ trans('finetune::tags.list.title') }}</th>
                    <th class="th-actions">{{ trans('finetune::tags.list.actions') }}</th>
                </tr>
                </thead>
                <tbody id="sortable">
                   <tr v-for="item in tags" class="tr-@{{ item.tag }}" :class="renderSelectedRow(item)">
                   @include('finetune::partials.select')
                        <td class="title"><a href="#edit" v-on:click="edit(item)" title="{{ trans('finetune::tags.attributes.edit') }} @{{ item['title'] }}">@{{ item['title'] }}</a></td>
                        <td class="actions"><a href="/admin/tags/@{{ item['id'] }}" class="btn btn-sm btn-plain" title="{{ trans('finetune::tags.attributes.nodes') }}">{{ trans('finetune::tags.nodes') }}</a></td>
                    </tr>
                </tbody>
            </table>

            @include('finetune::partials.destroyModal')

        </div>

        <div v-else >
            <div class="no-content">
                <p class="text-lg-center">{{ trans('finetune::tags.empty') }}</p>
                <a title="{{ trans('finetune::tags.create') }}" href="#" class="btn btn-success btn-block" @click='createTag'>{{ trans('finetune::tags.create') }}</a>
            </div>
        </div>

        @include('finetune::tags.update')
    </div>
@stop

@section('controls')
    <div class="actions">
        <div class="btn-group">
            <a href="#" class="btn" @click='destroy' title="{{ trans('finetune::tags.attributes.delete') }}">{{ trans('finetune::tags.delete') }}</a>
            <a href="#" class="btn btn-success" @click='createTag' title="{{ trans('finetune::tags.create') }}">{{ trans('finetune::tags.create') }}</a>
        </div>
    </div>
@stop

@section('breadcrumb')
    @include('finetune::partials.breadcrumbs', ['array' => ['admin'=> trans('finetune::main.admin'), 'content'=>  trans('finetune::tags.title')]])
@stop