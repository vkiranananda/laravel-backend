<template>
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-12" v-for="component in data.components[components]">
                    <component :is="component.name" :component="component" :data="data"></component>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['data', 'components']
}
</script>
