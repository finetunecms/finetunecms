<ul class="nav col-md-3 pull-right">
    <li class="nav-item"><a href="#" title="edit" @click="edit(imageItem)"><i class="fa fa-pencil"></i></a></li>
    <li class="nav-item"><a href="/admin/media/@{{ imageItem.id }}/edit" title="Crop image" v-if="(imageItem.type == 'image')" @click="image(imageItem.id)"><i class="fa fa-crop"></i></a></li>
</ul>



