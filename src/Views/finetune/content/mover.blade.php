<div class="card quick-action" v-if="moveAction">
    <h6 class="card-header">
        Choose a Parent Node
    </h6>
    <div class="card-block">
        <div class="row">
            <div class="col-md-9">
                @include('finetune::partials.formvalidation')
                <v-select :value.sync="moveParent" :options="filterMoveNodes()" :on-change="moveChange" label="title"></v-select>
            </div>
            <div class="col-md-3">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" @click="closeMoveActon()">Close</button>
                    <button type="button" class="btn btn-success" @click='sendNodes()'>Save</button>
                </div>
            </div>
        </div>

    </div>

</div>