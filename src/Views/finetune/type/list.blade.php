@extends('finetune::layouts.admin')

@section('manage')
    <i v-if="loading" class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>
    <div id="type-list" class="content" v-cloak>

        <div class="table-responsive" v-if="types.length != 0">

            <table class="table table-striped">
                <thead>
                <tr class="active">
                    <th class="th-select">Select</th>
                    <th class="th-title">Title</th>
                    <th class=""></th>
                    <th class="th-actions">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in types" class="tr-@{{ item.id }}" :class="renderSelectedRow(item)">
                @include('finetune::partials.select')
                    <td class="title"><a href="#" @click="edit(item)">@{{ item.title }}</a></td>
                    <td>
                        <span class="tag tag-default">@{{ item.outputs }}</span>
                        <span v-if="item.date != 0" class="tag tag-default">date</span>
                        <span v-if="item.nesting != 0" class="tag tag-default">nesting</span>
                    </td>
                    <td class="actions">
                        <a href="/admin/types/@{{ item['id'] }}" class="btn btn-sm btn-plain">Custom Fields</a>
                    </td>
                </tr>
                </tbody>
            </table>

            @include('finetune::partials.destroyModal')
            @include('finetune::type.typeUpdateModal')

        </div>

        <div v-else>
            <div class="no-content">
                <p class="text-lg-center">{{ trans('finetune::content.empty') }}</p>
                <a href="#" class="btn btn-success btn-block" @click="create()">{{ trans('finetune::type.create') }}</a>
            </div>
        </div>

    </div>
@stop

@section('controls')
    <div class="actions">
        <div class="btn-group">
            <a href="#" class="btn" @click='destroy'>{{ trans('finetune::type.delete') }}</a>
            <a href="#" class="btn btn-success" @click="create()">{{ trans('finetune::type.create') }}</a>
        </div>
    </div>
@stop

@section('breadcrumb')
    @include('finetune::partials.breadcrumbs', ['array' => ['admin'=>'admin', 'content'=>'types']])
@stop