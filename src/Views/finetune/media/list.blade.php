@extends('finetune::layouts.admin')

@section('manage')
    <i v-if="loading" class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>
    <div class="content media-area" v-cloak>
        <div class="media-table">
            <div v-show="uploader">
                <alert :show="errorsShow" state="danger" dismissible>
                    <li v-for="(index, error) in errors">
                        <div v-for="(index, value) in error">
                            <p>@{{ value }}</p>
                        </div>
                    </li>
                </alert>
            </div>
            <div class="images" v-if="media.length > 0">
                <table class="table table-striped">
                    <thead>
                    <tr class="active">
                        <th></th>
                        <th></th>
                        <th class="th-title">{{ trans('finetune::media.list.title') }}</th>
                        <th class="th-uploaded">{{ trans('finetune::media.list.uploaded') }}</th>
                    </tr>
                    </thead>
                    <tbody id="sortable" v-sortable="{onUpdate:orderUpdate}">

                    <tr v-for="file in files" :class="file.active">
                        <td>
                            <a class="btn btn-icon " @click="remove(file)"><i class="fa fa-trash"></i></a>
                        </td>
                        <td>
                            <img v-bind:src="renderImage(file.id, this.upload._files[file.id].file)" :id="file.id"
                                 height="50px"/>
                        </td>
                        <td class="title">
                            @{{file.name}}
                        </td>
                        <td>
                            <p v-if="file.errno.length > 0" class="error">
                                        <span v-if="file.errno == 'size'">
                                            {{ trans('finetune::media.errors.size') }}
                                        </span>
                                <span v-if="file.errno == 'extension'">
                                           {{ trans('finetune::media.errors.extension') }}
                                        </span>
                                <span v-if="file.errno == 'not_support'">
                                            {{ trans('finetune::media.errors.extension') }}
                                        </span>
                                <span v-if="file.errno == 'network'">
                                            {{ trans('finetune::media.errors.communicating') }}
                                        </span>
                                <span v-if="file.errno == 'server'">
                                              {{ trans('finetune::media.errors.communicating') }}
                                        </span>
                                <span v-if="file.errno == 'denied'">
                                              {{ trans('finetune::media.errors.communicating') }}
                                        </span>
                                <span v-if="file.errno == 'abort'">
                                              {{ trans('finetune::media.errors.abort') }}
                                        </span>
                            </p>
                            <vs-progress
                                    variant="success"
                                    :animated="true"
                                    :value="parseInt(file.progress)"
                                    striped
                                    v-else>
                                @{{file.progress}}
                            </vs-progress>
                        </td>

                    </tr>


                    <tr v-for="item in search()" @click="selectMedia(item)":class="renderSelectedRow(item)">
                    <td class="select" @click="selectRow(item)">
                    <tooltip
                            text="{{ trans('finetune::content.tooltip.select') }}"
                            position="top"
                            :triggers="['hover']">
                        <i :class="renderSelected(item)" @click="selectRow(item)"></i>
                    </tooltip>
                    </td>
                    <td v-if="item.type == 'image'">
                        <img v-lazy="item.thumb"
                             :srcset="'{{ config('finetune.protocol') }}{{ $site->domain }}'+ item.thumb"
                             style="cursor: pointer;height: 50px;"/>
                    </td>
                    <td v-if="item.type == 'file'">
                        <i class="fa fa-file fa-3x"></i>
                    </td>
                    <td class="title" v-if="item.title.length > 0">@{{ item.title | truncate '20' }}</td>
                    <td class="title" v-else>@{{ item.filename | truncate '20'}}</td>
                    <td>@{{ parseHuman(item.created_at) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div v-else>
                <h3>{{ trans('finetune::media.empty') }}</h3>
                <table class="table table-striped" v-if="files.length > 0">
                    <thead>
                    <tr class="active">
                        <th></th>
                        <th></th>
                        <th class="th-title">{{ trans('finetune::media.list.title') }}</th>
                        <th class="th-uploaded">{{ trans('finetune::media.list.uploaded') }}</th>
                    </tr>
                    </thead>
                    <tbody id="sortable" v-sortable="{onUpdate:orderUpdate}">

                    <tr v-for="file in files" :class="file.active">
                        <td>
                            <a class="btn btn-icon " @click="remove(file)"><i class="fa fa-trash"></i></a>
                        </td>
                        <td>
                            <img v-bind:src="renderImage(file.id, this.upload._files[file.id].file)" :id="file.id"
                                 height="50px"/>
                        </td>
                        <td class="title">
                            @{{file.name}}
                        </td>
                        <td>
                            <p v-if="file.errno.length > 0" class="error">
                                        <span v-if="file.errno == 'size'">
                                            {{ trans('finetune::media.errors.size') }}
                                        </span>
                                <span v-if="file.errno == 'extension'">
                                           {{ trans('finetune::media.errors.extension') }}
                                        </span>
                                <span v-if="file.errno == 'not_support'">
                                            {{ trans('finetune::media.errors.extension') }}
                                        </span>
                                <span v-if="file.errno == 'network'">
                                            {{ trans('finetune::media.errors.communicating') }}
                                        </span>
                                <span v-if="file.errno == 'server'">
                                              {{ trans('finetune::media.errors.communicating') }}
                                        </span>
                                <span v-if="file.errno == 'denied'">
                                              {{ trans('finetune::media.errors.communicating') }}
                                        </span>
                                <span v-if="file.errno == 'abort'">
                                              {{ trans('finetune::media.errors.abort') }}
                                        </span>
                            </p>
                            <vs-progress
                                    variant="success"
                                    :animated="true"
                                    :value="parseInt(file.progress)"
                                    striped
                                    v-else>
                                @{{file.progress}}
                            </vs-progress>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="media-attributes">
            <div v-show="mediaItem != ''" class="fixed">
                <tabs size="md" :fade="false">
                    <tab id="update" title="{{ trans('finetune::media.update') }}" class="tab-update">
                        <div class="tabbed-content">
                            <div class="col-md-12 attribute-item">
                                <div class="img-wrap" v-if="mediaItem.type == 'image'">
                                    <img :src="'{{ config('finetune.protocol') }}{{ $site->domain }}' + mediaItem.external + '/200x200?fit=true&bg=ffffff'" v-if="mediaItem.type == 'image'"/>
                                    <a class="btn btn-primary btn-block edit-image" href="/admin/media/@{{ mediaItem.id }}/edit" v-if="mediaItem.type == 'image' ">{{ trans('finetune::media.crop') }}</a>
                                </div>

                            </div>
                            <div class="form-group">
                                <label for='folder'>{{ trans('finetune::media.label.groups') }}</label>
                                <v-select :on-change="groupChange" :value.sync="mediaItem.folders" label="title"
                                          :multiple="true"
                                          :options="folders"
                                          placeholder="{{ trans('finetune::media.placeholder.groups') }}"></v-select>

                            </div>
                            @include('finetune::partials.fields',[
                                'name' => 'title',
                                'required' => true,
                                'type' => 'text',
                                'placeholder' => trans('finetune::media.placeholder.title'),
                                'vmodel' => 'mediaItem.title',
                                'vvalidate' => 'alpha_dash',
                                'title' =>  trans('finetune::media.label.title')])
                            @include('finetune::partials.fields',[
                                 'name' => 'filename',
                                 'required' => true,
                                 'type' => 'text',
                                 'placeholder' => trans('finetune::media.placeholder.filename'),
                                 'vmodel' => 'mediaItem.filename',
                                 'vvalidate' => 'required',
                                 'title' => trans('finetune::media.label.filename')])
                            <button type="button" class="btn btn-success btn-block" @click='saveMedia()'>{{ trans('finetune::media.save') }}</button>
                        </div>

                    </tab>
                    <tab id="nodes" title="{{ trans('finetune::media.usage') }}" class="tab-update">
                        <div class="tabbed-content">
                            <div class="nodes" v-if="mediaItem.nodes.length > 0">
                                <ul class="nav nav-stacked">
                                    <li class="nav-item" v-for="node in mediaItem.nodes">
                                        <a class="nav-link"
                                           href="/admin/content/@{{ node.id }}/edit">@{{ node.title }}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="nodes" v-else>
                                <p>{{ trans('finetune::media.noUsage') }}</p>
                            </div>
                        </div>
                    </tab>
                </tabs>
            </div>
        </div>
        @include('finetune::media.updateFolder')
        @include('finetune::media.moveFiles')
        @include('finetune::partials.destroyModal')
    </div>
@endsection

@section('controls')
    <div class="actions ">
        <div class="btn-group">

            <a href="#" @click="moveMedia()" class="btn"> {{ trans('finetune::media.move') }}</a>
            <a href="#" @click="destroy()" class="btn">{{ trans('finetune::media.delete') }}</a>
            <a href="#" @click="addFolder()" class="btn">{{ trans('finetune::media.add') }}</a>
            <a title="{{ trans('finetune::content.attributes.save') }}" href="#" class="btn" @click="orderSave()" v-if="
            folderId != ''">{{ trans('finetune::media.saveOrder') }}</a>
            <file-upload
                    title="<div class='btn btn-success'>{{ trans('finetune::media.upload') }}</strong>"
                    class="file-upload"
                    name="file"
                    post-action="/admin/api/media"
                    :extensions="extensions"
                    :accept="accept"
                    :multiple="multiple"
                    :size="size"
                    v-ref:upload
                    :drop="drop">
            </file-upload>
        </div>
    </div>
    <div class="media-header" v-cloak>
        <div class="media-groups">
            <v-select :on-change="changeFolder" :value.sync="folder" label="title"
                      :multiple="false"
                      :options="getSelectFolders()"
                      placeholder="{{ trans('finetune::media.placeholder.groups') }}"></v-select>
        </div>

        <button v-bind:class="['btn','btn-primary']" @click="addFolder('edit')" v-if="folderId != ''"
        >{{ trans('finetune::media.groups.edit') }}</button>

        <div class="media-filter">
            <dropdown
                    :text="filterLabel"
                    variant="default"
                    :arrow="false"
                    :caret="true">
                <ul class="dropdown-menu dropdown-menu-left">
                    <li><a class="dropdown-item" href="#" @click='filterLoad({"filterlabel":"<?php echo trans("media.filter.all") ?>","filtertag":"All" })'
                        >{{ trans('finetune::media.filter.all') }}</a></li>
                    <li><a class="dropdown-item" href="#" @click='filterLoad({"filterlabel":"<?php echo trans("media.filter.images") ?>","filtertag":"Images" })'
                        >{{ trans('finetune::media.filter.images') }}</a></li>
                    <li><a class="dropdown-item" href="#" @click='filterLoad({"filterlabel":"<?php echo trans('finetune::media.filter.files') ?>","filtertag":"Files" })'
                        >{{ trans('finetune::media.filter.files') }}</a></li>
                </ul>
            </dropdown>
        </div>

        <div class="media-search">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="{{ trans('finetune::media.placeholder.search') }}"
                       v-model="searchTerm"
                       @keyup.enter="search"/>
                <span class="input-group-addon search-icon"><i class="fa fa-search"></i></span>
            </div>
        </div>

    </div>

@stop

@section('breadcrumb')
    <div v-cloak>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a title="Link to {{ trans('finetune::media.breadcrumb.admin') }}"
                                           href="/admin">{{ trans('finetune::media.breadcrumb.admin') }}</a></li>
            <li class="breadcrumb-item"><a title="Link to {{ trans('finetune::media.breadcrumb.media') }}"
                                           href="/admin/media">{{ trans('finetune::media.breadcrumb.media') }}</a></li>
            <li class="breadcrumb-item active" v-if="folderId != ''">@{{ folder.title }}</li>
            <li class="breadcrumb-item active" v-else>{{ trans('finetune::media.breadcrumb.main') }}</li>
        </ol>
    </div>
@stop