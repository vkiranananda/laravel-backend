<template functional>
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-12" v-for="component in props.data.components[props.components]">
                    <component :is="component.name" :component="component" :data="props.data"></component>
                </div>
            </div>
        </div>
    </div>
</template>
