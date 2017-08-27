@extends('finetune::layouts.admin')

@section('manage')
    <alert
            :show="errorsShow"
            state="danger"
            dismissible v-cloak>
        <ul class="alert-list">
            <li v-for="(index, error) in errors">
                <div v-for="(index, value) in error">
                    @{{ value }}
                </div>
            </li>
        </ul>
    </alert>
    <i v-if="loading" class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>
    <div id="content-update" class="content-tabbed" v-cloak>
        <tabs size="md" :fade="false" v-show="!previewActive">
            <tab id="content" title="{{ trans('finetune::content.tabs.content') }}" class="tab-content">
                <div class="row" v-if="node.type.length == 0">
                    <div class="col-md-9">
                        <div class="form-group">
                            <h3>{{ trans('finetune::content.type.helper') }}</h3>
                            <label class="control-label">{{ trans('finetune::content.type.title') }}</label>
                            <v-select :on-change="typeChange" :value.sync="node.type" label="title"
                                      :options="types"></v-select>
                        </div>
                    </div>
                </div>

                <div class="block">
                    <div class="row">
                        <div class="col-md-9">
                            <h4>{{ trans('finetune::content.block.main') }}</h4>
                            @include('finetune::partials.fields',[
                            'name' => 'body-title',
                            'value' => isset($node->title) ? $node->title : '',
                            'required' => false,
                            'type' => 'text',
                            'vmodel' => 'node.title',
                            'popover' => trans('finetune::content.popover.title'),
                            'popoverPos' => 'top',
                            'vvalidate' => 'required',
                            'placeholder' => trans('finetune::content.block.placeholder'),
                            'title' => trans('finetune::content.block.title')])

                            <textarea
                                    class="form-control"
                                    id="body-content"
                                    ided="body-content"
                                    rows="10"
                                    placeholder="body-content"
                                    v-tinymce="node.body">
                            </textarea>
                        </div>
                        <div class="col-md-3">

                            <h4>{{ trans('finetune::content.options.title') }}</h4>

                            <label>{{ trans('finetune::content.options.image') }}
                                <popover title="{{ trans('finetune::content.popover.imageTitle') }}" position="left"
                                         :triggers="['hover']"
                                         text="{{ trans('finetune::content.popover.imageDesc') }}">
                                    <a class="btn btn-plain btn-sm btn-info">?</a>
                                </popover>
                            </label>

                            <input type="hidden" name="body-image" v-model="node.media.id"/>
                            <img :src="node.media.external" class="full-width" v-if="node.media.id != 0">
                            <div class="content-image-btns">
                                <a href="#" class="btn btn-success btn-block" @click='changeImage("body")' v-if="node.media.id == 0">{{ trans('finetune::content.image.add') }}</a>
                                <a href="#" class="btn btn-success" @click='changeImage("body")' v-else>{{ trans('finetune::content.image.change') }}</a>
                                <a href="#" class="btn btn-danger hidden" @click='removeImage("body")' v-if="node.media.id != 0">{{ trans('finetune::content.image.remove') }}</a>
                            </div>


                            @if(isset($node->user))
                                <p>{{ trans('finetune::content.options.author') }}:
                                    <strong>{{ $node->user->username }}</strong></p>
                            @endif

                            <div v-if="date" id="date">
                                <fieldset class="form-group">
                                    <label>{{ trans('finetune::content.options.date') }}
                                        <popover title="{{ trans('finetune::content.popover.dateTitle') }}"
                                                 :triggers="['hover']"
                                                 text="{{ trans('finetune::content.popover.dateDesc') }}" position="left">
                                            <a class="btn btn-plain btn-sm btn-info">?</a>
                                        </popover>
                                    </label>
                                    <date-picker :time.sync="starttime" :option="option" :limit="limit"></date-picker>
                                    <input type="hidden" name="due_date"
                                           value="{{ isset($node->publish_on) ? \Carbon\Carbon::parse($node->publish_on)->format('d-m-Y') : old('publish_on') }}"
                                           v-model="startTime"/>
                                </fieldset>
                            </div>

                            <div v-if="hasTags">
                                <label class="control-label">{{ trans('finetune::content.options.tags') }}
                                    <popover title="{{ trans('finetune::content.popover.tagTitle') }}" :triggers="['hover']"
                                             text="{{ trans('finetune::content.popover.tagDesc') }}" position="left">
                                        <a class="btn btn-plain btn-sm btn-info">?</a>
                                    </popover>
                                </label>

                                <v-select :on-change="tagsChange" :value.sync="node.tags" label="title" :multiple="true"
                                          :options="tags"
                                          placeholder="{{ trans('finetune::content.placeholder.tags') }}"></v-select>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-for="(index, block) in blocks" class="block @{{ block.name }}-block">
                    <div class="row">
                        <div class="col-md-9">
                            <h4>@{{ block.name | capitalize }}</h4>
                            <div class="form-group">
                                <label class="control-label"
                                       for="@{{ block.name }}-title">{{ trans('finetune::content.block.title') }}
                                    <popover title="{{ trans('finetune::content.popover.blockTitle') }}" :triggers="['hover']"
                                             text="{{ trans('finetune::content.popover.blockDesc') }}" position="top">
                                        <a class="btn btn-plain btn-sm btn-info">?</a>
                                    </popover>
                                </label>
                                <input type="text" class="form-control " id="@{{ block.name }}-title"
                                       name="@{{ block.name }}-title"
                                       v-model="blocks[index].title"
                                       aria-describedby="input@{{ block.name }}-titleStatus"
                                       placeholder="{{ trans('finetune::content.block.title') }}">
                                <span id="input@{{ block.name }}-titleStatus" class="sr-only"> (success) </span>
                            </div>
                            <div v-if="block.name.length > 0">
                                      <textarea
                                              class="form-control"
                                              rows="10"
                                              :id="block.name"
                                              v-bind:ided="block.name"
                                              placeholder="body-content"
                                              v-tinymce="block.content">
                                     </textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h4>{{ trans('finetune::content.options.title') }}</h4>
                            <label class="control-label"
                                   for="@{{ block.name }}-title">{{ trans('finetune::content.options.imageBlock') }}
                                <popover title="{{ trans('finetune::content.popover.blockOptionsTitle') }}"
                                         :triggers="['hover']"
                                         text="{{ trans('finetune::content.popover.blockOptionsDesc') }}" position="left">
                                    <a class="btn btn-plain btn-sm btn-info">?</a>
                                </popover>
                            </label>
                            <input type="hidden" name="@{{ block.name }}-image" v-model="block.media.id"/>
                            <img :src="block.media.external" class="full-width" v-if="block.media.id != 0">
                            <div class="btn-group">
                                <a href="#" class="btn btn-success" @click='changeImage(block.name)' v-if="
                                block.media.id == 0">{{ trans('finetune::content.image.add') }}</a>
                                <a href="#" class="btn btn-success" @click='changeImage(block.name)' v-else
                                >{{ trans('finetune::content.image.change') }}</a>
                                <a href="#" class="btn btn-danger hidden" @click='removeImage(block.name)' v-if="
                                block.media.id != 0">{{ trans('finetune::content.image.remove') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </tab>

            <tab id="custom" title="{{ trans('finetune::content.tabs.custom') }}">
                <h4>{{ trans('finetune::content.customFields') }}</h4>
                <div v-show="customFields.length > 0">
                    <div v-for="(index, field) of customFields">
                        <div class="field-group custom-field" v-if="field.type != 'select'">
                            <div v-if="field.type == 'icons'">
                                <div class="icons">
                                    <h3>Icons</h3>

                                    <div class="{ {$index % 12 ? '' : 'row'} }" v-for="(icon,index) in field.icons">
                                        <div class="col-md-2" style="height:60px">
                                            <label :for="icon">
                                                <i class="fa @{{ icon }}"></i>@{{ icon }}
                                                <input type="radio" name="icon" v-model="field.value" :id="icon"
                                                       :value="icon" class="form-control"/>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <label for="field[@{{ field.name }}]" class="control-label">@{{ field.label }}</label>

                                <input class="form-control" type="@{{ field.type }}" placeholder="@{{ field.name }}"
                                       name="field[@{{ field.name }}]"
                                       id="@{{ field.name }}-custom-field"
                                       v-model="field.value"/>
                            </div>
                        </div>
                        <div class="field-group custom-field" v-if="field.type == 'select'">
                            <label for="field[@{{ field.name }}]" class="control-label">@{{ field.label }}</label>
                            <v-select v-if="field.multiple == 0" :value.sync="field.value"
                                      :options="splitter(field.values)" label="label">
                            </v-select>
                            <v-select v-if="field.multiple == 1" :value.sync="field.value"
                                      :options="splitter(field.values)" label="label" :multiple="true">
                            </v-select>
                        </div>
                    </div>
                </div>
            </tab>

            <tab id="orphan" title="{{ trans('finetune::content.tabs.orphan') }}" v-if="hasOrphans">
                <div v-for="(index, block) in orphanBlock" class="block @{{ block.name }}-block">
                    <div class="row">
                        <div class="col-md-9">
                            <h4>@{{ block.name | capitalize }}</h4>
                            <div class="form-group">
                                <label class="control-label"
                                       for="@{{ block.name }}-title">{{ trans('finetune::content.block.title') }}
                                    <popover title="{{ trans('finetune::content.popover.blockTitle') }}" position="top"
                                             :triggers="['hover']"
                                             text="{{ trans('finetune::content.popover.blockDesc') }}">
                                        <a class="btn btn-plain btn-sm btn-info">?</a>
                                    </popover>
                                </label>
                                <input type="text" class="form-control " id="@{{ block.name }}-title"
                                       name="@{{ block.name }}-title"
                                       v-model="orphanBlock[index].title"
                                       aria-describedby="input@{{ block.name }}-titleStatus"
                                       placeholder="{{ trans('finetune::content.block.title') }}">
                                <span id="input@{{ block.name }}-titleStatus" class="sr-only"> (success) </span>
                            </div>
                            <div v-if="block.name.length > 0">
                                      <textarea
                                              class="form-control"
                                              rows="10"
                                              :id="block.name"
                                              v-bind:ided="block.name"
                                              placeholder="body-content"
                                              v-tinymce="block.content">
                                     </textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h4>{{ trans('finetune::content.options.title') }}</h4>
                            <div v-show="block.media.length > 0">
                                <label class="control-label"
                                       for="@{{ block.name }}-title">{{ trans('finetune::content.options.image') }}
                                    <popover title="{{ trans('finetune::content.popover.blockOptionsTitle') }}"
                                             position="left"
                                             :triggers="['hover']"
                                             text="{{ trans('finetune::content.popover.blockOptionsDesc') }}">
                                        <a class="btn btn-plain btn-sm btn-info">?</a>
                                    </popover>
                                </label>
                                <input type="hidden" name="@{{ block.name }}-image" v-model="block.media.id"/>
                                <img :src="block.media.external" class="full-width" v-if="block.media != null">
                            </div>
                            <div class="column-full mt-1">
                                <label class="control-label mb-1">
                                    <popover title="{{ trans('finetune::content.popover.mergeTitle') }}" position="left"
                                             :triggers="['hover']"
                                             text="{{ trans('finetune::content.popover.mergeDesc') }}">
                                        <a class="btn btn-plain btn-sm btn-info">?</a>
                                    </popover>
                                    {{ trans('finetune::content.options.mergeTitle') }}
                                </label>
                                <v-select :on-change="mergeChange" label="name" :options="blocks"
                                          placeholder="{{ trans('finetune::content.placeholder.merge') }}"></v-select>
                                <a href="#" class="btn btn-success btn-block mt-1" @click='mergeBlock(block)'
                                ><i class="fa fa-random"
                                    aria-hidden="true"></i>{{ trans('finetune::content.mergeBlock') }}</a>

                                <a href="#" class="btn btn-danger btn-block mt-1" @click="destroyOrphan(block)"><i
                                        class="fa fa-trash"></i> Delete Orphan</a>
                            </div>

                        </div>
                    </div>
                </div>
            </tab>

            <tab :id="package.name" :title="package.name" v-for="package in packages">
                <div v-for="field in package.fields">
                    <div class="form-group" v-if="field.type != 'checkbox'">
                        <label class="control-label" :for="field.name">@{{ field.name }}
                            <popover :title="field.name" :triggers="['hover']" :text="field.name" position="top">
                                <a class="btn btn-plain btn-sm btn-info">?</a>
                            </popover>
                        </label>
                        {{--  [{ value: 4, label: 'Four'},{ value: 5, label: 'Five'}] --}}

                        <v-select v-if="['select','multiple'].indexOf(field.type) != -1" :value.sync="field.value" label="title" :multiple="(['multiple'].indexOf(field.type) != -1)"
                                  :options="field.values" :placeholder="field.name"></v-select>

                        <input v-if="['text','number','password'].indexOf(field.type) != -1" type="@{{ field.type }}" v-model="field.value" :id="field.name" class="form-control" />
                    </div>
                    <div class="checkbox" v-else>
                        <input v-if="field.type == 'checkbox'" type="checkbox" v-model="field.value" type="checkbox" :id="field.name"/>
                        <label class="control-label" :for="field.name">@{{ field.name }}</label>
                        <popover :title="field.name" :triggers="['hover']" :text="field.name" position="top">
                            <a class="btn btn-plain btn-sm btn-info">?</a>
                        </popover>
                    </div>
                </div>
            </tab>

            <tab id="seo" title="{{ trans('finetune::content.tabs.seo') }}">
                <h4>{{ trans('finetune::content.metaSet') }}</h4>

                @include('finetune::partials.fields',[
                'name' => 'meta_title',
                'value' => (isset($node->meta_title) ? $node->meta_title : ''),
                'required' => true,
                'type' => 'text',
                'vmodel' => 'node.meta_title',
                'popover' => trans('finetune::content.popover.meta'),
                'popoverPos' => 'left',
                'placeholder' => trans('finetune::content.placeholder.meta'),
                'title' => trans('finetune::content.form.meta')])

                @include('finetune::partials.fields',[
                'name' => 'keywords',
                'value' => (isset($node->keywords) ? $node->keywords : ''),
                'required' => true,
                'type' => 'text',
                'vmodel' => 'node.keywords',
                'popover' => trans('finetune::content.popover.keywords'),
                'popoverPos' => 'left',
                'placeholder' => trans('finetune::content.placeholder.keywords'),
                'title' => trans('finetune::content.form.keywords')])

                @include('finetune::partials.fields',[
                'name' => 'dscpn',
                'value' => (isset($node->dscpn) ? $node->dscpn : null),
                'required' => true,
                'type' => 'textarea',
                'vmodel' => 'node.dscpn',
                'popover' => trans('finetune::content.popover.dscpn'),
                'popoverPos' => 'left',
                'placeholder' => trans('finetune::content.placeholder.dscpn'),
                'title' => trans('finetune::content.form.dscpn')])

            </tab>

            <tab id="advanced" title="{{ trans('finetune::content.tabs.advanced') }}">
                <h4>{{ trans('finetune::content.pageSet') }}</h4>
                <div class="form-group">
                    <label class="control-label">{{ trans('finetune::content.type.title') }}
                        <popover title="{{ trans('finetune::content.popover.type') }}" :triggers="['hover']"
                                 position="left"
                                 text="{{ trans('finetune::content.popover.typeDesc') }}">
                            <a class="btn btn-plain btn-sm btn-info">?</a>
                        </popover>
                    </label>
                    <v-select :on-change="typeChange" :value.sync="node.type" label="title"
                              :options="types"></v-select>
                </div>

                @include('finetune::partials.fields',[
                'name' => 'tag',
                'value' => (isset($node->tag) ? $node->tag : ''),
                'required' => true,
                'type' => 'text',
                'vmodel' => 'node.tag',
                'placeholder' => trans('finetune::content.placeholder.tag'),
                'popover' => trans('finetune::content.popover.tag'),
                'popoverPos' => 'left',
                'vvalidate' => 'alpha_dash',
                'title' => trans('finetune::content.form.tag')])

                @include('finetune::partials.fields',[
                'name' => 'redirect',
                'value' => (isset($node->redirect) ? $node->redirect : ''),
                'required' => true,
                'type' => 'text',
                'vmodel' => 'node.redirect',
                'popover' => trans('finetune::content.popover.redirect'),
                'popoverPos' => 'left',
                'placeholder' => trans('finetune::content.placeholder.redirect'),
                'title' => trans('finetune::content.form.redirect')])

                @if(!Entrust::ability(['Superadmin'],['advanced_user']))
                    <div style="display:none">
                        @endif

                        @include('finetune::partials.fields',[
                        'name' => 'homepage',
                        'value' => (isset($node->homepage) ? $node->homepage : ''),
                        'required' => true,
                        'class' => 'checkbox-success',
                        'type' => 'checkbox',
                        'vmodel' => 'node.homepage',
                        'popover' => trans('finetune::content.popover.homepage'),
                        'popoverPos' => 'left',
                        'placeholder' => trans('finetune::content.homepage.placeholder'),
                        'title' => trans('finetune::content.form.homepage')])

                        @include('finetune::partials.fields',[
                        'name' => 'soft_publish',
                        'value' => (isset($node->soft_publish) ? $node->soft_publish : ''),
                        'required' => true,
                        'class' => 'checkbox-success',
                        'type' => 'checkbox',
                        'vmodel' => 'node.soft_publish',
                        'popover' => trans('finetune::content.popover.soft'),
                        'popoverPos' => 'left',
                        'placeholder' => trans('finetune::content.placeholder.soft'),
                        'title' => trans('finetune::content.form.soft')])

                        @include('finetune::partials.fields',[
                        'name' => 'exclude',
                        'value' => (isset($node->exclude) ? $node->exclude : ''),
                        'required' => true,
                        'class' => 'checkbox-success',
                        'type' => 'checkbox',
                        'vmodel' => 'node.exclude',
                        'popover' => trans('finetune::content.popover.exclude'),
                        'popoverPos' => 'left',
                        'placeholder' => trans('finetune::content.placeholder.exclude'),
                        'title' => trans('finetune::content.form.exclude')])

                        @if(!Entrust::ability(['Superadmin'],['advanced_user']))
                    </div>
                @endif
            </tab>


        </tabs>

        @include('finetune::content.imagePopup')
        @include('finetune::content.previewPopup')
        @include('finetune::partials.destroyModal')
    </div>


    @if(!empty($node))
        <input type="hidden" value="{{ $node->id }}" id="nodeIdField"/>
    @else
        @if(isset($parentNode))
            <input type="hidden" value="{{ $parentNode->id }}" id="parentIdField"/>
        @endif

    @endif

    <div class="overlay" v-if="saving" v-cloak>
        <i class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>
    </div>
@stop

@section('controls')
    <div class="actions" v-cloak>
        <div class="btn-group">
            <a href="#" v-on:click="preview()" class="btn">{{ trans('finetune::content.preview') }}</a>
            <button v-on:click="saveContent(0)" class="btn btn-success"
                    :disabled="saving"><span v-if="!saving">{{ trans('finetune::content.saveDraft') }}</span><i v-else class="fa fa-spinner fa-spin"></i></button>
            <button v-on:click="saveContent(1)" class="btn btn-success" :disabled="saving"
                    saving><span v-if="!saving">{{ trans('finetune::content.savePub') }}</span><i v-else class="fa fa-spinner fa-spin"></i></button>
        </div>
    </div>
@stop

@section('breadcrumb')
    @include('finetune::partials.breadcrumbs', ['array' => $breadcrumb])
@stop