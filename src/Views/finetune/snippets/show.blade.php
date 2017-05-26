@extends('finetune::layouts.admin')

@section('manage')
    <i v-if="loading" class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>
    <div class="content" v-cloak>
        <input type="hidden" id="groupInput" value="{{ $group->id }}" />
        <div class="table-responsive" v-if="snippets.length != 0">
            <table class="table table-striped">
                <thead>
                <tr class="active">
                    <th class="th-select">{{ trans('finetune::snippets.list.select') }}</th>
                    <th class="th-publish">{{ trans('finetune::snippets.list.publish') }}</th>
                    <th class="th-title">{{ trans('finetune::snippets.list.title') }}</th>
                </tr>
                </thead>
                <tbody id="sortable"  v-sortable="{onUpdate:orderUpdate }">
                <tr v-for="item in snippets" class="tr-@{{ item.tag }}"  :class="renderSelectedRow(item)">
                @include('finetune::partials.select')
                    <td v-if="item.publish == 1" ><i class="fa fa-check" @click="togglePublish(item)"></i></td>
                    <td v-else><i class="fa fa-ban" @click="togglePublish(item)"></i></td>
                    <td class="title"><a href="/admin/snippets/{{ $group->id }}/snippet/@{{ item['id'] }}/edit">@{{ item.title }}</a></td>
                </tr>
                </tbody>
            </table>
            @include('finetune::partials.destroyModal')
        </div>
        <div v-else>
            <div class="no-content">
                <p class="text-lg-center">{{ trans('finetune::snippets.empty') }}</p>
                <a href="/admin/snippets/{{ $group->id }}/snippet/create" class="btn btn-success btn-block" >{{ trans('finetune::snippets.create') }}</a>
            </div>
        </div>
    </div>
@stop

@section('controls')
    <div class="actions">
        <div class="btn-group">
            <a href="#" class="btn" @click="destroy">{{ trans('finetune::snippets.delete') }}</a>
            <a href="#" class="btn" @click="orderSave">{{ trans('finetune::snippets.order') }}</a>
            <a href="/admin/snippets/{{ $group->id }}/snippet/create" class="btn btn-success" >{{ trans('finetune::snippets.create') }}</a>
        </div>
    </div>
@stop

@section('breadcrumb')
    @include('finetune::partials.breadcrumbs', ['array' => ['admin'=>'admin', 'admin/snippets'=>'snippets groups', 'content' => 'Snippets']])
@stop