<td class="select" @click="selectRow(item)">
    <tooltip
            text="{{ trans('finetune::content.tooltip.select') }}"
            position="top"
            :triggers="['hover']">
        <i :class="renderSelected(item)"  @click="selectRow(item)"></i>
    </tooltip>
</td>