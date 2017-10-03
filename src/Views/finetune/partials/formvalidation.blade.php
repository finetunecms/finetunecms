<alert
        :show="errorsShow"
        state="danger"
        dismissible v-cloak>
    <div v-if="errors['message']">
        <h2>@{{ errors['message'] }}</h2>
    </div>
    <ul class="alert-list">
        <li v-for="(index, error) in errors['errors']">
            @{{ error }}
        </li>
    </ul>
</alert>