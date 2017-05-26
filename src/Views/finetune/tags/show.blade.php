@extends('finetune::layouts.admin')

@section('manage')
    <i v-if="loading" class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>

    <div class="content" v-cloak>
        <input type="hidden" id="tagInput" value="{{ $tag->id }}" />
        <div class="table-responsive" v-if="nodes.length != 0">
            <table class="table table-striped">
                <thead>
                <tr class="active">
                    <th class="th-select">{{ trans('finetune::tags.list.select') }}</th>
                    <th class="th-title">{{ trans('finetune::tags.list.title') }}</th>
                    <th class="th-actions">{{ trans('finetune::tags.list.actions') }}</th>
                </tr>
                </thead>
                <tbody id="sortable">
                <tr v-for="item in nodes" class="tr-@{{ item.tag }}" :class="renderSelectedRow(item)">
                @include('finetune::partials.select')
                    <td class="title"><a href="/admin/content/@{{ item['id'] }}/edit">@{{ item.title }}</a></td>
                    <td class="actions">

                    </td>
                </tr>
                </tbody>
            </table>

            @include('finetune::partials.destroyModal')

        </div>

        <div v-else>
            <div class="no-content">
                <p class="text-lg-center">{{ trans('finetune::tags.tagged.empty') }}</p>
            </div>
        </div>
    </div>
@stop

@section('controls')
    <div class="actions"  v-if="nodes.length != 0" v-cloak>
        <div class="btn-group">
            <a href="#" class="btn " @click='destroy'>{{ trans('finetune::tags.tagged.delete') }}</a>
        </div>
    </div>
@stop

@section('breadcrumb')
    @include('finetune::partials.breadcrumbs', ['array' => ['admin'=> trans('finetune::main.admin'), 'admin/tags' => trans('finetune::tags.title'), 'content' => trans('finetune::tags.nodes')]])
@stop