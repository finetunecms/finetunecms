@extends('finetune::layouts.admin')

@section('manage')
    <i v-if="loading" class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>
    <div id="fields-list" class="content" v-cloak>
        <input type="hidden" id="typeInput" value="{{ $type->id }}" />
        <div class="table-responsive" v-if="fields.length != 0">
            <table class="table table-striped">
                <thead>
                <tr class="active">
                    <th class="th-select">Select</th>
                    <th>Label / Title</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Values</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in fields" class="tr-@{{ item.id }}" :class="renderSelectedRow(item)">
                @include('finetune::partials.select')
                    <td><a href="#" @click="edit(item)">@{{ item.label }}</a></td>
                    <td>@{{ item.name }}</td>
                    <td>@{{ item.type }}</td>
                    <td>@{{ item.values }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <div class="no-content">
                <p class="text-lg-center">{{ trans('finetune::content.empty') }}</p>
                <a href="#" class="btn btn-success btn-block" @click="create()">{{ trans('finetune::type.fields.create') }}</a>
            </div>
        </div>

        @include('finetune::type.customFieldModal')

        @include('finetune::partials.destroyModal')
    </div>
@stop

@section('controls')
    <div class="actions">
        <div class="btn-group">
            <a href="#" class="btn" @click='destroy'>{{ trans('finetune::type.fields.delete') }}</a>
            <a href="#" class="btn btn-success" @click="create()">{{ trans('finetune::type.fields.create') }}</a>
        </div>
    </div>
@stop

@section('breadcrumb')
    @include('finetune::partials.breadcrumbs', ['array' => ['admin'=>'Admin', 'admin/types'=>'Types', 'admin/types/' => $type->title, 'content' => 'Fields']])
@stop