@extends('finetune::layouts.admin')

@section('manage')
    <alert :show="errorsShow" state="danger" dismissible v-cloak>
        <li v-for="(index, error) in errors">
            <div v-for="(index, value) in error">
                <p>@{{ value }}</p>
            </div>
        </li>
    </alert>
    <input type="hidden" value="{{ $group->id }}" id="groupIdField"/>
    <i v-if="loading" class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>
    <div id="snippet-update" class="content-tabbed" v-cloak>
        <tabs size="md" :fade="false">
            <tab title="{{ trans('finetune::snippets.tabs.content') }}" class="tab-content">
                <div class="row">
                    <div class="col-md-9">
                        <h4>{{ trans('finetune::snippets.block.main') }}</h4>
                        @include('finetune::partials.fields',[
                        'name' => 'body-title',
                        'value' => isset($snippet->title) ? $snippet->title : '',
                        'required' => false,
                        'type' => 'text',
                        'vmodel' => 'snippet.title',
                        'vvalidate' => 'required',
                        'placeholder' => trans('finetune::snippets.block.placeholder'),
                        'title' => trans('finetune::snippets.block.title')])
                        <textarea
                                class="form-control"
                                id="snippet-content"
                                ided="snippet-content"
                                rows="10"
                                placeholder="snippet-content"
                                v-tinymce="snippet.body">
                        </textarea>
                    </div>
                    <div class="col-md-3">
                        <h4>{{ trans('finetune::snippets.options.title') }}</h4>

                        <input type="hidden" name="snippet-image" v-model="snippet.media.id" v-if="snippet.media != null"/>
                        <input type="hidden" name="snippet-image" value="" v-else/>

                        <img :src="snippet.media.external" class="full-width" v-if="snippet.media != null">
                        <div class="form-group">
                                <div class="content-image-btns">
                                    <a href="#" class="btn btn-success btn-block" @click='changeImage()' v-if="
                                    snippet.media == null">{{ trans('finetune::snippets.image.add') }}</a>
                                    <a href="#" class="btn btn-success" @click='changeImage()' v-else>{{ trans('finetune::snippets.image.change') }}</a>
                                    <a href="#" class="btn btn-danger hidden" @click='removeImage()' v-if="snippet.media != null">{{ trans('finetune::snippets.image.remove') }}</a>
                                </div>
                        </div>
                    </div>
                </div>
            </tab>
            <tab title="{{ trans('finetune::snippets.tabs.link') }}">

                <div class="form-group">
                    <label for="link">{{ trans('finetune::snippets.form.link_type') }}</label>
                    <select class="form-control" v-model="snippet.link_type" id="link" name="link">
                        <option value="0" selected>Please select a link type</option>
                        <option value="1">Internal</option>
                        <option value="2">External</option>
                    </select>
                </div>

                <div class="form-group" v-if="snippet.link_type == 1">
                    <label>{{ trans('finetune::snippets.form.internal_link') }}</label>
                    <v-select :on-change="linkChange" :value.sync="snippet.node" label="title" :options="nodes" placeholder="{{ trans('finetune::snippets.form.internal_link') }}"></v-select>
                </div>

                <div class="form-group" v-if="snippet.link_type == 2">
                    <label for="external_link">{{ trans('finetune::snippets.form.external_link') }}</label>
                    <input id="external_link" type="text" v-model="snippet.link_external" placeholder="external link" class="form-control"/>
                </div>
            </tab>
            <tab title="{{ trans('finetune::snippets.tabs.advanced') }}">
                @include('finetune::partials.fields',[
               'name' => 'tag',
              'value' => isset($snippet->tag) ? $snippet->tag : old('tag'),
               'required' => true,
               'type' => 'text',
               'placeholder' => trans('finetune::snippets.form.tag'),
               'vmodel' => 'snippet.tag',
               'title' => trans('finetune::snippets.form.tag')])
            </tab>
        </tabs>

        @include('finetune::content.imagePopup')
    </div>

    @if(!empty($snippet))
        <input type="hidden" value="{{ $snippet->id }}" id="snippetIdField"/>
    @endif
@stop

@section('controls')
    <div class="actions">
        <div class="btn-group">
            <button @click='saveSnippet(0)' class="btn btn-success":disabled="saving"><span v-if="!saving">{{ trans('finetune::snippets.saveDraft') }}</span><i v-else class="fa fa-spinner fa-spin"></i></button>
            <button @click='saveSnippet(1)' class="btn btn-success":disabled="saving"><span v-if="!saving">{{ trans('finetune::snippets.savePub') }}</span><i v-else class="fa fa-spinner fa-spin"></i></button>
        </div>
    </div>
@stop


@section('breadcrumb')
    @include('finetune::partials.breadcrumbs', ['array' => ['admin'=>'admin', 'admin/snippets' => 'Snippet Groups', 'admin/snippets/'.$group->id => $group->title, 'content'=>'snippet Create And Update']])
@stop