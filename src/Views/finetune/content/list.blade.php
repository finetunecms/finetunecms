@extends('finetune::layouts.admin')

@section('manage')
    <div id="content-list">
        <i v-if="loading" class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>
        <div class="content" v-cloak v-else>
            @include('finetune::content.mover')
            <div class="input-group search">
                <input type="text" class="form-control" placeholder="Search Site..." v-model="searchTerm" @keyup.enter="searchNodes">
                <span class="input-group-btn">
                    <button class="btn btn-secondary" type="button" @click="searchNodes"><i class="fa fa-search"></i></button>
                  </span>
            </div>
            <div class="table-responsive" v-if="nodes.length != 0">
                <table class="table table-striped">
                    <thead>
                    <tr class="active">
                        <th class="th-select">{{ trans('finetune::content.list.select') }}</th>
                        <th class="th-publish">{{ trans('finetune::content.list.publish') }}</th>
                        <th class="th-title">{{ trans('finetune::content.list.title') }}</th>
                        <th class="th-type">{{ trans('finetune::content.list.type') }}</th>
                        <th class="th-children">{{ trans('finetune::content.list.children') }}</th>
                        <th class="th-actions">{{ trans('finetune::content.list.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody id="sortable"  v-sortable="{onUpdate:orderUpdate}">
                    <tr v-for="item in nodes" class="tr-@{{ item['tag'] }}" :class="renderSelectedRow(item)">
                        @include('finetune::partials.select')
                        <td v-if="item.publish == 1" ><i class="fa fa-check" @click="togglePublish(item)"></i></td>
                        <td v-else><i class="fa fa-ban" @click="togglePublish(item)"></i></td>
                        <td class="title" v-if="item.canEdit"><a title="Edit @{{ item['title'] }}" href="/admin/content/@{{ item['id'] }}/edit">@{{ item['title'] }}</a><span v-if="item.homepage == 1">&#42;</span></td>
                        <td class="title" v-else>@{{ item['title'] }}<span v-if="item.homepage == 1">&#42;</span></td>
                        <td class="type"><span class="tag tag-default">@{{ item['type']['title'] }}</span></td>
                        <td>
                            <span v-if="item.children.length != 0" class="tag tag-default">@{{ item['children'].length }}</span>
                        </td>
                        <td class="actions">
                            <a title="{{ trans('finetune::content.attributes.manage') }}" href="/admin/content/@{{ item['id'] }}" class="btn btn-sm btn-plain" v-if="canShowManage(item)">{{ trans('finetune::content.manage') }}</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <span>&#42; <span class="small">{{ trans('finetune::content.list.asterisk') }}</span></p>
                @include('finetune::partials.destroyModal')
            </div>
            <div v-else>
                <div class="no-content">
                    <p class="text-lg-center">{{ trans('finetune::content.empty') }}</p>
                    <a title="{{ trans('finetune::content.attributes.create') }}" href="/admin/content/create" class="btn btn-success btn-block">{{ trans('finetune::content.create') }}</a>
                </div>
            </div>
        </div>
    </div>

@stop

@section('controls')
    <div class="actions">
        <div class="btn-group">
            <a title="{{ trans('finetune::content.attributes.delete') }}" href="#" class="btn" @click="destroy" v-if="destroyBtn">{{ trans('finetune::content.delete') }}</a>
            <a title="{{ trans('finetune::content.attributes.move') }}" href="#" class="btn" @click="move" v-if="canMove">{{ trans('finetune::content.move') }}</a>
            <a title="{{ trans('finetune::content.attributes.save') }}" href="#" class="btn" @click="orderSave" v-if="canOrder">{{ trans('finetune::content.save') }}</a>
            <a title="{{ trans('finetune::content.attributes.create') }}" href="/admin/content/create" class="btn btn-success">{{ trans('finetune::content.create') }}</a>
        </div>
    </div>
@stop

@section('breadcrumb')
    @include('finetune::partials.breadcrumbs', ['array' => ['admin' => trans('finetune::main.admin'), 'content' => trans('finetune::content.breadcrumb.content')]])
@stop